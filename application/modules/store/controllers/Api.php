<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("store/store_model", "mStoreModel");
        $this->load->library('session');

        $lang = Security::decrypt($this->input->get_request_header('Lang', DEFAULT_LANG));
        Translate::changeSessionLang($lang);

    }


    public function create()
    {

        $user_id = RequestInput::post("user_id");

        if (!GroupAccess::isGrantedUser($user_id, 'store', ADD_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $name = RequestInput::post("name");
        $address = RequestInput::post("address");
        $detail = RequestInput::post("detail");
        $tel = RequestInput::post("tel");
        $category = intval(RequestInput::post("category_id"));
        $lat = doubleval(RequestInput::post("lat"));
        $lng = doubleval(RequestInput::post("lng"));
        $images = RequestInput::post("images");

        $params = array(
            "name" => $name,
            "address" => $address,
            "detail" => $detail,
            "phone" => $tel,
            "user_id" => $user_id,
            "category" => $category,
            "latitude" => $lat,
            "longitude" => $lng,
            "images" => $images,
            //"typeAuth"  => $this->mUserBrowser->getData("typeAuth")
        );

        $data = $this->mStoreModel->createStore($params);

        echo json_encode($data);
        return;
    }


    public function delete()
    {

        $store_id = intval(RequestInput::post("store_id"));
        $user_id = intval(RequestInput::post("user_id"));

        if (!GroupAccess::isGrantedUser($user_id, 'store', DELETE_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        echo json_encode(
            $this->mStoreModel->delete($store_id, $user_id), JSON_FORCE_OBJECT
        );
        return;
    }


    public function changeStatus()
    {

        $status = RequestInput::post("status");
        $user_id = intval(RequestInput::post("user_id"));
        $store_id = intval(RequestInput::post("store_id"));

        $params = array(
            "status" => $status,
            "user_id" => $user_id,
            "store_id" => $store_id,
        );

        $data = $this->mStoreModel->changeStatus($params);

        echo json_encode($data, JSON_FORCE_OBJECT);
        return;

    }

    public function rate()
    {


        $mac_address = RequestInput::post("mac_adr");
        $rate = intval(RequestInput::post("rate"));
        $guest_id = intval(RequestInput::post("guest_id"));
        $user_id = intval(RequestInput::post("user_id"));
        $review = RequestInput::post("review");
        $pseudo = RequestInput::post("pseudo");
        $store_id = intval(RequestInput::post("store_id"));


        $params = array(
            "mac_adr" => $mac_address,
            "store_id" => $store_id,
            "rate" => $rate,
            "guest_id" => $guest_id,
            "user_id" => $user_id,
            'review' => $review,
            'pseudo' => $pseudo
        );


        $data = $this->mStoreModel->rate($params);

        echo json_encode($data);

    }


    public function getStores()
    {

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));
        $order_by = RequestInput::post("order_by");

        $latitude = doubleval(RequestInput::post("latitude"));
        $longitude = doubleval(RequestInput::post("longitude"));
        $radius = RequestInput::post("radius");

        $store_id = intval(RequestInput::post("store_id"));
        $user_id = intval(RequestInput::post("user_id"));
        $category_id = intval(RequestInput::post("category_id"));
        $search = RequestInput::post("search");


        $mac_adr = RequestInput::post("mac_adr");
        $store_ids = Security::decrypt(RequestInput::post("store_ids"));

        $current_date = Security::decrypt(RequestInput::post("current_date"));
        $current_tz = Security::decrypt(RequestInput::post("current_tz"));
        $opening_time = intval(Security::decrypt(RequestInput::post("opening_time")));

        if ($current_date == "") {
            $current_tz = Security::decrypt($this->input->get_request_header('Timezone', "UTC"));
            $current_date = MyDateUtils::convert(
                date("Y-m-d h:i A", time()),
                "UTC",
                TimeZoneManager::getTimeZone(),
                "Y-m-d H:i:s"
            );
        }

        $is_featured = intval(Security::decrypt(RequestInput::post("is_featured")));

        $params = array(
            "user_id" => $user_id,
            "limit" => $limit,
            "page" => $page,
            "category_id" => $category_id,
            "latitude" => $latitude,
            "longitude" => $longitude,
            "store_id" => $store_id,
            "store_ids" => $store_ids,
            "search" => $search,
            "status" => 1,
            "mac_adr" => $mac_adr,
            "order_by" => $order_by,
            "radius" => $radius,

            "current_date" => $current_date,
            "current_tz" => $current_tz,
            "opening_time" => $opening_time,

            "is_featured" => $is_featured,
        );
        
        $data = $this->mStoreModel->getStores($params, array(), function ($_params) {


        });

        if ($data[Tags::SUCCESS] == 1) {

            if(ModulesChecker::isEnabled("cf_manager")){
                foreach ($data[Tags::RESULT] as $key => $store) {
                    $cf = $this->get_cf_by_category($store['category_id']);
                    if ($cf != NULL && $cf[Tags::SUCCESS]==1){
                        $data[Tags::RESULT][$key]['cf'] = $cf;
                        $data[Tags::RESULT][$key]['cf_id'] = $cf[Tags::RESULT][0]['id'];
                    }else{
                        $data[Tags::RESULT][$key]['cf'] = array();
                        $data[Tags::RESULT][$key]['cf_id'] = 0;
                    }

                }
            }


            foreach ($data[Tags::RESULT] as $key => $article) {
                $data[Tags::RESULT][$key]['services'] = $this->mService->loadGroupedServices($article['id_store']);
                $data[Tags::RESULT][$key]['category_name'] = Translate::sprint($data[Tags::RESULT][$key]['category_name'],$data[Tags::RESULT][$key]['category_name']);
            }

            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => $data[Tags::COUNT]));

        } else {

            echo json_encode($data);
        }

    }

    private function get_cf_by_category($category_id)
    {

        $cat = $this->mCategoryModel->getByCategoryByID($category_id);

        if ($cat['cf_id'] > 0)
            return $this->mCFManager->getList0($cat['cf_id']);

        return NULL;
    }

    public function edit()
    {

        $user_id = intval(RequestInput::post("user_id"));

        if (!GroupAccess::isGrantedUser($user_id, 'store', EDIT_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $store_id = intval((RequestInput::post("store_id")));
        $name = RequestInput::post("name");
        $address = RequestInput::post("address");
        $detail = RequestInput::post("detail");
        $tel = RequestInput::post("tel");

        $category = intval(RequestInput::post("category_id"));
        $lat = doubleval(RequestInput::post("lat"));
        $lng = doubleval(RequestInput::post("lng"));
        $images = RequestInput::post("images");


        $params = array(
            "store_id" => $store_id,
            "name" => $name,
            "address" => $address,
            "detail" => $detail,
            "phone" => $tel,
            "user_id" => $user_id,
            "category" => $category,
            "latitude" => $lat,
            "longitude" => $lng,
            "images" => $images,
        );

        $data = $this->mStoreModel->updateStore($params);

        echo json_encode($data);
        return;

    }


    public function getComments()
    {

        $mac_adr = RequestInput::post("mac_adr");
        $mac_adr = RequestInput::post("mac_adr");
        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));
        $store_id = intval(RequestInput::post("store_id"));

        $params = array(
            'mac_adr' => $mac_adr,
            'limit' => $limit,
            'page' => $page,
            'store_id' => $store_id
        );

        $data = $this->mStoreModel->getComments($params);

        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function removeStore()
    {


        $this->requireAuth();

        $user_id = trim(RequestInput::post("user_id"));
        $store_id = Security::decrypt(RequestInput::post("store_id"));

        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $store_id,
            'module' => "store",
        );


        $data = BookmarkManager::remove($params);

        echo json_encode($data);

    }

    public function saveStore()
    {

        $user_id = trim(RequestInput::post("user_id"));
        $store_id = Security::decrypt(RequestInput::post("store_id"));

        $this->load->module('bookmark');

        $params = array(
            'user_id' => $user_id,
            'module_id' => $store_id,
            'module' => "store",
        );

        if ($user_id > 0) {
            $params['guest_id'] = $this->mUserModel->getGuestIDByUserId($user_id);
        }

        if (!BookmarkManager::exist($params))
            $data["first_time"] = 1;
        else {
            $data["first_time"] = 0;
        }

        $data = BookmarkManager::add($params);


        echo json_encode($data);


    }


}
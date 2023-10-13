<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_browser", "mUserBrowser");
        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("store/store_model", "mStoreModel");

    }

    public function saveConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('setting',CHANGE_APP_SETTING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $OPENING_TIME_ENABLED = RequestInput::post("OPENING_TIME_ENABLED");

        ConfigManager::setValue("OPENING_TIME_ENABLED",$OPENING_TIME_ENABLED);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }

    public function cf_categories_edit(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('store',MANAGE_STORES)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $cat_id = intval(RequestInput::post("cat_id"));
        $cf_id = intval(RequestInput::post("cf_id"));

        $this->db->where("id_category",$cat_id);
        $this->db->update("category",array(
            "cf_id"=>intval($cf_id)
        ));

        echo json_encode(array(Tags::SUCCESS=>1));

    }

    public function getStoresAjax2(){

        $params = array(
            "limit"   => RequestInput::get('limit'),
            "search"  => RequestInput::get('search'),
            "page"  => RequestInput::get('page'),
            "status"  => 1
        );


        $data = $this->mStoreModel->getStores($params);

        echo json_encode($data,JSON_OBJECT_AS_ARRAY);return;
    }


    public function getStoresAjax()
    {

        $params = array(
            "limit" => 5,
            "search" => RequestInput::get('search'),
            "status" => 1
        );


        if($this->mUserBrowser->getData("manager") != 1){
            $params["user_id"] = $this->mUserBrowser->getData('id_user');
        }

        if(SessionManager::getData("manager") == 1){
            unset($params['user_id']);
        }

        $data = $this->mStoreModel->getStores($params);


        $result = array();

        if (isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object) {

                $o = array(
                    'text' => Text::output($object['name']),
                    'id' => $object['id_store'],

                    'title' => Text::output($object['name']),
                    'description' => strip_tags(Text::output($object['detail'])),
                    'image' => ImageManagerUtils::getFirstImage($object['images']),
                );

                if (strlen($o['description']) > 100) {
                    $o['description'] = substr(strip_tags(Text::output($o['description'])), 0, 100) . ' ...';
                }

                $result['results'][] = $o;


            }

        echo json_encode($result, JSON_OBJECT_AS_ARRAY);
        return;
    }

    public function markAsFeatured()
    {

        //check if user have permission
        $this->enableDemoMode();


        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if ($this->mUserBrowser->isLogged()) {

            $user_id = $this->mUserBrowser->getData("user_id");

            $id = intval(RequestInput::post("id"));
            $featured = intval(RequestInput::post("featured"));

            echo json_encode(
                $this->mStoreModel->markAsFeatured(array(
                    "user_id" => $user_id,
                    "id" => $id,
                    "featured" => $featured

                ))
            );
            return;

        }

        echo json_encode(array(Tags::SUCCESS => 0));
    }

    public function delete()
    {

        if (!GroupAccess::isGranted('store', DELETE_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $id_store = intval(RequestInput::post("id"));

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            $user_id = $this->mUserBrowser->getData('id_user');
        } else {
            $user_id = 0;
        }

        echo json_encode(
            $this->mStoreModel->delete($id_store, $user_id)
        );
        return;
    }

    public function deleteReview()
    {
        //check if user have permission
        $this->enableDemoMode();
        $id = intval(RequestInput::post("id"));
        echo json_encode(
            $this->mStoreModel->deleteReview($id)
        );
        return;
    }

    public function edit()
    {

        if (!GroupAccess::isGranted('store', EDIT_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $times = RequestInput::post("times");
        $id_store = intval((RequestInput::post("id")));
        $name = RequestInput::post("name");
        $address = RequestInput::post("address");
        $detail = RequestInput::post("detail");
        $tel = RequestInput::post("tel");
        $user = intval(RequestInput::post("id_user"));
        $category = intval(RequestInput::post("cat"));
        $lat = doubleval(RequestInput::post("lat"));
        $lng = doubleval(RequestInput::post("lng"));
        $images = RequestInput::post("images");
        $gallery = RequestInput::post("gallery");
        $canChat = RequestInput::post("canChat");
        $website = RequestInput::post("website");
        $book = RequestInput::post("book");
        $video_url = RequestInput::post("video_url");
        $country = RequestInput::post("country");
        $city = RequestInput::post("city");
        $affiliate_link = RequestInput::post("affiliate_link");


        $params = array(
            "store_id" => $id_store,
            "name" => $name,
            "address" => $address,
            "detail" => $detail,
            "tel" => $tel,
            "user_id" => $this->mUserBrowser->getData("id_user"),
            "category" => $category,
            "latitude" => $lat,
            "longitude" => $lng,
            "images"    => $images,
            "gallery"    => $gallery,
            "times"     => $times,
            "video_url" => $video_url,
            "timezone"  => $this->mUserBrowser->getData("user_timezone"),
            "typeAuth"  => $this->mUserBrowser->getData("typeAuth"),
            "country"  => $country,
            "city"  => $city,
            "canChat" => $canChat,
            "book" => $book,
            "website" => $website,
            "affiliate_link" => $affiliate_link,
        );

        $data = $this->mStoreModel->updateStore($params);

        echo json_encode($data);


    }

    public function createStore()
    {

        if (!GroupAccess::isGranted('store', ADD_STORE)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $times = RequestInput::post("times");
        $name = RequestInput::post("name");
        $address = RequestInput::post("address");
        $detail = RequestInput::post("detail");
        $tel = RequestInput::post("tel");
        $user = Security::decrypt(RequestInput::post("id_user"));
        $category = intval(RequestInput::post("cat"));
        $lat = doubleval(RequestInput::post("lat"));
        $lng = doubleval(RequestInput::post("lng"));
        $images = RequestInput::post("images");
        $gallery = RequestInput::post("gallery");
        $canChat = RequestInput::post("canChat");
        $book = RequestInput::post("book");
        $website = RequestInput::post("website");
        $video_url = RequestInput::post("video_url");
        $country = RequestInput::post("country");
        $city = RequestInput::post("city");
        $affiliate_link = RequestInput::post("affiliate_link");

        $params = array(
            "name" => $name,
            "address" => $address,
            "detail" => $detail,
            "phone" => $tel,
            "video_url" => $video_url,
            "user_id" => $this->mUserBrowser->getData("id_user"),
            "category" => $category,
            "latitude" => $lat,
            "longitude" => $lng,
            "images" => $images,
            "gallery" => $gallery,
            "times" => $times,
            "timezone" => $this->mUserBrowser->getData("user_timezone"),
            "typeAuth" => $this->mUserBrowser->getData("typeAuth"),
            "canChat" => $canChat,
            "book" => $book,
            "website" => $website,
            "country"  => $country,
            "city"  => $city,
            "affiliate_link"  => $affiliate_link,
        );


        $data = $this->mStoreModel->createStore($params);

        echo json_encode($data);
    }


    public function status()
    {
        $this->enableDemoMode();

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if (RequestInput::post("type") == "store") {
            $id = intval(RequestInput::post("id"));
            echo $this->mStoreModel->storeAccess($id);
            return;
        }
    }


}
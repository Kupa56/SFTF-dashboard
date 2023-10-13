<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Offer_model extends CI_Model
{

    private $limit = 10;

    public $button_templates = array(
        'order' => "Order now",
        'book' => "Book now",
        'get' => "Get now",
    );

    function __construct()
    {
        parent::__construct();
        define('MAX_CHARS_OFFERS_DESC', 2000);

        $this->load->model("setting/config_model", 'mConfigModel');
        if (!defined('OFFERS_IN_DATE'))
            $this->mConfigModel->save('OFFERS_IN_DATE', false);

    }


    public function checkExpiredOffers(){

        $date = MyDateUtils::convert(date("Y-m-d H:i:s",time()),"UTC","EST","Y-m-d H:i:s");
        $device_date_to_utc = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d H:i:s");

        $this->db->where("offer.date_end <=", $device_date_to_utc);
        $this->db->where("offer.is_deal", 1);
        $this->db->update("offer",array(
            'status' => 0
        ));

    }


    public function getAllActiveImages(){
        $result = array();
        $this->db->select('images,id_offer');
        $stores = $this->db->get('offer');
        $stores = $stores->result();
        foreach ($stores as $store){
            $images = json_decode($store->images,JSON_OBJECT_AS_ARRAY);
            if(count($images)>0){
                foreach ($images as $image){
                    $result[] = $image;
                }
            }
        }
        return $result;
    }


    public function getUnverifiedStoresCount()
    {

        $this->db->where('verified', 0);
        return $this->db->count_all_results("offer");

    }


    public function campaign_input($args)
    {


        $params = array(
            'limit' => LIMIT_PUSHED_GUESTS_PER_CAMPAIGN,
            'order' => 'last_activity',
        );

        //get store
        $this->db->select("store_id");
        $this->db->where("id_offer", $args['module_id']);
        $this->db->where("user_id", $args['user_id']);
        $obj = $this->db->get($args['module_name'], 1);
        $obj = $obj->result();

        if (count($obj) > 0) {
            $params['__module'] = "store";
            $params['__module_id'] = $obj[0]->store_id;
        }


        //custom parameter for option order by random guest or distance
        if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 1) {//

        } else if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 2) { //get guests by distance


            if (count($obj) > 0) {

                $store_id = $obj[0]->store_id;
                $this->db->select("latitude,longitude");
                $this->db->where("id_store", $store_id);
                $obj = $this->db->get("store", 1);
                $obj = $obj->result();

                if (count($obj) > 0) {
                    $params['lat'] = $obj[0]->latitude;
                    $params['lng'] = $obj[0]->longitude;
                }

            }

        } else if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 3) { //get guest by random and last_activity


        }


        //custom parameter for platforms
        if (isset($args['custom_parameters']['platforms'])
            && !empty($args['custom_parameters']['platforms'])) {

            foreach ($args['custom_parameters']['platforms'] as $key => $value) {
                if ($value == 1) {
                    $params['custom_parameter_platform'][] = $key;
                }
            }

            if (empty($params['custom_parameter_platform']))
                $params['custom_parameter_platform'][] = "unspecified";

        }


        $this->load->model("User/mUserModel");
        $data = $this->mUserModel->getGuests($params, function ($params) {

            if (ModulesChecker::isEnabled("bookmark") && _NOTIFICATION_AGREEMENT_USE == TRUE) {

                $this->db->select('guest_id');


                $this->db->where("module", $params['__module']);
                $this->db->where("module_id", $params['__module_id']);
                $this->db->where('notification_agreement', 1);
                $this->db->where('guest_id !=', "");
                $guests = $this->db->get('bookmarks');
                $guests = $guests->result_array();

                $ids = array(0);

                foreach ($guests as $g) {
                    $ids[] = $g['guest_id'];
                }

                if (!empty($ids))
                    $this->db->where_in('id', $ids);

            }

            if (isset($params['custom_parameter_platform'])
                && !empty($params['custom_parameter_platform'])) {
                $this->db->where_in('platform', $params['custom_parameter_platform']);
            }

        });


        return $data;
    }

    public function campaign_output($campaign = array())
    {

        $type = $campaign['module_name'];
        $module_id = $campaign['module_id'];

        $this->db->where("id_offer", $module_id);
        $this->db->where("status", 1);
        $offer = $this->db->get("offer", 1);
        $offer = $offer->result_array();

        if (count($offer) > 0) {

            $str_id = $offer[0]['store_id'];

            $this->db->where("id_store", $str_id);
            $this->db->where("status", 1);
            $obj = $this->db->get("store", 1);
            $obj = $obj->result_array();

            if (count($obj) > 0) {

                $data['title'] = Text::output($campaign['name']);
                $data['sub-title'] = Text::output($campaign['text']);
                //$data['sub-title'] = Text::output($offer[0]['name']);
                $data['id'] = $module_id;
                $data['type'] = $type;

                $content = json_decode($offer[0]["content"], JSON_OBJECT_AS_ARRAY);
                $content['currency'] = DEFAULT_CURRENCY;
                $content['attachment'] = ImageManagerUtils::getImage($offer[0]['images']);
                $content['store_name'] = $obj[0]['name'];


                $data['body'] = $content;
                $data['image'] = $content['attachment'];

                $imgJson = json_decode($offer[0]['images'], JSON_OBJECT_AS_ARRAY);
                $data['image_id'] = $imgJson[0];

                return $data;
            }

        }


        return NULL;

    }

    public function getDefaultCurrencyCode()
    {
        return DEFAULT_CURRENCY;
    }


    public function getOffersAnalytics($months = array(), $owner_id = 0)
    {

        $analytics = array();

        foreach ($months as $key => $m) {

            $last_month = date("Y-m-t", strtotime($key));
            $start_month = date("Y-m-1", strtotime($key));

            $this->db->where("created_at >=", $start_month);
            $this->db->where("created_at <=", $last_month);


            if ($owner_id > 0)
                $this->db->where('user_id', $owner_id);

            $count = $this->db->count_all_results("offer");

            $analytics['months'][$key] = $count;

        }

        if ($owner_id > 0)
            $this->db->where('user_id', $owner_id);

        $analytics['count'] = $this->db->count_all_results("offer");

        $analytics['count_label'] = Translate::sprint("Offers");
        $analytics['color'] = "#009dff";
        $analytics['icon_tag'] = "<i class=\"mdi mdi-percent-box-outline\"></i>";
        $analytics['label'] = "Offer";

        if ($owner_id == 0)
            $analytics['link'] = admin_url("offer/all_offers");
        else
            $analytics['link'] = admin_url("offer/my_stores");

        return $analytics;

    }


    public function markAsFeatured($params = array())
    {

        extract($params);


        if (!isset($type) and !isset($id) and !isset($featured))
            return array(Tags::SUCCESS => 0);


        $this->db->where("id_offer", $id);
        $this->db->update("offer", array(
            "featured" => intval($featured)
        ));

        return array(Tags::SUCCESS => 1);
    }

    public function switchTo($old_owner = 0, $new_owner = 0)
    {

        if ($new_owner > 0) {

            $this->db->where("id_user", $new_owner);
            $c = $this->db->count_all_results("user");
            if ($c > 0) {

                $this->db->where("user_id", $old_owner);
                $this->db->update("offer", array(
                    "user_id" => $new_owner
                ));

                return TRUE;
            }

        }

        return FALSE;
    }

    public function editOffersCurrency()
    {

        $this->db->select("content,id_offer");
        $offers = $this->db->get("offer");
        $offers = $offers->result_array();

        foreach ($offers as $value) {

            $content = $value['content'];

            if (!is_array($content))
                $content = json_decode($content, JSON_OBJECT_AS_ARRAY);

            print_r($content);

            if (isset($content['currency']['code'])) {

                $currencyObject = $this->getCurrencyByCode($content['currency']['code']);

                $content = json_encode(array(
                    "description" => $content['description'],
                    "price" => $content['price'],
                    "percent" => $content['percent'],
                    "currency" => $currencyObject
                ), JSON_FORCE_OBJECT);


                $this->db->where("id_offer", $value['id_offer']);
                $this->db->update("offer", array(
                    "content" => $content
                ));

            }

        }


    }

    public function getCurrencyByCode($code)
    {

        $currencies = json_decode(CURRENCIES, JSON_OBJECT_AS_ARRAY);

        if (isset($currencies[$code])) {
            return $currencies[$code];
        }

        return $this->getDefaultCurrency();
    }

    public function getDefaultCurrency()
    {

        $currencies = json_decode(CURRENCIES, JSON_OBJECT_AS_ARRAY);
        $d = DEFAULT_CURRENCY;
        foreach ($currencies as $key => $value) {
            if ($key == $d) {
                return $value;
            }
        }

        return;
    }


    public function changeStatus($params = array())
    {

        $errors = array();
        $data = array();
        extract($params);

        if (isset($offer_id) and $offer_id > 0) {

            $this->db->where("id_offer", intval($offer_id));
            $offer = $this->db->get("offer", 1);
            $offer = $offer->result();

            if (count($offer) > 0) {

                $status = $offer[0]->status;

                if ($status == 1) {
                    $this->db->where("id_offer", intval($offer_id));
                    $this->db->update("offer", array(
                        "status" => 0
                    ));
                } else {
                    $this->db->where("id_offer", intval($offer_id));
                    $this->db->update("offer", array(
                        "status" => 1
                    ));
                }

            }

        }

        return array(Tags::SUCCESS => 1);
    }

    public function getMyAllOffers($params = array())
    {

        $errors = array();
        $data = array();

        extract($params);

        if (isset($user_id) and $user_id > 0) {

            $this->db->where("status", 1);
            $this->db->where("user_id", intval($user_id));
            $this->db->order_by("id_offer", "DESC");
            $data = $this->db->get("offer");
            $data = $data->result_array();

            return array(Tags::SUCCESS => 1, Tags::RESULT => $data);
        }

        return array(Tags::SUCCESS => 0);
    }

    public function getOffers($params = array(), $whereArray = array(), $callback = NULL)
    {

        extract($params);
        $errors = array();
        $data = array();


        if (!isset($page)) {
            $page = 1;
        }

        if (!isset($limit)) {
            $limit = NO_OF_ITEMS_PER_PAGE;
        }

        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);


        $this->db->where('store.hidden', 0);


        if (isset($is_featured) and $is_featured == 1) {
            $this->db->where("offer.featured", 1);
        }

        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('offer.name', $search);
            $this->db->or_like('store.name', $search);
            $this->db->or_like('store.address', $search);
            $this->db->or_like('offer.description', $search);
            $this->db->group_end();
        }


        if (isset($store_id) and $store_id > 0) {
            $data ['offer.store_id'] = intval($store_id);
        }

        if (isset($value_type) and $value_type == 'price') {
            $data ['offer.value_type'] = 'price';
        } else if (isset($value_type) and ($value_type == 'percent' or $value_type == 'discount')) {
            $data ['offer.value_type'] = 'percent';
        }


        $custom_where = "";
        if (isset($data ['offer.value_type']) and $data ['offer.value_type'] == "price" && isset($offer_value)) {
            if ($offer_value == "$") {//1-15
                $custom_where = "(offer.offer_value > 1 AND offer.offer_value <= 15)";
            } else if ($offer_value == "$$") {//15-35
                $custom_where = "(offer.offer_value > 15 AND offer.offer_value <= 35)";
            } else if ($offer_value == "$$$") {//35-60
                $custom_where = "(offer.offer_value > 35 AND offer.offer_value <= 60)";
            } else if ($offer_value == "$$$$") {//60+
                $custom_where = "(offer.offer_value > 60)";
            }
        }

        if (isset($data ['offer.value_type']) and $data ['offer.value_type'] == "percent" && isset($offer_value)) {
            if ($offer_value == "0-25%") {//1-15
                $custom_where = "(offer.offer_value > 0 AND offer.offer_value <= 25)";
            } else if ($offer_value == "25-50%") {//15-35
                $custom_where = "(offer.offer_value > 25 AND offer.offer_value <= 50)";
            } else if ($offer_value == "50-75%") {//35-60
                $custom_where = "(offer.offer_value > 50 AND offer.offer_value <= 75)";
            } else if ($offer_value == "75-100%") {//60+
                $custom_where = "(offer.offer_value > 75)";
            }
        }


        if ($custom_where != "") {
            $this->db->where($custom_where, NULL, TRUE);
        }


        if (isset($offer_id) and $offer_id > 0) {
            $data ['offer.id_offer'] = intval($offer_id);
        }


        if (isset($date_end) and $date_end != "" and Text::validateDate($date_end)) {
            $date_end = MyDateUtils::convert($date_end, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");
            $this->db->where("offer.date_end >=", $date_end);
        }


        if (isset($user_id) and $user_id > 0) {
            $this->db->where("offer.user_id", intval($user_id));
        } else if (isset($is_super) and $is_super) {

        } else if (isset($statusM) and !empty($statusM)) {
            $this->db->where("offer.status", $statusM);
        }

        if (isset($status) and !empty($filterBy)) {
            if ($status == 0) {
                $this->db->where("offer.status", $status);
            } else if ($status == 1) {
                $current = date("Y-m-d H:i:s", time());
                $this->db->where("offer.status", $status);
                if ($filterBy == "Published") {
                    $this->db->where("offer.date_start > ", $current);
                } else if ($filterBy == "Started") {
                    $this->db->where("offer.date_start < ", $current);
                    $this->db->where("offer.date_end > ", $current);
                } else if ($filterBy == "Finished") {
                    $this->db->where("offer.date_end > ", $current);
                }
            }
        }else if(isset($status) && $status>0){
            $this->db->where("offer.status", $status);
        }


        //distance
        $calcul_distance = "";
        if (
            isset($longitude)
            and
            isset($latitude)

        ) {

            $longitude = doubleval($longitude);
            $latitude = doubleval($latitude);


            $calcul_distance = " , IF( store.latitude = 0,99999,  (1000 * ( 6371 * acos (
                              cos ( radians(" . $latitude . ") )
                              * cos( radians( store.latitude ) )
                              * cos( radians( store.longitude ) - radians(" . $longitude . ") )
                              + sin ( radians(" . $latitude . ") )
                              * sin( radians( store.latitude ) )
                            )
                          ) ) ) as 'distance'  ";


        }


        if (isset($category_id) and $category_id > 0) {
            $this->db->where("store.category_id", $category_id);
        }


        $this->db->where($data);
        $this->db->join("store", "offer.store_id=store.id_store");

        $count = $this->db->count_all_results("offer");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if ($count == 0)
            return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => array());


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        $this->db->where('store.hidden', 0);

        if (isset($is_featured) and $is_featured == 1) {
            $this->db->where("offer.featured", 1);
        }

        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('offer.name', $search);
            $this->db->or_like('store.name', $search);
            $this->db->or_like('store.address', $search);
            $this->db->or_like('offer.description', $search);
            $this->db->group_end();
        }


        if (isset($store_id) and $store_id > 0) {
            $data ['offer.store_id'] = intval($store_id);
        }

        if (isset($value_type) and $value_type == 'price') {
            $data ['offer.value_type'] = 'price';
        } else if (isset($value_type) and ($value_type == 'percent' or $value_type == 'discount')) {
            $data ['offer.value_type'] = 'percent';
        }

        if (isset($value_type) and $value_type != 0) {
            $data ['offer.value_type'] = doubleval($value_type);
        }

        if (isset($offer_id) and $offer_id > 0) {
            $data ['offer.id_offer'] = intval($offer_id);
        }

        if (isset($date_end) and $date_end != "" and Text::validateDate($date_end)) {
            $date_end = MyDateUtils::convert($date_end, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");

            $this->db->where("offer.date_end >=", $date_end);
        }

        if (isset($user_id) and $user_id > 0) {

            $this->db->where("offer.user_id", intval($user_id));

        } else if (isset($is_super) and $is_super) {

        } else if (isset($statusM) and !empty($statusM)) {
            $this->db->where("offer.status", $statusM);
        }

        // filter offers by status
        if (isset($status) and !empty($filterBy)) {
            if ($status == 0) {
                $this->db->where("offer.status", $status);
            } else if ($status == 1) {
                $current = date("Y-m-d H:i:s", time());
                //$current = MyDateUtils::convert($current, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d");
                $this->db->where("offer.status", $status);
                if ($filterBy == "Published") {
                    $this->db->where("offer.date_start > ", $current);
                } else if ($filterBy == "Started") {
                    $this->db->where("offer.date_start < ", $current);
                    $this->db->where("offer.date_end > ", $current);
                } else if ($filterBy == "Finished") {
                    $this->db->where("offer.date_end < ", $current);
                }
            }
        }else if(isset($status) && $status>0){
            $this->db->where("offer.status", $status);
        }

        if (isset($category_id) and $category_id > 0) {
            $this->db->where("store.category_id", $category_id);
        }

        if ($custom_where != "") {
            $this->db->where($custom_where, NULL, TRUE);
        }

        $this->db->join("store", "store.id_store=offer.store_id");
        $this->db->join("category", "category.id_category=store.category_id");
        $this->db->select("offer.*,store.latitude,store.longitude,store.address as address , store.name as 'store_name', category.name as 'category_name', category.color as 'category_color'" . $calcul_distance, FALSE);


        $this->db->where($data);
        $this->db->from("offer");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());


        if (isset ($order_by) and $order_by == "recent") {
            $this->db->order_by("offer.id_offer", "DESC");
        } else if ($calcul_distance != "" && isset ($order_by) && $order_by == "nearby") {
            $this->db->order_by("distance", "ASC");
        } else {
            $this->db->order_by("offer.id_offer", "DESC");
        }

        if (isset($radius) and $radius > 0 && $calcul_distance != "")
            $this->db->having('distance <= ' . intval($radius), NULL, FALSE);

        $offers = $this->db->get();
        $offers = $offers->result_array();


        if (count($offers) < $limit) {
            $count = count($offers);
        }

        foreach ($offers as $key => $offer) {

            //coupon
            $offers[$key]['hasGotCoupon'] = $this->hasGotCoupon($offer['id_offer']);

            if ($this->isSaved("offer", $offer['id_offer']))
                $offers[$key]['saved'] = 1;
            else
                $offers[$key]['saved'] = 0;


            if ($offer['order_enabled'] > 0 && $offer['cf_id'] > 0)
                $offers[$key]['cf'] = $this->mCFManager->getList0($offer['cf_id']);
            else
                $offers[$key]['cf'] = array();


            $offers[$key]['link'] = site_url("offer/id/" . $offer["id_offer"]);
            $offers[$key]['short_description'] = strip_tags(Text::output(Text::output($offer['description'])));

            if (isset($offer['images'])) {

                $images = (array)json_decode($offer['images']);

                $offers[$key]['images'] = array();
                // $new_stores_results[$key]['image'] = $store['images'];
                foreach ($images as $k => $v) {
                    $offers[$key]['images'][] = _openDir($v);
                }

            } else {
                $offers[$key]['images'] = array();
            }


            //parse amount + currency
            $offers[$key]['currency'] = $this->mCurrencyModel->getCurrency($offer['currency']);


        }


        if ($calcul_distance != "" && $order_by == "nearby") {
            $offers = $this->re_order_featured_item($offers);
        }


        $object = ActionsManager::return_action("offer", "func_getOffers", $offers);
        if ($object != NULL)
            $offers = $object;


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $offers);
    }

    private function hasGotCoupon($offer_id)
    {

        $user_id = Security::decrypt($this->input->get_request_header('Session-User-Id', 0));

        if($user_id == 0 && ClientSession::isLogged()){
            $user_id = ClientSession::getData("id_user");
        }

        return $this->mQrcouponModel->hasGotCoupon($offer_id,$user_id);

    }

        private function isSaved($module, $module_id)
    {

        $user_id = Security::decrypt($this->input->get_request_header('Session-User-Id', 0));

        if($user_id == 0 && ClientSession::isLogged()){
            $user_id = ClientSession::getData("id_user");
        }

        $this->db->where("module", "offer");
        $this->db->where("module_id", $module_id);
        $this->db->where("user_id",$user_id);
        $c = $this->db->count_all_results("bookmarks");

        if ($c > 0)
            return TRUE;

        return FALSE;
    }


    public function re_order_featured_item($data = array())
    {

        $new_data = array();

        foreach ($data as $key => $value) {
            if ($value['featured'] == 1) {
                $new_data[] = $data[$key];
                unset($data[$key]);
            }
        }


        foreach ($data as $value) {
            $new_data[] = $value;
        }

        /* usort($data,function($first, $second){
             return strtolower($first['featured']) < strtolower($second['featured']);
         });*/


        return $new_data;
    }


    public function addOffer($params = array())
    {

        extract($params);


        $errors = array();
        $data = array();


        /*
         *  MANAGE OFFER IMAGES
         */
        if (isset($images) and !is_array($images))
            $images = json_decode($images, JSON_OBJECT_AS_ARRAY);

        if (!empty($images)) {
            $data["images"] = array();
            $i = 0;
            try {
                if (!empty($images)) {
                    foreach ($images as $value) {
                        $data["images"][$i] = $value;
                        $i++;
                    }
                    $data["images"] = json_encode($data["images"], JSON_FORCE_OBJECT);
                }
            } catch (Exception $e) {

            }

        }

        if (isset($data["images"]) and empty($data["images"])) {
            $errors['images'] = Translate::sprint("Please upload an image");
        }

        if (isset($store_id) and $store_id > 0) {
            $data['store_id'] = intval($store_id);
        } else {
            $errors['store_id'] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);
        }


        if (isset($name) and $name != "") {
            $data['name'] = Text::input($name);
        } else {
            $errors['name'] = Translate::sprint(Messages::OFFER_NAME_MISSING);
        }

        if (isset($description) and $description != "") {
            $data['description'] = Text::inputWithoutStripTags($description);
        } else {
            $errors['description'] = Translate::sprint(Messages::EVENT_DESCRIPTION_EMPTY);
        }

        if (isset($price) and doubleval($price) > 0) {

            $data['offer_value'] = doubleval($price);
            $data['value_type'] = 'price';

            if (isset($currency) and $currency != "" and preg_match('#([a-zA-Z])#', $currency)) {
                $data['currency'] = $currency;
            } else {
                $data['currency'] = DEFAULT_CURRENCY;
            }

        } else if (isset($percent) and (intval($percent) > 0 || intval($percent) < 0)) {
            $data['offer_value'] = doubleval($percent);
            $data['value_type'] = 'percent';
        } else {
            //Create an offer with a non specified value type : e.g promo , free offre ...etc
            $data['value_type'] = 'unspecified';
            //$errors['price'] = Translate::sprint(Messages::OFFER_VALUE_EMPTY);
        }

        if (isset($currency) and $currency != "" and preg_match('#([a-zA-Z])#', $currency)) {
            $data['currency'] = $currency;
        } else {
            $data['currency'] = DEFAULT_CURRENCY;
        }

        if (isset($is_deal) && $is_deal == 1) {
            if (isset($date_start) and Text::validateDate($date_start)) {
                $data['date_start'] = $date_start;//." ".$currentTime;
            } else {
                $errors['date_start'] = Translate::sprint(Messages::DATE_BEGIN_NOT_VALID);
            }

            if (isset($date_end) and Text::validateDate($date_end)) {
                $data['date_end'] = $date_end;
            } else {
                $errors['date_end'] = Text::_print("Date of end is not valid!");
            }

            $data['is_deal'] = intval($is_deal);
        } else {
            $data['date_end'] = date('Y-m-d H:i:s', PHP_INT_MAX); // the highest integer number it can represent in the PHP_INT_MAX constant
        }


        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = $user_id;
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_FOUND);
        }


        /*
         * Coupon config
         */

        if (isset($offer_coupon_config_type)
            && $offer_coupon_config_type == Qrcoupon::COUPON_UNLIMITED
            && isset($offer_coupon_code)) {

            if(!coupon::check($offer_coupon_code,6)){
                $errors['coupon_code'] = _lang("Coupon code is not valid, maximum 6 characters");
            }


            $data['coupon_config'] = Qrcoupon::COUPON_UNLIMITED;
            $data['coupon_redeem_limit'] = -1;
            $data['coupon_code'] = $offer_coupon_code;

        }else if (isset($offer_coupon_config_type)
            && $offer_coupon_config_type == Qrcoupon::COUPON_LIMITED
            && isset($offer_coupon_config_limit)
            && isset($offer_coupon_code)) {


            if(!coupon::check($offer_coupon_code,6)){
                $errors['coupon_code'] = _lang("Coupon code is not valid, maximum 6 characters");
            }

            $data['coupon_config'] = Qrcoupon::COUPON_LIMITED;
            $data['coupon_redeem_limit'] = intval($offer_coupon_config_limit);
            $data['coupon_code'] = $offer_coupon_code;

        }else if (isset($offer_coupon_config_type)
            && $offer_coupon_config_type == Qrcoupon::COUPON_DISABLED) {

            $data['coupon_config'] = Qrcoupon::COUPON_DISABLED;
            $data['coupon_redeem_limit'] = 0;

        }

        /*
       * End Coupon config
       */


        //Set the date created as current date
        $data['date_created'] = date("Y-m-d H:i:s", time());


        if (!isset($user_type) or (isset($user_type) and $user_type == "manager")) {

            if (empty($errors) and $store_id > 0) {

                $this->db->where("user_id", $user_id);
                $this->db->where("id_store", $store_id);
                $this->db->where("status", 1);
                $store = $this->db->get("store", 1);
                $store = $store->result_array();
                if (count($store) == 0) {
                    $errors['store'] = Translate::sprint(Messages::USER_NOT_FOUND);;
                }

            }

        }


        if (empty($errors) && isset($order_enabled) && $order_enabled == 1 && isset($user_id)) {

            if ($data['value_type'] != "price" && $data['offer_value'] > 0) {
                $errors['err'] = _lang("You couldn't enable order in this offer, the offer should has a specific price");
                return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
            }

            if (GroupAccess::isGrantedUser($user_id, "cf_manager")) {
                if (isset($order_cf_id) && $order_cf_id > 0) {
                    $data['order_enabled'] = 1;
                    $data['cf_id'] = intval($order_cf_id);
                } else {
                    $errors['cf'] = Text::_print("Please select a custom fields");
                }
            } else {//get from category

                $this->db->select("category_id,name");
                $this->db->where("id_store", $store_id);
                $this->db->where("status", 1);
                $store = $this->db->get("store", 1);
                $store = $store->result_array();

                if (count($store) > 0) {
                    $cat = $this->mStoreModel->getCategory($store[0]['category_id']);
                    if ($cat['cf_id'] > 0) {
                        $data['order_enabled'] = 1;
                        $data['cf_id'] = $cat['cf_id'];
                    } else {
                        $errors['cf'] = Translate::sprintf("This store (%s) unable to use order system, the reason is there is no custom fields linked with store's category", array($store[0]['name']));
                    }
                } else
                    $errors['store_id'] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);

            }


            if (empty($errors) && isset($button_template)) {

                if (isset($this->button_templates[$button_template])) {
                    $data['order_button'] = $button_template;
                } else {
                    $data['order_button'] = "order";
                }

            }

        }


        if (empty($errors) and isset($user_id) and $user_id > 0) {

            $nbr_offers_monthly = UserSettingSubscribe::getUDBSetting($user_id, KS_NBR_OFFERS_MONTHLY);

            if ($nbr_offers_monthly > 0 || $nbr_offers_monthly == -1) {

                if(ConfigManager::getValue("ANYTHINGS_APPROVAL")){
                    $data['status'] = 0;
                }else{
                    $data['status'] = 1;
                }

                $date = date("Y-m-d H:i:s", time());
                $data['created_at'] = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC");

                $this->db->insert("offer", $data);
                $offer_id = $this->db->insert_id();

                if ($nbr_offers_monthly > 0) {
                    $nbr_offers_monthly--;
                    UserSettingSubscribe::refreshUSetting($user_id, KS_NBR_OFFERS_MONTHLY, $nbr_offers_monthly);
                }


                //manage uri & seo
                $this->createURI($offer_id,$data['name']);


                //send insert action
                ActionsManager::add_action("offer", "onAdd", array("id" => $offer_id));


                return array(Tags::SUCCESS => 1);

            } else {
                $errors["offers"] = Translate::sprint(Messages::EXCEEDED_MAX_NBR_STORES);
            }

        } else {
            $errors['store'] = Text::_print("Error!");
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }

    private function createURI($offer_id,$title){

        $module = FModuleLoader::getModuleDetail("cms");
        if (version_compare($module['version_name'], '2.0.1') >= 0) {
            $slug = CMSUtils::createSlug("$offer_id-".$title,"-");
            $uri = "detail-offer/".$offer_id;
            CMSUtils::addNewSlug($slug,$uri);
        }
    }

    public function editOffer($params = array())
    {

        extract($params);


        $errors = array();
        $data = array();


        /*
        *  MANAGE OFFER IMAGES
        */

        if (isset($images) and !is_array($images))
            $images = json_decode($images, JSON_OBJECT_AS_ARRAY);

        if (!empty($images)) {
            $data["images"] = array();
            $i = 0;
            try {
                if (!empty($images)) {
                    foreach ($images as $value) {
                        $data["images"][$i] = $value;
                        $i++;
                    }
                    $data["images"] = json_encode($data["images"], JSON_FORCE_OBJECT);
                }
            } catch (Exception $e) {

            }
        } else {
            $data["images"] = json_decode("", JSON_OBJECT_AS_ARRAY);
        }

        if (isset($data["images"]) and empty($data["images"])) {
            $errors['images'] = Translate::sprint("Please upload an image");
        }

        if (isset($name) and $name != "") {
            $data['name'] = Text::input($name);
        } else {
            //$errors['store'] = Text::_print("Store id is messing");
        }

        if (isset($store_id) and $store_id > 0) {
            $data['store_id'] = intval($store_id);
        } else {
            $errors['store'] = Translate::sprint("Please_select_store", "Please select store");
        }

        if (isset($offer_id) and $offer_id > 0) {
            $data['id_offer'] = intval($offer_id);
        } else {
            $errors['id_offer'] = Translate::sprint(Messages::OFFER_ID_MISSING);
        }

        if (isset($description) and $description != "") {
            $data['description'] = Text::inputWithoutStripTags($description);
        } else {
            $errors['description'] = Translate::sprint(Messages::EVENT_DESCRIPTION_EMPTY);
        }


        if (isset($price) and doubleval($price) > 0) {
            $data['offer_value'] = doubleval($price);
            $data['value_type'] = 'price';
            if (isset($currency) and $currency != "" and preg_match('#([a-zA-Z])#', $currency)) {
                $data['currency'] = $currency;
            } else {
                $data['currency'] = DEFAULT_CURRENCY;
            }
        } else if (isset($percent) and (intval($percent) > 0 || intval($percent) < 0)) {
            $data['offer_value'] = doubleval($percent);
            $data['value_type'] = 'percent';
        } else {
            $data['value_type'] = 'unspecified';
        }

        if (isset($is_deal) && $is_deal == 1) {

            if (isset($date_start) and Text::validateDate($date_start)) {
                $data['date_start'] = $date_start;
            } else {
                $errors['date_start'] = Translate::sprint(Messages::DATE_BEGIN_NOT_VALID);
            }

            if (isset($date_end) and Text::validateDate($date_end)) {
                $data['date_end'] = $date_end;
            } else {
                $errors['date_end'] = Text::_print("Date of end is not valid!");
            }

            $data['is_deal'] = intval($is_deal);
        }


        if (isset($user_id) and intval($user_id) > 0) {
            $data['user_id'] = $user_id;
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_FOUND);
        }


        /*
         * Coupon config
         */

        if (isset($offer_coupon_config_type)
            && $offer_coupon_config_type == Qrcoupon::COUPON_UNLIMITED
            && isset($offer_coupon_code)) {

            if(!coupon::check($offer_coupon_code,6)){
                $errors['coupon_code'] = _lang("Coupon code is not valid, maximum 6 characters");
            }

            $data['coupon_config'] = Qrcoupon::COUPON_UNLIMITED;
            $data['coupon_redeem_limit'] = -1;
            $data['coupon_code'] = $offer_coupon_code;

        }else if (isset($offer_coupon_config_type)
            && $offer_coupon_config_type == Qrcoupon::COUPON_LIMITED
            && isset($offer_coupon_config_limit)
            && isset($offer_coupon_code)) {

            if(!coupon::check($offer_coupon_code,6)){
                $errors['coupon_code'] = _lang("Coupon code is not valid, maximum 6 characters");
            }


            $data['coupon_config'] = Qrcoupon::COUPON_LIMITED;
            $data['coupon_redeem_limit'] = intval($offer_coupon_config_limit);
            $data['coupon_code'] = $offer_coupon_code;

        }else if (isset($offer_coupon_config_type)
            && $offer_coupon_config_type == Qrcoupon::COUPON_DISABLED) {

            $data['coupon_config'] = Qrcoupon::COUPON_DISABLED;
            $data['coupon_redeem_limit'] = 0;

        }

        /*
       * End Coupon config
       */

        if (empty($errors) and $store_id > 0) {

            $this->db->where("user_id", $user_id);
            $this->db->where("id_store", $store_id);
            $this->db->where("status", 1);
            $c = $this->db->count_all_results("store");
            if ($c == 0) {
                $errors['store'] = Translate::sprint(Messages::STORE_ID_NOT_VALID);
            }

        }


        if (empty($errors) && isset($order_enabled) && $order_enabled == 1 && isset($user_id)) {

            if (GroupAccess::isGrantedUser($user_id, "cf_manager")) {
                if (isset($order_cf_id) && $order_cf_id > 0) {
                    $data['order_enabled'] = 1;
                    $data['cf_id'] = intval($order_cf_id);
                } else {
                    $errors['cf'] = Text::_print("Please select a custom fields");
                }
            } else {//get from category

                $this->db->select("category_id,name");
                $this->db->where("id_store", $store_id);
                $this->db->where("status", 1);
                $store = $this->db->get("store", 1);
                $store = $store->result_array();

                if (count($store) > 0) {
                    $cat = $this->mStoreModel->getCategory($store[0]['category_id']);
                    if ($cat['cf_id'] > 0) {
                        $data['order_enabled'] = 1;
                        $data['cf_id'] = $cat['cf_id'];
                    } else {
                        $errors['cf'] = Translate::sprintf("This store (%s) unable to use order system, the reason is there is no custom fields linked with store's category", array($store[0]['name']));
                    }
                } else
                    $errors['store_id'] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);

            }


            if (empty($errors) && isset($button_template)) {

                if (isset($this->button_templates[$button_template])) {
                    $data['order_button'] = $button_template;
                } else {
                    $data['order_button'] = "order";
                }

            }

        }


        if (empty($errors) and isset($user_id) and $user_id > 0) {

            $date = date("Y-m-d H:i:s", time());
            $data['updated_at'] = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC");

            //$data['status'] = 1;
            $this->db->where("id_offer", $offer_id);
            $this->db->where("user_id", $user_id);
            $this->db->update("offer", $data);

            //send insert action
            ActionsManager::add_action("offer", "onUpdate", array("id" => $offer_id));

            return array(Tags::SUCCESS => 1);

        } else {
            $errors['store'] = Text::_print("Error! ");
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    public function deleteOffer($params = array())
    {

        extract($params);
        $errors = array();
        $data = array();

        $user_id = $this->mUserBrowser->getData("id_user");

        if (isset($offer_id) and $offer_id > 0 && $user_id > 0) {


            $this->db->where("id_offer", $offer_id);
            $offers = $this->db->get("offer");
            $offerToDelete = $offers->result();

            //Delete all images from this offers
            if (isset($offerToDelete[0]->images)) {
                $images = (array)json_decode($offerToDelete[0]->images);
                foreach ($images as $k => $v) {
                    _removeDir($v);
                }
            }

            $this->db->where("id_offer", $offer_id);
            $this->db->delete("offer");

            //send insert action
            ActionsManager::add_action("offer", "onDelete", array("id" => $offer_id));


            return array(Tags::SUCCESS => 1);

        }

        return array(Tags::SUCCESS => 0);
    }


    function hiddenOfferOutOfDate()
    {
        $this->db->select("date_end,id_offer");
        $this->db->where("status", 1);
        $offers = $this->db->get("offer", 1);
        $offers = $offers->result_array();
        if (count($offers) > 0) {
            $currentDate = date("Y-m-d H:i:s", time());
            $currentDate = MyDateUtils::convert($currentDate, TimeZoneManager::getTimeZone(), "UTC", "Y-m-d H:i:s");
            foreach ($offers as $value) {
                if (strtotime($value["date_end"]) < strtotime($currentDate)) {
                    $this->db->where("id_offer", $value["id_offer"]);
                    $this->db->update("offer", array(
                        "status" => 0));
                }
            }
            return array(Tags::SUCCESS => 1);
        } else {
            return array(Tags::SUCCESS => 0);
        }
    }



    public function addFields16()
    {


        if (!$this->db->field_exists('images', 'offer')) {
            $fields = array(
                'images' => array('type' => 'TEXT', 'after' => 'image', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('description', 'offer')) {
            $fields = array(
                'description' => array('type' => 'TEXT', 'after' => 'images', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('value_type', 'offer')) {
            $fields = array(
                'value_type' => array('type' => 'VARCHAR(10)', 'after' => 'description', 'default' => 'percent'),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('offer_value', 'offer')) {
            $fields = array(
                'offer_value' => array('type' => 'DOUBLE', 'after' => 'value_type', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('currency', 'offer')) {
            $fields = array(
                'currency' => array('type' => 'VARCHAR(30)', 'after' => 'offer_value', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

    }


    public function updateFields()
    {


        if (!$this->db->field_exists('verified', 'offer')) {
            $fields = array(
                'verified' => array('type' => 'INT', 'after' => 'tags', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }


        if (!$this->db->field_exists('created_at', 'offer')) {
            $fields = array(
                'created_at' => array('type' => 'DATETIME', 'after' => 'date_end', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('updated_at', 'offer')) {
            $fields = array(
                'updated_at' => array('type' => 'DATETIME', 'after' => 'created_at', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('is_deal', 'offer')) {
            $fields = array(
                'is_deal' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }


        if (!$this->db->field_exists('order_enabled', 'offer')) {
            $fields = array(
                'order_enabled' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }

        if (!$this->db->field_exists('cf_id', 'offer')) {
            $fields = array(
                'cf_id' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }


        if (!$this->db->field_exists('order_button', 'offer')) {
            $fields = array(
                'order_button' => array('type' => 'TEXT', 'default' => ""),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }


        if (!$this->db->field_exists('payment_enabled', 'offer')) {
            $fields = array(
                'payment_enabled' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('offer', $fields);
        }


        $sql = "ALTER TABLE `offer` CHANGE `name` `name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($sql);

    }


}
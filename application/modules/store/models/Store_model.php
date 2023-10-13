<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Store_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function disableActiveStores($user_id){

        $this->db->where('user_id',$user_id);
        $this->db->update('store',array(
            'status' => 0
        ));

    }

    public function getCity($id){

        $this->db->where("id_city",$id);
        $cities = $this->db->get("city",1);
        $cities = $cities->result_array();

        if(isset($cities[0]))
            return $cities[0];

        return NULL;

    }

    public function getNearbyCity($lat,$lng){

        $longitude = doubleval($lng);
        $latitude = doubleval($lat);

        $calcul_distance = " , IF( city.latitude = 0,99999,  (1000 * ( 6371 * acos (
                              cos ( radians(" . $latitude . ") )
                              * cos( radians( city.latitude ) )
                              * cos( radians( city.longitude ) - radians(" . $longitude . ") )
                              + sin ( radians(" . $latitude . ") )
                              * sin( radians( city.latitude ) )
                            )
                          ) ) ) as 'distance'  ";

        $this->db->select("city.*" . $calcul_distance, FALSE);
        $this->db->order_by("distance", "ASC");
        $this->db->from("city");
        $this->db->limit(5);
        $cities = $this->db->get();
        $cities = $cities->result_array();

        if(isset($cities[0]))
            return $cities[0];

        return NULL;
    }

    public function getAllActiveImages($store_id){
        $result = array();
        $this->db->select('images,id_store');
        $this->db->where('id_store',$store_id);
        $stores = $this->db->get('store');
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

    public function contact_store($params=array()){

        $data = array();
        $errors = array();

        if(isset($params['store_id'])
            && $store =  $this->storeExists($params['store_id'])){

            if($store != NULL){
                $data['storeData'] = $store;
            }else{
                $errors[] = _lang("Store is not exists");
            }

        }else{
            $errors[] = _lang("Store is not valid");
        }

        if(isset($params['client_id']) && $params['client_id'] > 0){
            $data['client_id'] = $params['client_id'];
        }else{
            $errors[] = _lang("client ID is not valid");
        }

        if(isset($params['email']) && Text::checkEmailFields($params['email'])){
            $data['email'] = $params['email'];
        }else{
            $errors[] = _lang("Email is not valid");
        }

        if(isset($params['full_name']) && Text::checkNameCompleteFields($params['full_name'])){
            $data['full_name'] = $params['full_name'];
        }else{
            $errors[] = _lang("full_name is not valid");
        }

        if(isset($params['telephone']) && $params['telephone'] !=""){
            if(Text::checkPhoneFields($params['telephone'])){
                $data['telephone'] = $params['telephone'];
            }else
                $errors[] = _lang("Telephone is not valid");
        }else{
            $errors[] = _lang("Telephone is empty");
        }

        if(isset($params['message']) && $params['message'] !=""){
            $data['message'] = $params['message'];
        }else{
            $errors[] = _lang("Message is empty");
        }

        if(empty($errors)){
            $data['clientData'] = $this->mUserModel->getUserData($data['client_id']);
            if(empty($data['clientData']))
                $errors[] = _lang("Client is not exists!");
        }

        if(empty($errors)){

            //parse message1
            $message1 = "You have received new message from \"%s\"";
            $message1 = Translate::sprintf($message1,array(
                $data['storeData']['name']
            ));

            //send first message

            //parse message2
            $message2 = "Full_Name: <b>%s</b>\nEmail: <b>%s</b>\nTelephone: <b>%s</b>\nMessage:\n %s";
            $message2 = Translate::sprintf($message2,array(
                $data['full_name'],
                $data['email'],
                $data['telephone'],
                $data['message'],
            ));

            //send inbox

            if(ModulesChecker::isEnabled("messenger")){
                $this->mMessengerModel->sendMessage(array(
                    "sender_id" => $data['client_id'],
                    "receiver_id" => $data['storeData']['user_id'],
                    "discussion_id" => 0,
                    "content" => Text::input($message1)
                ));
                $this->mMessengerModel->sendMessage(array(
                    "sender_id" => $data['client_id'],
                    "receiver_id" => $data['storeData']['user_id'],
                    "discussion_id" => 0,
                    "content" => Text::input($message2)
                ));
            }

            //send mail
            $ownerData = $this->mUserModel->getUserData( $data['storeData']['user_id']);
            $this->sendMail(array(
                "recipientName" => $ownerData['name'],
                "recipientEmail" => $ownerData['email'],
                "subject" => $message1,
                "body" => $message1."\n\n".$message2,
            ));


            $object = array(
                'client' =>  $data['clientData'],
                'store' =>  $data['storeData'],
                'owner' =>  $ownerData,
                'message1' =>  $message1,
                'message2' =>  $message2,
            );

            return array(Tags::SUCCESS=>1,Tags::RESULT=>$object);
        }


        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    private function sendMail($params=array()){

        $appLogo = _openDir(ConfigManager::getValue('APP_LOGO'));
        $imageUrl = "";
        if (!empty($appLogo)) {
            $imageUrl = $appLogo['200_200']['url'];
        }

        $body = nl2br($params['body']);

        $messageText = Text::textParserHTML(array(
            "name" => $params['recipientName'],
            "imageUrl" => $imageUrl,
            "email" => ConfigManager::getValue("DEFAULT_EMAIL"),
            "appName" => ConfigManager::getValue("APP_NAME"),
            "body" => $body,
        ), $this->load->view("mailing/templates/default.html",NULL,TRUE));



        $mail = new DTMailer();
        $mail->setRecipient($params['recipientEmail']);
        $mail->setFrom(ConfigManager::getValue("DEFAULT_EMAIL"));
        $mail->setFrom_name(ConfigManager::getValue("APP_NAME"));
        $mail->setMessage($messageText);
        $mail->setReplay_to(ConfigManager::getValue("DEFAULT_EMAIL"));
        $mail->setReplay_to_name(ConfigManager::getValue("APP_NAME"));
        $mail->setType("html");
        $mail->setSubject($params['subject']);
        if(!$mail->send()){
            return TRUE;
        }


        $mail = new DTMailer();
        $mail->setRecipient(ConfigManager::getValue("DEFAULT_EMAIL"));
        $mail->setFrom(ConfigManager::getValue("DEFAULT_EMAIL"));
        $mail->setFrom_name(ConfigManager::getValue("APP_NAME"));
        $mail->setMessage($messageText);
        $mail->setReplay_to(ConfigManager::getValue("DEFAULT_EMAIL"));
        $mail->setReplay_to_name(ConfigManager::getValue("APP_NAME"));
        $mail->setType("html");
        $mail->setSubject(trans("Copy")." - ".$params['subject']);
        if($mail->send()){
            return TRUE;
        }

    }


    public function storeExists($store_id){

        $this->db->where('id_store',$store_id);
        $count = $this->db->count_all_results('store');

        if($count == 0)
            return NULL;

        $this->db->where('id_store',$store_id);
        $store = $this->db->get('store',1);
        $store = $store->result_array();

        if(isset($store[0]))
            return $store[0];

        return NULL;
    }

    public function create_default_checkout_fields()
    {

        $pdc_cf = ConfigManager::getValue("store_default_checkout_cf");
        $pdc_cf = intval($pdc_cf);
        if ($pdc_cf == 0) {

            $fields = array(
                0 => array(
                    "type" => "input.text",
                    "label" => "Full name",
                    "required" => 1,
                    "order" => 1,
                    "step" => 1,
                ),
                1 => array(
                    "type" => "input.phone",
                    "label" => "Phone",
                    "required" => 1,
                    "order" => 2,
                    "step" => 1,
                ),
                3 => array(
                    "type" => "input.date",
                    "label" => "Reservation date",
                    "required" => 1,
                    "order" => 4,
                    "step" => 1,
                ),

                4 => array(
                    "type" => "input.time",
                    "label" => "Reservation time",
                    "required" => 1,
                    "order" => 5,
                    "step" => 1,
                ),
            );

            $label = "Default_Reservation_Checkout_fields";

            $result = $this->mCFManager->createCustomFields(array(
                "fields" => $fields,
                "label" => $label,
                "user_id" => SessionManager::getData("id_user"),
            ));

            if ($result[Tags::SUCCESS] == 1) {

                $id = $this->db->insert_id();

                $this->db->where("id", $id);
                $this->db->update("cf_list", array(
                    'editable' => 1
                ));

                $this->db->where("cf_id", 0);
                $this->db->update("category", array(
                    'cf_id' => $id
                ));


                return $id;
            }


        }

        return 0;
    }


    public function campaign_input($args)
    {


        $params = array(
            'limit' => LIMIT_PUSHED_GUESTS_PER_CAMPAIGN,
            'module' => $args['module_name'],
            'order' => "last_activity",
            'module_id' => $args['module_id']
        );

        //custom parameter for option order by random guest or distance
        if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 1) {//

        } else if (isset($args['custom_parameters']['getting_option'])
            && $args['custom_parameters']['getting_option'] == 2) { //get guests by distance

            //get position from modules
            $this->db->select("latitude,longitude");
            $this->db->where("id_store", $args['module_id']);
            $this->db->where("user_id", $args['user_id']);
            $obj = $this->db->get($args['module_name'], 1);
            $obj = $obj->result();

            if (count($obj) > 0) {
                $params['lat'] = $obj[0]->latitude;
                $params['lng'] = $obj[0]->longitude;
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

            if (ModulesChecker::isEnabled("bookmark") && _NOTIFICATION_AGREEMENT_USE) {

                $this->db->select('guest_id');

                $this->db->where("module", $params['module']);
                $this->db->where("module_id", $params['module_id']);
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

        $this->db->where("id_store", $module_id);
        $this->db->where("status", 1);
        $obj = $this->db->get("store", 1);
        $obj = $obj->result_array();

        if (count($obj) > 0) {

            $data['title'] = Text::output($campaign['name']);
            $data['sub-title'] = Text::output($campaign['text']);
            //$data['sub-title'] = Text::output($obj[0]['name']);
            $data['id'] = $module_id;
            $data['type'] = $type;

            $data['image'] = ImageManagerUtils::getFirstImage($obj[0]['images']);

            $imgJson = json_decode($obj[0]['images'], JSON_OBJECT_AS_ARRAY);
            $data['image_id'] = $imgJson[0];

        }

        return $data;

    }

    public function getStoresAnalytics($months = array(), $owner_id = 0)
    {

        $analytics = array();


        foreach ($months as $key => $m) {

            $last_month = date("Y-m-t", strtotime($key));
            $start_month = date("Y-m-1", strtotime($key));

            $this->db->where("created_at >=", $start_month);
            $this->db->where("created_at <=", $last_month);


            if ($owner_id > 0)
                $this->db->where('user_id', $owner_id);

            $count = $this->db->count_all_results("store");

            $analytics['months'][$key] = $count;

        }


        if ($owner_id > 0)
            $this->db->where('user_id', $owner_id);

        $analytics['count'] = $this->db->count_all_results("store");

        $analytics['count_label'] = Translate::sprint("Stores");
        $analytics['color'] = "#dd4b39";
        $analytics['icon_tag'] = "<i class=\"mdi mdi-storefront-outline\"></i>";
        $analytics['label'] = "Store";

        if ($owner_id == 0)
            $analytics['link'] = admin_url("store/all_stores");
        else
            $analytics['link'] = admin_url("store/my_stores");


        return $analytics;

    }


    public function markAsFeatured($params = array())
    {

        extract($params);

        if (isset($typeAuth) and $typeAuth != "admin")
            return array(Tags::SUCCESS => 0);

        if (!isset($type) and !isset($id) and !isset($featured))
            return array(Tags::SUCCESS => 0);

        $this->db->where("id_store", $id);
        $this->db->update("store", array(
            "featured" => intval($featured)
        ));

        return array(Tags::SUCCESS => 1);
    }

    public function delete($store_id, $user_id = 0)
    {


        $data["user_id"] = $user_id;

        if ($store_id > 0) {
            $store_id = intval($store_id);
        } else {
            $errors["store"] = Translate::sprint(Messages::STORE_NOT_SPECIFIED);
        }


        if (empty($errors) AND isset($data)) {

            $this->db->where("id_store", $store_id);

            if ($user_id > 0)
                $this->db->where("user_id", intval($user_id));

            $storeToDelete = $this->db->get("store", 1);
            $storeToDelete = $storeToDelete->result();

            if (count($storeToDelete) == 0) {
                $errors["Authorization"] = Translate::sprint(Messages::USER_AUTORIZATION_ACCESS);
            } else {


                foreach (StoreManager::getSubscriptions() as $subscription) {
                    //Delete all things related to this store
                    $this->db->where($subscription['field'], $store_id);
                    $this->db->delete($subscription['module']);
                }

                $this->db->where('id_store',$store_id);
                $this->db->update('store',array(
                    'hidden' => 1
                ));

                //send insert action
                ActionsManager::add_action("store", "onDelete", array("id" => $store_id));

                return array(Tags::SUCCESS => 1, "url" => admin_url("store/stores"));
            }
        }


        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }


    public function deleteReview($id_review)
    {

        if ($this->mUserBrowser->isLogged()) {
            $data["user_id"] = $this->mUserBrowser->getAdmin("id_user");
            if (isset($id_review) AND $id_review > 0) {
                $data["id_rate"] = $id_review;
            } else {
                $errors["review"] = Translate::sprint(Messages::REVIEW_NOT_SPECIFIED);
            }


            if (empty($errors) AND isset($data)) {
                $this->db->where("id_rate", $data["id_rate"]);

                $count = $this->db->count_all_results("rate");
                if ($count == 0) {
                    $errors["Authorization"] = Translate::sprint(Messages::USER_AUTORIZATION_ACCESS);
                } else {

                    $this->db->where("id_rate", $data["id_rate"]);
                    $this->db->delete("rate");

                    return array(Tags::SUCCESS => 1, "url" => admin_url("store/reviews"));
                }
            }

        } else {
            $errors["Authentification"] = Translate::sprint(Messages::USER_MISS_AUTHENTIFICATION);

        }


        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }

    public function changeStatus($params = array())
    {

        $errors = array();
        $data = array();


        if (isset($params['user_id']) and $params['user_id'] > 0) {
            $data['user_id'] = intval($params['user_id']);
        } else {
            $errors[] = Translate::sprint("User is not valid!");
        }

        if (isset($params['store_id']) and $params['store_id'] > 0) {
            $data['id_store'] = intval($params['store_id']);
        } else {
            $errors[] = Translate::sprint("Store is not valid!");
        }


        if (empty($errors) and !empty($data)) {
            $data['verified'] = 1;
            $this->db->where($data);
            $c = $this->db->count_all_results('store');
            if ($c == 1) {

                $this->db->where($data);
                $this->db->update('store', array(
                    'status' => intval($params['status'])
                ));

                return array(Tags::SUCCESS => 1);
            } else {
                $errors[] = Translate::sprint("You are not an owner of this business or this business does not verified yet");
            }
        }


        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }

    public function storeAccess($id)
    {

        $this->db->select("status");
        $this->db->from("store");
        $this->db->where("id_store", $id);
        $statusUser = $this->db->get()->row()->status;

        if (intval($statusUser) == 0) {
            $data['status'] = 1;
        } else if (intval($statusUser) == 1) {
            $data['status'] = 0;
        } else {
            $errors["status"] = Translate::sprint(Messages::STATUS_NOT_FOUND);
        }

        if (isset($data) AND empty($errors)) {

            if ($statusUser == 1) {

                //Disable all offers related to this store
                $this->db->where("store_id", $id);
                $this->db->update("offer", $data);
                //Disable all events related to this store
                $this->db->where("store_id", $id);
                $this->db->update("event", $data);
                //Disable store
                $this->db->where("id_store", $id);
                $this->db->update("store", $data);

            } else if ($statusUser == 0) {
                //Enable stores
                $this->db->where("id_store", $id);
                $this->db->update("store", $data);

            }


            return json_encode(array("success" => 1, "url" => admin_url("myStores")));

        } else {
            return json_encode(array("success" => 0, "errors" => $errors));
        }


    }

    public function nbr_reviews_per_rate($store_id, $rate)
    {

        $this->db->where('store_id', $store_id);
        $this->db->where('rate', $rate);
        return $this->db->count_all_results('rate');

    }

    public function getReviews($params = array())
    {

        extract($params);

        if (!isset($limit))
            $limit = NO_OF_ITEMS_PER_PAGE;

        if (!isset($page))
            $page = 1;


        if (isset($id_store) AND $id_store > 0) {
            $this->db->where("rate.store_id", $id_store);
        } else {

            if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
                $stores_id = $this->getOwnStores();
                if (!empty($stores_id))
                    $this->db->where_in("rate.store_id", $stores_id);
                else
                    $this->db->where_in("rate.store_id", array(0));
            }

        }

        $this->db->where("store.hidden", 0);
        $this->db->join('store','store.id_store=rate.store_id');

        $count = $this->db->count_all_results("rate");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if (isset($id_store) AND $id_store > 0) {
            $this->db->where("store_id", $id_store);
        } else {

            if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
                $stores_id = $this->getOwnStores();
                if (!empty($stores_id))
                    $this->db->where_in("store_id", $stores_id);
                else
                    $this->db->where_in("store_id", array(0));
            }

        }

        $this->db->where("store.hidden", 0);
        $this->db->join('store','store.id_store=rate.store_id');
        $this->db->select("rate.*,store.name as nameStr");
        $this->db->from("rate");

        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());
        $this->db->order_by("id_rate", "desc");
        $data = $this->db->get();

        $pagination->links(array(), admin_url("reviews"));


        return array(Tags::SUCCESS => 1, "reviews" => $data->result(), "pagination" => $pagination);
    }

    public function switchTo($old_owner = 0, $new_owner = 0)
    {

        if ($new_owner > 0) {

            $this->db->where("id_user", $new_owner);
            $c = $this->db->count_all_results("user");
            if ($c > 0) {

                $this->db->where("user_id", $old_owner);
                $this->db->update("store", array(
                    "user_id" => $new_owner
                ));

                return TRUE;
            }

        }

        return FALSE;
    }

    public function getCatName($id)
    {

        $this->db->select("name");
        $this->db->where("id_category", $id);
        $store = $this->db->get("category", 1);
        $store = $store->result_array();

        if (count($store) > 0) {
            return $store[0]['name'];
        }

        return "";
    }


    public function getCategory($id)
    {

        $this->db->where("id_category", $id);
        $store = $this->db->get("category", 1);
        $store = $store->result_array();

        if (count($store) > 0) {
            return $store[0];
        }

        return NULL;
    }

    public function getStoreData($id)
    {

        $stores = $this->mStoreModel->getStores(array(
            'limit' => 1,
            'store_id' => $id
        ));

        if (isset($stores[Tags::RESULT][0])) {
            return  $stores[Tags::RESULT][0];
        }

        return NULL;
    }

    public function getStoreName($id)
    {

        $store = $this->getStoreData($id);

        if ($store != NULL) {
            return  $store['name'];
        }

        return "";
    }


    public function getMyAllStores($params = array())
    {
        $errors = array();
        $data = array();

        extract($params);

        if (isset($user_id) and $user_id > 0) {

            $stores = $this->getStores(array(
                'user_id' =>intval($user_id),
                'hidden' => 0,
                'order_by' => "recent",
                'status' => 1,
                'limit' => -1,
            ));

            return $stores;
        }

        return array(Tags::SUCCESS => 0);
    }


    public function rate($params = array())
    {
        $errors = array();
        $data = array();

        extract($params);

        if (isset($mac_adr) AND Security::checkMacAddress($mac_adr)) {
            $data['mac_user'] = trim($mac_adr);
        } else {
            // $errors['mac_user'] = INVALID_MAC_ADDRESS ;
        }

        if (isset($rate) AND $rate > 0 AND $rate <= 5) {
            $data['rate'] = $rate;
        } else {
            $errors['rate'] = "Invalid rate";
        }


        if (isset($pseudo) AND $pseudo != null) {
            $data['pseudo'] = $pseudo;
        }

        if (isset($review) AND $review != null) {
            $data['review'] = $review;
        }


        if (isset($store_id) AND $store_id > 0) {
            $data['store_id'] = $store_id;
        } else {
            $errors['store_id'] = STORE_ID;
        }

        if (isset($guest_id) AND $guest_id > 0) {
            $data['guest_id'] = $guest_id;
        }

        if (isset($user_id) AND $user_id > 0) {
            $data['user_id'] = $user_id;
        }

        if (empty($errors)) {

            $data['date_created'] = date("Y-m-d H:s", time());
            $data['date_created'] = MyDateUtils::convert($data['date_created'], TimeZoneManager::getTimeZone(), "UTC");

            if(isset( $data['store_id']))
                $this->db->where("store_id", $data['store_id']);

            if(isset( $data['user_id']))
                $this->db->where("user_id", $data['user_id']);

            if(isset( $data['guest_id']))
                $this->db->where("guest_id", $data['guest_id']);

            $count = $this->db->count_all_results("rate");

            if ($count == 0) {
                $this->db->insert("rate", $data);
                return array(Tags::SUCCESS => 1);
            }

            $errors[] = _lang("You've already added review!");
        }

        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
    }


    private function calculVotes($datas = array())
    {
        $new_data_results = array();

        foreach ($datas as $key => $data) {


            $new_data_results[$key] = $data;
            $new_data_results[$key]['voted'] = FALSE;

            if (TRUE) {


                //$this->db->where("mac_user",$mac_adr);
                $this->db->where("store_id", $data['id_store']);
                $count = $this->db->count_all_results("rate");


                if ($count > 0) {

                    $new_data_results[$key]['voted'] = TRUE;
                }

                $new_data_results[$key]['nbr_votes'] = 0;
                $new_data_results[$key]['votes'] = 0;


                //calcul votes
                $votes = $this->db->query("SELECT SUM(rate) AS votes, COUNT(*) as nbr_votes FROM rate WHERE store_id=" . $data['id_store']);
                foreach ($votes->result() AS $value) {
                    $new_data_results[$key]['nbr_votes'] = $value->nbr_votes;

                    try {


                        if ($value->votes > 0 and $value->nbr_votes > 0)
                            $new_data_results[$key]['votes'] = (doubleval($value->votes / $value->nbr_votes));
                        else
                            $new_data_results[$key]['votes'] = 0;

                    } catch (Exception $ex) {
                        $new_data_results[$key]['votes'] = 0;
                    }


                    if (!$new_data_results[$key]['votes']) {
                        $new_data_results[$key]['votes'] = 0;
                    }


                }

            }

        }


        return $new_data_results;

    }


    public function getUnverifiedStoresCount()
    {

        $this->db->where('verified', 0);
        $this->db->where('hidden', 0);
        return $this->db->count_all_results("store");

    }

    public function getOpeningTime($current_date){

        $current_time = date('H:i:s', strtotime($current_date));

        $day = $current_date;
        $day = date("l", strtotime($day));
        $day = strtolower($day);

        $this->db->select('store_id');
        $this->db->where('day', $day);
        $this->db->where('opening <=', $current_time);
        $this->db->where('closing >=', $current_time);
        $this->db->where('enabled', 1);

        $opening_time = $this->db->get('opening_time');
        $opening_time = $opening_time->result_array();

        $opening_stores = array(0);

        if (count($opening_time) > 0) {
            $opening_stores = array();
            foreach ($opening_time as $opt) {
                $opening_stores[] = $opt['store_id'];
            }
        }

        return $opening_stores;
    }

    public function getStores($params = array(), $whereArray = array(), $callback = NULL)
    {

        //params login password mac_address
        $errors = array();
        $data = array();

        //extract — Importe les variables dans la table des symboles
        extract($params);

        if (!isset($page))
            $page = 1;

        if (isset($page) and $page == 0) {
            $page = 1;
        }

        if (isset($limit) AND $limit == 0) {
            $limit = 20;
        } else if ($limit > 0) {

        } else if ($limit == -1) {
            $limit = 100000000;
        }


        /*
        * OPENING TIME CONDITION
        */

        if (isset($opening_time)
            and ($opening_time == 1)
            and isset($current_date)
            and isset($current_tz)) {

            $opening_stores = $this->getOpeningTime($current_date);

            if (isset($opening_stores) and !empty($opening_stores)) {
                $this->db->where_in("store.id_store", $opening_stores);
            }

        }

        /*
         * END OPENING TIME CONDITION
         */

        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);

        $this->db->where('store.hidden', 0);


        if (isset($city_id) and $city_id > 0) {
            $this->db->where("store.city_id", $city_id);
        }

        if (isset($is_featured) and $is_featured == 1) {
            $this->db->where("store.featured", 1);
        }


        if (isset($status) and $status >= 0) {
            $this->db->where("store.status", $status);
        }

        if (isset($category_id) and $category_id > 0) {
            $this->db->where("store.category_id", $category_id);
        }

        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('store.name', $search);
            $this->db->or_like('store.address', $search);
            $this->db->or_like('store.detail', $search);
            $this->db->group_end();
        }

        if (isset($owner_id) and $owner_id > 0) {
            $this->db->where("store.user_id", $owner_id);
        }


        if (isset($user_id) and $user_id > 0) {
            $this->db->where("store.user_id", $user_id);
        }

        if (isset($store_ids) && $store_ids != "") {

            if (preg_match("#^([0-9,]+)$#", $store_ids)) {
                $new_ids = explode(",", $store_ids);
                $this->db->where_in("store.id_store", $new_ids);
            }

        }


        if (isset($store_id) and $store_id > 0) {
            $this->db->where("store.id_store", $store_id);
        }


        $calcul_distance = "";

        if (
            isset($longitude)
            AND
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


        $this->db->join("category", "category.id_category=store.category_id");
        $count = $this->db->count_all_results("store");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();



        if ($count == 0)
            return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => array());



        /*
         * OPENING TIME CONDITION
         */

        if (isset($opening_stores) and !empty($opening_stores)) {
            $this->db->where_in("store.id_store", $opening_stores);
        }

        /*
         * END OPENING TIME CONDITION
         */


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);


        $this->db->where('store.hidden', 0);


        if (isset($city_id) and $city_id > 0) {
            $this->db->where("store.city_id", $city_id);
        }

        if (isset($is_featured) and $is_featured == 1) {
            $this->db->where("store.featured", 1);
        }


        if (isset($status) and $status >= 0) {
            $this->db->where("store.status", $status);
        }

        if (isset($category_id) and $category_id > 0) {
            $this->db->where("store.category_id", $category_id);
        }


        if (isset($search) and $search != "") {
            $this->db->group_start();
            $this->db->like('store.name', $search);
            $this->db->or_like('store.address', $search);
            $this->db->or_like('store.detail', $search);
            $this->db->group_end();
        }

        if (isset($owner_id) and $owner_id > 0) {
            $this->db->where("store.user_id", $owner_id);
        }

        if (isset($user_id) and $user_id > 0) {
            $this->db->where("store.user_id", $user_id);
        }

        if (isset($store_ids) && $store_ids != "") {

            if (preg_match("#^([0-9,]+)$#", $store_ids)) {
                $new_ids = explode(",", $store_ids);

                $this->db->where_in("store.id_store", $new_ids);
            }

        }

        if (isset($store_id) and $store_id > 0) {
            $this->db->where("store.id_store", $store_id);
        }


        $this->db->select("( SELECT sum(rate.rate)/count(rate.id_rate) as sumRating FROM rate WHERE rate.store_id=store.id_store ) as 'sumRating',"
            . " category.color as category_color , category.name as category_name,store.*" . $calcul_distance, FALSE);


        if ($calcul_distance != "" && isset ($order_by) AND $order_by == "nearby") {

            $this->db->order_by("distance ASC");

            if (isset($radius) and $radius > 0 && $calcul_distance != "")
                $this->db->having('distance <= ' . intval($radius), NULL, FALSE);

        }else if (isset ($order_by) AND $order_by == "top_rated") {
            $this->db->order_by("sumRating", "DESC");
        } else if (isset ($order_by) AND $order_by == "nearby_top_rated") {

            $this->db->order_by("sumRating DESC, distance ASC");
            if (isset($radius) and $radius > 0 && $calcul_distance != "")
                $this->db->having('distance <= ' . intval($radius), NULL, FALSE);

        }else if (isset ($order_by) AND $order_by == "recent") {
            $this->db->order_by("store.id_store", "DESC");
        } else if ($calcul_distance != "" && isset ($order_by) && $order_by == "nearby") {
            $this->db->order_by("distance", "ASC");
            if (isset($radius) and $radius > 0 && $calcul_distance != "")
                $this->db->having('distance <= ' . intval($radius), NULL, FALSE);
        }


        $this->db->from("store");
        $this->db->join("category", "category.id_category=store.category_id");

        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());
        $stores = $this->db->get();
        $stores = $stores->result_array();


        $new_stores_results = array();
        foreach ($stores as $key => $store) {

            $userData = $this->mUserModel->syncUser(array(
                "user_id" => $store['user_id']
            ));

            $new_stores_results[$key] = $store;
            $new_stores_results[$key]['user'] = $userData;

            $new_stores_results[$key]['detail'] = html_entity_decode(
                $new_stores_results[$key]['detail'],
                ENT_QUOTES,
                ENCODING
            );

            if (isset($store['images'])) {
                $images = (array)json_decode($store['images']);
                $new_stores_results[$key]['images'] = array();
                // $new_stores_results[$key]['image'] = $store['images'];
                foreach ($images AS $k => $v) {
                    $new_stores_results[$key]['images'][] = _openDir($v);
                }
            } else {
                $new_stores_results[$key]['images'] = array();
            }


        }


        //get offers
        $this->load->model("offer/offer_model", "mOfferModel");
        foreach ($new_stores_results as $key => $store) {

            /*
             * Attachment City
             */

            if(isset($store['city_id']) && $store['city_id'] > 0){

                $city = $this->getCity($store['city_id']);

                if($city != NULL)
                    $new_stores_results[$key]['city_name'] = $city['name'];
                else{
                    $new_stores_results[$key]['city_name'] = "";
                }

            }else{
                $new_stores_results[$key]['city_name'] = "";
            }

            /*
            * End Attachment City
            */

            /*
             * Bookmark
             */
            if($this->isSaved("store",$store['id_store']))
                $new_stores_results[$key]['saved'] = "1";
            else
                $new_stores_results[$key]['saved'] = "0";


            /*
             * prepare opening time
             */

            $new_stores_results[$key]['opening'] = "0";
            $new_stores_results[$key]['opening_time_table'] = array();

            //get a valid date from system

            if(!isset($current_tz) OR $current_tz == "")
                $current_tz = Security::decrypt($this->input->get_request_header('Timezone', "UTC"));

            $current_date = MyDateUtils::convert(
                date("Y-m-d h:i A", time()),
                date_default_timezone_get(),
                $current_tz,
                "Y-m-d H:i:s"
            );


            if (isset($current_date) and isset($current_tz)) {

                // $current_date = MyDateUtils::convert($current_date, 'UTC',$current_tz , 'Y-m-d H:i:s');
                $day = $current_date;
                $day = date("l", strtotime($day));
                $day = strtolower($day);

                $current_time = date('H:i:s', strtotime($current_date));

                $this->db->where('store_id', $store['id_store']);
                $opening_time = $this->db->get('opening_time', 7);
                $opening_time = $opening_time->result_array();

                $new_stores_results[$key]['opening_time_table'] = $opening_time;

                if (count($opening_time) > 0) {

                    $all_enabled = false;
                    foreach ($opening_time as $ot) {
                        if($ot['enabled']==1){
                            $all_enabled = true;
                        }
                    }

                    if($all_enabled)
                        foreach ($opening_time as $ot) {

                            if ($ot['day'] == $day) {

                                $this->db->where('day', $day);
                                $this->db->where('opening <=', $current_time);
                                $this->db->where('closing >=', $current_time);
                                $this->db->where('store_id', $store['id_store']);
                                $this->db->where('enabled', 1);

                                $opening_time = $this->db->count_all_results('opening_time');

                                if($opening_time==1)
                                    $new_stores_results[$key]['opening'] = "1";
                                else
                                    $new_stores_results[$key]['opening'] = "-1";

                                break;
                            }
                        }

                }


            }

            /*
             * END prepare opening time
             */


            //prepare gallery

            $this->db->where("module", "store");

            $this->db->where("module_id", $store['id_store']);
            $glr = $this->db->count_all_results("gallery");
            $new_stores_results[$key]['gallery'] = $this->db->where("module", "store")->where("module_id", $store['id_store'])
                ->count_all_results("gallery");


            //count offers per store
            $this->db->where("status",1);
            $this->db->where("store_id",$store['id_store']);

            $new_stores_results[$key]['nbrOffers'] =  $this->db->count_all_results("offer");


            $this->db->where("store_id",$store['id_store']);
            $this->db->where("status",1);
            $this->db->order_by("id_offer","DESC");
            $offer = $this->db->get("offer",1);
            $offer = $offer->result();

            if (count($offer) > 0) {

                if ((isset($offer[0]->value_type) and $offer[0]->value_type == "percent") and isset($offer[0]->offer_value))
                    $new_stores_results[$key]['lastOffer'] = $offer[0]->offer_value . " %";
                else if ((isset($offer[0]->value_type) and $offer[0]->value_type == "price") and isset($offer[0]->offer_value)) {
                    $new_stores_results[$key]['lastOffer'] = Currency::parseCurrencyFormat($offer[0]->offer_value, $offer[0]->currency);
                }

            }


            $new_stores_results[$key]['link'] = site_url("store/id/" . $store["id_store"]);

        }

        $object = ActionsManager::return_action("store", "func_getStores", $new_stores_results);
        if ($object != NULL)
            $new_stores_results = $object;



        if (count($new_stores_results) < $limit) {
            $count = count($new_stores_results);
        }

        $new_stores_results = $this->calculVotes($new_stores_results);

        if ($calcul_distance != "" && isset ($order_by) && $order_by=="nearby") {
            $new_stores_results = $this->re_order_featured_item($new_stores_results);
        }

        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $new_stores_results);

    }

    private function isSaved($module,$module_id){

        $user_id = Security::decrypt($this->input->get_request_header('Session-User-Id', 0));


        $this->db->where("module",$module);
        $this->db->where("module_id",$module_id);
        $this->db->where("user_id",$user_id);

        $c = $this->db->count_all_results("bookmarks");

        if($c>0)
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

        /*usort($data,function($first, $second){
            return strtolower($first['featured']) < strtolower($second['featured']);
        });*/

        return $new_data;
    }



    public function updateStore($params = array())
    {


        //params login password mac_address
        $errors = array();
        $data = array();

        extract($params);

        if(isset($store_id) && $store_id==0){
            $errors['id'] = Translate::sprint(Messages::STORE_ID_NOT_VALID);
        }

        if (!isset($images))
            $images = array();
        else
            $images = json_decode($images);

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

        if (isset($name) AND $name != "") {
            $data["name"] = $name;
        } else {
            $errors['name'] = Translate::sprint(Messages::STORE_NAME_EMPTY);
        }



        if (isset($address) AND $address != "") {
            $data["address"] = $address;
        } else {
            $errors['address'] = Translate::sprint(Messages::STORE_ADDRESS_EMPTY);
        }


        if (isset($user_id)) {
            $data["user_id"] = intval($user_id);
        } else {
            $errors["user"] = Translate::sprint(Messages::USER_NOT_LOGGED_IN);
        }


        if (isset($detail) AND $detail != "") {
            $data["detail"] = Text::inputWithoutStripTags($detail);
        }

        if (isset($video_url) AND $video_url != "") {
            $pattern = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
            if (preg_match($pattern, $video_url)) {
                $data['video_url'] = Text::input($video_url);
            } else {
                $errors['video_url'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
            }
        } else {
            $data['video_url'] = "";
        }


        if (isset($website) and $website != "") {

            if (filter_var($website, FILTER_VALIDATE_URL)) {
                $pattern = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
                if (preg_match($pattern, $website)) {
                    $data['website'] = Text::input($website);
                } else {
                    $errors['website'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
                }
            } else {
                $errors['website'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
            }

        }else{
            $data['website'] = NULL;
        }


        if (isset($affiliate_link) and $affiliate_link != "") {

            if (filter_var($affiliate_link, FILTER_VALIDATE_URL)) {
                $pattern = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
                if (preg_match($pattern, $affiliate_link)) {
                    $data['affiliate_link'] = Text::input($affiliate_link);
                } else {
                    $errors['affiliate_link'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
                }
            } else {
                $errors['affiliate_link'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
            }

        }else{
            $data['affiliate_link'] = NULL;
        }


        if (isset($tel) AND $tel != "") {
            if (preg_match("#^[0-9 \-_.\(\)\+]+$#i", $tel)) {
                $data["telephone"] = $tel;
            } else {
                $errors['tel'] = Translate::sprint(Messages::STORE_PHONE_INVALID);
            }
        }


        if (!isset($latitude) && !isset($longitude)) {
            $errors['location'] = Translate::sprint(Messages::STORE_LOCATION_NOT_FOUND);
        } else {
            $data["latitude"] = $latitude;
            $data["longitude"] = $longitude;
        }

        if (isset($category) AND $category > 0) {
            $data["category_id"] = $category;
        } else {
            $errors["category"] = Translate::sprint(Messages::STORE_CATEGORY_NOT_SET);
        }


        if (isset($canChat) and !empty($canChat)) {
            $data['canChat'] = 1;
        } else {
            $data['canChat'] = 0;
        }

        if (isset($book) and $book >= 0 && !empty($book)) {
            $data['book'] = 1;
        } else {
            $data['book'] = 0;
        }


        /*
        * Attach city & country
        */

        if(@ModulesChecker::isEnabled('location_picker')) {
            if (isset($params['city'])) {
                $city = $this->location_picker_model->getCity($params['city']);
                if($city != NULL){
                    $data['city_id'] = $city['id_city'];
                    $data['country_code'] = $city['country_code'];
                }
            }
        }


        if (empty($errors) AND !empty($data) AND isset($store_id) AND $store_id > 0) {

            $this->db->where("id_store", $store_id);
            $this->db->where("user_id", $data["user_id"]);

            $count = $this->db->count_all_results("store");

            if ($count == 0) {
                $errors["Access"] = Translate::sprint(Messages::USER_ACCESS_DENIED);
            }

        }


        if (empty($errors) AND !empty($data)) {

            $date = date("Y-m-d H:i:s", time());
            $data['updated_at'] = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC");


            $this->db->where("id_store", $store_id);
            $this->db->update("store", $data);

            //updating opening times
            if (isset($times)) {
                $tz = "UTC";

                if (isset($timezone))
                    $tz = $timezone;

                $this->opening_time_validate($store_id, $times, $tz);

            }


            if (ModulesChecker::isRegistred("gallery")) {
                /*
            *  MANAGE STORE GALLERY
            */

                if (isset($gallery))
                    $gallery = json_decode($gallery, JSON_OBJECT_AS_ARRAY);
                else
                    $gallery = array();

                if (!empty($gallery)) {

                    $imageIds = array();
                    try {

                        if (!empty($gallery)) {
                            foreach ($gallery as $value) {
                                $image_name = $value;
                                if (preg_match("#[a-z0-9]#i", $image_name)) {
                                    $imageIds[$value] = $value;
                                }
                            }
                        }


                        if (!empty($imageIds)) {
                            $this->mGalleryModel->saveGallery("store", $store_id, $imageIds);
                        }

                    } catch (Exception $e) {

                    }

                }
                /*
                *  END MANAGE STORE GALLERY
                *////////////////////////////////////

            }


            //send insert action
            ActionsManager::add_action("store","onUpdate",array("id"=>$store_id));


            return array(Tags::SUCCESS => 1, "url" => admin_url("store/stores"));
        } else {
            return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
        }


    }


    public function createStore($params = array())
    {

        //params login password mac_address
        $errors = array();
        $data = array();

        //extract — Importe les variables dans la table des symboles
        extract($params);

        /*
         *  MANAGE STORE PHOTOS
         */
        if (isset($images))
            $images = json_decode($images, JSON_OBJECT_AS_ARRAY);
        else
            $images = array();

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

        if (empty($data["images"]) OR $data["images"] == "") {
            $errors['img'] = Translate::sprint("Please select a photo");
        }
        /*
        *  END MANAGE STORE PHOTOS
        *////////////////////////////////////


        if (isset($name) and $name != "") {
            $data['name'] = $name;
        } else {
            $errors['name'] = Translate::sprint(Messages::STORE_NAME_EMPTY);
        }

        if (isset($address) and $address != "") {
            $data['address'] = Text::input($address);
        } else {
            $errors['address'] = Translate::sprint(Messages::STORE_ADDRESS_EMPTY);
        }

        if (isset($website) and $website != "") {

            if (filter_var($website, FILTER_VALIDATE_URL)) {
                $pattern = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
                if (preg_match($pattern, $website)) {
                    $data['website'] = Text::input($website);
                } else {
                    $errors['website'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
                }
            } else {
                $errors['website'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
            }

        }


        if (isset($affiliate_link) and $affiliate_link != "") {

            if (filter_var($affiliate_link, FILTER_VALIDATE_URL)) {
                $pattern = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
                if (preg_match($pattern, $affiliate_link)) {
                    $data['affiliate_link'] = Text::input($affiliate_link);
                } else {
                    $errors['affiliate_link'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
                }
            } else {
                $errors['affiliate_link'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
            }

        }

        if (isset($phone) && $phone != "") {

            if (preg_match("#^[0-9 \-_.\(\)\+]+$#i", $phone)) {
                $data['telephone'] = Text::input($phone);
            } else {
                $errors['telephone'] = Translate::sprint(Messages::STORE_PHONE_INVALID);
            }

        } else {
            $data['telephone'] = "";
        }


        if (isset($latitude) and $latitude != "") {

            $latitude = doubleval($latitude);
            if (is_double($latitude)) {
                $data['latitude'] = doubleval($latitude);
                $this->session->set_userdata("latitude", doubleval($latitude));
            } else {
                $errors['latitude'] = Translate::sprint(Messages::USER_LOCATION_ERROR);
            }

        } else {
            $errors['latitude'] = Translate::sprint(Messages::USER_LOCATION_ERROR);
        }


        if (isset($longitude) and $longitude != "") {

            $longitude = doubleval($longitude);
            if (is_double($longitude)) {
                $data['longitude'] = doubleval($longitude);
                $this->session->set_userdata("longitude", doubleval($longitude));
            } else {
                $errors['longitude'] = Translate::sprint(Messages::USER_LOCATION_ERROR);
            }

        } else {
            $errors['longitude'] = Translate::sprint(Messages::USER_LOCATION_ERROR);
        }


        if (isset($category) and $category > 0) {
            $data['category_id'] = intval($category);
        } else {
            $errors['category_id'] = Translate::sprint(Messages::STORE_CATEGORY_NOT_SET);
        }


        if (isset($detail) and $detail != "") {
            $data['detail'] = Text::inputWithoutStripTags($detail);
        } else {
            $errors['detail'] = Translate::sprint(Messages::EVENT_DESCRIPTION_EMPTY);
        }



        if (isset($video_url) and $video_url != "") {

            if (filter_var($video_url, FILTER_VALIDATE_URL)) {
                $pattern = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
                if (preg_match($pattern, $video_url)) {
                    $data['video_url'] = Text::input($video_url);
                } else {
                    $errors['video_url'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
                }
            } else {
                $errors['video_url'] = Translate::sprint(Messages::EVENT_WEBSITE_INVALID);
            }

        }

        if (isset($user_id) AND $user_id > 0) {

            $this->db->where("id_user", $user_id);
            $count = $this->db->count_all_results("user");

            if ($count == 0) {
                $errors['user'] = Translate::sprint(Messages::USER_CREATE_ACCOUNT);
            } else {
                $data['user_id'] = $user_id;
            }

        }


        if (isset($canChat) and !empty($canChat)) {
            $data['canChat'] = 1;
        } else {
            $data['canChat'] = 0;
        }

        if (isset($book) and $book >= 0 && !empty($book)) {
            $data['book'] = 1;
        } else {
            $data['book'] = 0;
        }

        // current date from the system
        $data['date_created'] = date("Y-m-d H:i:s", time());

        /*
       * Attach city & country
       */

        if(@ModulesChecker::isEnabled('location_picker')) {
            if (isset($params['city'])) {
                $city = $this->location_picker_model->getCity($params['city']);
                if($city != NULL){
                    $data['city_id'] = $city['id_city'];
                    $data['country_code'] = $city['country_code'];
                }
            }
        }

        if (empty($errors) AND !empty($data)) {

            $nbr_store = UserSettingSubscribe::getUDBSetting($user_id, KS_NBR_STORES);

            if ($nbr_store > 0 || $nbr_store == -1) {

                $date = date("Y-m-d H:i:s", time());
                $data['created_at'] = MyDateUtils::convert($date, TimeZoneManager::getTimeZone(), "UTC");

                if (@ModulesChecker::isEnabled('location_picker')) {
                    $location_detail = LocationPickerManager::getAddressDetail($data['latitude'], $data['longitude']);
                    if (isset($location_detail[0])) {
                        if (isset($location_detail[0]['city']))
                            $data['city_str'] = $location_detail[0]['city'];
                        if (isset($location_detail[0]['country']))
                            $data['country'] = $location_detail[0]['country'];
                        if (isset($location_detail[0]['country_code']))
                            $data['country_code'] = $location_detail[0]['country_code'];
                    }
                }


                if(ConfigManager::getValue("ANYTHINGS_APPROVAL")){
                    $data['status'] = 0;
                }else{
                    $data['status'] = 1;
                }


                $this->db->insert("store", $data);
                $store_id = $this->db->insert_id();

                $this->db->where("id_store", $store_id);
                $store = $this->db->get("store");
                $store = $store->result_array();

                //add opening time
                $store_id = $store[0]['id_store'];

                if (isset($times)) {

                    $tz = "UTC";

                    if (isset($timezone))
                        $tz = $timezone;

                    $this->opening_time_validate($store_id, $times, $tz);

                }


                //refresh number of stores
                if ($nbr_store > 0) {
                    $nbr_store--;
                    UserSettingSubscribe::refreshUSetting($user_id, KS_NBR_STORES, $nbr_store);
                }


                //manage gallery for store
                $this->manage_gallery($store_id,$gallery);

                //manage uri & seo
                $this->createURI($store_id,$data['name']);

                //validate images
                if(isset($data["images"])){
                    $images = json_decode($data["images"],JSON_OBJECT_AS_ARRAY);
                    if(is_array($images))
                        foreach ($images as $image){
                            ImageManagerUtils::validate($image);
                        }
                }

                //send insert action
                ActionsManager::add_action("store","onAdd",array("id"=>$store_id));

                //send notification to the admin
                Mailer::sendAdminNotification(
                    Translate::sprintf("New store created, you can check out the link <a href='%s'>manage stores</a>",array(admin_url("dashboard/store/all_stores"))),
                    _lang("New store created")
                );

                return array(Tags::SUCCESS => 1, Tags::RESULT => $store, "url" => admin_url("stores"));

            } else {
                $errors["stores"] = Translate::sprint(Messages::EXCEEDED_MAX_NBR_STORES);
            }


        } else {

            if (isset($errors['store']))
                return array(Tags::SUCCESS => -1, Tags::ERRORS => $errors);
            else
                return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);
        }


        return array(Tags::SUCCESS => 0, Tags::ERRORS => $errors);

    }


    private function createURI($store_id,$title){

        $module = FModuleLoader::getModuleDetail("cms");
        if (version_compare($module['version_name'], '2.0.1') >= 0) {
            $slug = CMSUtils::createSlug("$store_id-".$title,"-");
            $uri = "detail-store/".$store_id;
            CMSUtils::addNewSlug($slug,$uri);
        }
    }

    private function manage_gallery($store_id,$gallery){

        if (ModulesChecker::isRegistred("gallery")) {

            /*
            *  MANAGE STORE GALLERY
            */

            if (isset($gallery))
                $gallery = json_decode($gallery, JSON_OBJECT_AS_ARRAY);
            else
                $gallery = array();

            if (!empty($gallery)) {

                $imageIds = array();
                try {

                    if (!empty($gallery)) {
                        foreach ($gallery as $value) {
                            $image_name = $value;
                            if (preg_match("#[a-z0-9]#i", $image_name)) {
                                $imageIds[$value] = $value;
                            }
                        }
                    }


                    if (!empty($imageIds)) {
                        $this->mGalleryModel->saveGallery("store", $store_id, $imageIds);
                    }

                } catch (Exception $e) {

                }

            }
            /*
            *  END MANAGE STORE GALLERY
            *////////////////////////////////////

        }


    }

    public function getComments($params)
    {
        $errors = array();
        $data = array();
        extract($params);

        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 20;

        if (isset($store_id) && $store_id > 0)
            $this->db->where("store_id", $store_id);

        $count = $this->db->count_all_results("rate");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        if (isset($store_id) && $store_id > 0)
            $this->db->where("store_id", $store_id);


        $this->db->select("rate.*");
        $this->db->from("rate");

        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        $this->db->order_by("date_created", "desc");
        $reviews = $this->db->get();
        $reviews = $reviews->result_array();

        /*
         * getComments
         */


        $this->load->model("User/mUserModel");
        foreach ($reviews as $key => $review) {

            $image = base_url("/template/backend/images/profile_placeholder.png");

            if ($review['user_id'] > 0) {

                $user = $this->mUserModel->syncUser(array(
                    'user_id' => $review['guest_id']
                ));

                if ($user != NULL and isset($user[Tags::RESULT][0])) {
                    $user = $user[Tags::RESULT][0];
                    if (isset($user['images'][0]['200_200']['url'])) {
                        $image = $user['images'][0]['200_200']['url'];
                    } else {
                        $image = base_url("template/backend/images/profile_placeholder.png");
                    }
                }
            }

            $reviews[$key]['image'] = $image;
        }

        return array(Tags::SUCCESS => 1, Tags::RESULT => $reviews, Tags::COUNT => $count);

    }


    public function saveStore($params = array())
    {

        $data = array();
        $errors = array();


        extract($params);

        if (isset($store_id) AND $store_id > 0 AND isset($user_id) AND $user_id > 0) {

            $this->db->where("user_id", $user_id);
            $this->db->where("type", "stores");
            $obj = $this->db->get("saves");
            $obj = $obj->result_array();


            if (count($obj) == 0) {

                $this->db->insert("saves", array(
                    "user_id" => $user_id,
                    "type" => "stores",
                    "ids" => json_encode(array($store_id), JSON_OBJECT_AS_ARRAY),
                ));

            } else if ($obj[0]['ids'] != NULL) {

                $obj[0]['ids'] = json_decode($obj[0]['ids'], JSON_OBJECT_AS_ARRAY);

                if (!in_array($store_id, $obj[0]['ids'])) {

                    $obj[0]['ids'][] = $store_id;

                    $this->db->where("user_id", $user_id);
                    $this->db->where("type", "stores");
                    $this->db->update("saves", array(
                        "ids" => json_encode($obj[0]['ids'], JSON_OBJECT_AS_ARRAY),
                    ));
                }


            }

            $this->load->model("User/mUserModel");
            $this->mUserModel->addCustomer($user_id, $store_id);

        }

        return array(Tags::SUCCESS => 1);

    }

    public function removeStore($params = array())
    {

        $data = array();
        $errors = array();

        extract($params);

        if (isset($store_id) AND $store_id > 0 AND isset($user_id) AND $user_id > 0) {

            $this->db->where("user_id", $user_id);
            $this->db->where("type", "stores");
            $obj = $this->db->get("saves");
            $obj = $obj->result_array();


            if (count($obj) > 0 and $obj[0]['ids'] != NULL) {

                $obj[0]['ids'] = json_decode($obj[0]['ids'], JSON_OBJECT_AS_ARRAY);

                foreach ($obj[0]['ids'] as $k => $v) {

                    if ($v == $store_id) {
                        unset($obj[0]['ids'][$k]);
                    }

                }

                $this->db->where("user_id", $user_id);
                $this->db->where("type", "stores");
                $this->db->update("saves", array(
                    "ids" => json_encode($obj[0]['ids'], JSON_OBJECT_AS_ARRAY)
                ));

                $this->load->model("User/mUserModel");
                $this->mUserModel->removeCustomer($user_id, $store_id);
            }

        }

        return array(Tags::SUCCESS => 1);

    }


    private function getOwnStores()
    {

        $data = array();

        $user_id = $this->mUserBrowser->getData("id_user");
        $isManager = $this->mUserBrowser->getData("manager");


        if ($isManager != 1) {

            $this->db->where("user_id", $user_id);
            $this->db->select("id_store");
            $stores = $this->db->get("store");
            $stores = $stores->result();

            foreach ($stores as $store) {
                $data[] = $store->id_store;
            }
        }

        return $data;

    }


    public function recentlyAdd($user_id = 0)
    {

        $params = array(
            'limit' => NBR_RECENTLY_ADDED_STORES,
            'page' => 1,
            'order_by' => "recent",
        );

        if ($this->mUserBrowser->getData("manager") != 1) {
            $params['user_id'] =  $this->mUserBrowser->getData("id_user");
        }

        $stores = $this->getStores($params);

        return array(Tags::SUCCESS => 1, "stores" => $stores[Tags::RESULT], "count" => $stores[Tags::COUNT]);
    }


    public function updateFields()
    {
        /*
         * Clear table store
         */

        if ($this->db->field_exists('local_ref', 'store')) {
            $this->dbforge->drop_column('store', 'local_ref');
        }

        if ($this->db->field_exists('gp_ref', 'store')) {
            $this->dbforge->drop_column('store', 'gp_ref');
        }

        if ($this->db->field_exists('city', 'store')) {
            $this->dbforge->drop_column('store', 'city');
        }

        if ($this->db->field_exists('country', 'store')) {
            $this->dbforge->drop_column('store', 'country');
        }

        if ($this->db->field_exists('customers', 'store')) {
            $this->dbforge->drop_column('store', 'customers');
        }

        /*
        * End Clear table store
        */

        if (!$this->db->field_exists('city_str', 'store')) {
            $fields = array(
                'city_str' => array('type' => 'VARCHAR(100)',  'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }

        if (!$this->db->field_exists('city', 'store')) {
            $fields = array(
                'city' => array('type' => 'INT',  'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


        if (!$this->db->field_exists('default_value', 'cf_list')) {
            $fields = array(
                'default_value' => array('type' => 'VARCHAR(100)', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('cf_list', $fields);
        }

        if (!$this->db->field_exists('video_url', 'store')) {
            $fields = array(
                'video_url' => array('type' => 'VARCHAR(150)', 'after' => 'detail', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


        if (!$this->db->field_exists('user_id', 'rate')) {
            $fields = array(
                'user_id' => array('type' => 'INT', 'after' => 'store_id', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('rate', $fields);
        }


        if (!$this->db->field_exists('verified', 'store')) {
            $fields = array(
                'verified' => array('type' => 'INT', 'after' => 'tags', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


        if (!$this->db->field_exists('created_at', 'store')) {
            $fields = array(
                'created_at' => array('type' => 'DATETIME', 'after' => 'tags', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }

        if (!$this->db->field_exists('updated_at', 'store')) {
            $fields = array(
                'updated_at' => array('type' => 'DATETIME', 'after' => 'created_at', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


        //add new field to enable or disable chat feature for each tool
        if (!$this->db->field_exists('canChat', 'store')) {
            $fields = array(
                'canChat' => array('type' => 'INT', 'after' => 'status', 'default' => 1),
            );
            $this->dbforge->add_column('store', $fields);
        }

        //add new website  field
        if (!$this->db->field_exists('website', 'store')) {
            $fields = array(
                'website' => array('type' =>'VARCHAR(200)', 'after' => 'name', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


        if (!$this->db->field_exists('hidden', 'store')) {
            $fields = array(
                'hidden' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


        if (!$this->db->field_exists('affiliate_link', 'store')) {
            $fields = array(
                'affiliate_link' => array('type' => 'TEXT', 'default' => ""),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }



        $sql = "ALTER TABLE `store` CHANGE `name` `name` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;";
        $this->db->query($sql);
    }


    public function getOpeningTimes($store_id)
    {

        $this->db->where('store_id', $store_id);
        $result = $this->db->get('opening_time');
        $result = $result->result_array();
        return $result;

    }


    private function opening_time_validate($store_id, $times, $timezone = "UTC")
    {

        $days = array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        );

        foreach ($days as $day) {

            if (isset($times[$day]['opening']) and isset($times[$day]['closing'])) {

                $valid_opening_time = array(
                    'store_id' => $store_id,
                    'day' => $day,
                    'opening' => date("H:i", strtotime($times[$day]['opening'])),
                    'closing' => date("H:i", strtotime($times[$day]['closing'])),
                    'timezone' => $timezone,
                );
                if ($times[$day]['opening'] == ""
                    OR $times[$day]['closing'] == "")
                    $valid_opening_time['enabled'] = 0;
                else
                    $valid_opening_time['enabled'] = 1;

                $this->db->where('store_id', $store_id);
                $this->db->where('day', $day);
                $c = $this->db->count_all_results('opening_time');


                if ($c == 0) {
                    $this->db->insert('opening_time', $valid_opening_time);
                } else {
                    $this->db->where('store_id', $store_id);
                    $this->db->where('day', $day);
                    $this->db->update('opening_time', $valid_opening_time);
                }

            }

        }

    }


    public function addOpeningTimeTable()
    {


        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'store_id' => array(
                'type' => 'INT',
                'constraint' => 11
            ),
            'day' => array(
                'type' => 'VARCHAR(60)',
                'default' => NULL
            ),
            'opening' => array(
                'type' => 'TIME',
                'default' => NULL
            ),
            'closing' => array(
                'type' => 'TIME',
                'default' => NULL
            ),
            'enabled' => array(
                'type' => 'INT(1)',
                'default' => NULL
            ),
            'timezone' => array(
                'type' => 'VARCHAR(60)',
                'default' => NULL
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),
        );

        $this->dbforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('opening_time', TRUE, $attributes);


    }

    public function add_fk_field($module, $key)
    {

        if (!$this->db->field_exists($key, $module)) {
            $fields = array(
                $key => array('type' => 'INT', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column($module, $fields);
        }

    }


    public function add_store_country_field()
    {

        if (!$this->db->field_exists('country', 'store')) {
            $fields = array(
                'country' => array('type' => 'VARCHAR(60)',  'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }

        if (!$this->db->field_exists('country_code', 'store')) {
            $fields = array(
                'country_code' => array('type' => 'VARCHAR(3)',  'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('store', $fields);
        }


    }


}
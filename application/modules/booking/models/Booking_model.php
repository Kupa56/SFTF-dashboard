<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Booking_model extends CI_Model
{


    public function lastTransactions($user_id, $owner_id, $limit){

        ActionsManager::register("user","funcLoadLastActions",function ($object){
            return $object;
        });
    }

    public function checkBooking($id,$business_user_id){

        $this->db->select('booking.id, booking.amount, booking.status as "booking_status", booking.payment_status, store.name as "store_name", user.name as "user_name"');
        $this->db->where('booking.id',$id);
        $this->db->where('store.user_id',intval($business_user_id));
        $this->db->join('store','store.id_store=booking.store_id');
        $this->db->join('user','user.id_user=store.user_id');
        $booking = $this->db->get("booking",1);
        $result =  $booking->result_array();

        if(isset($result[0]))
            return $result[0];

        return NULL;
    }

    public function createDashboardWidget(){


        /*
         * Register widget
         */

        CMS_Display::set("widget_middle","booking/client-dashboard/booking-widget",array(
            "client_id" => SessionManager::isLogged()
            && GroupAccess::isGranted("booking",GRP_MANAGE_BOOKING_CONFIG)? -1
                : SessionManager::getData("id_user")
        ));

        /*
         * Include css & Javascript libraries
         */


        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets("booking","css/client-dashboard.css")
        );

        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets("booking","plugins/morris.js/morris.css")
        );

    }

    public function getWidgetData(){

        $result = array();

        $result['0'] = $this->countTodayBookings(); //today
        $result['1'] = $this->countYesterdayBookings(); //yesterday
        $result['2'] = $this->countLast7daysBookings();//last7days
        $result['3']  = $this->countLast30daysBookings(); //last30days
        $result['4'] = $this->countLast90daysBookings(); //last90days
        $result['5'] = $this->countLast1yearBookings(); //last1year

        return $result;
    }

    private function countTodayBookings(){

        //y / columns
        $columns = array();
        for ($i=23;$i>=0;$i--){
            $d = sprintf('%02d', $i);
            $columns[] = MyDateUtils::getDate("Y-m-d $d:00",time());
        }

        return $this->countBookingColumns($columns);

    }

    public function countYesterdayBookings(){

        //y / columns
        $columns = array();
        for ($i=23;$i>=0;$i--){
            $d = sprintf('%02d', $i);
            $columns[] = MyDateUtils::getDate("Y-m-d $d:00",strtotime("now - 1 days"));
        }


        return $this->countBookingColumns($columns);

    }

    public function countLast7daysBookings(){

        //y / columns
        $columns = array();
        for ($i=0;$i<=6;$i++){
            $columns[] = MyDateUtils::getDate("Y-m-d 23:59",strtotime("today - $i days"));
        }

        return $this->countBookingColumns($columns);

    }

    private function countLast30daysBookings(){

        //y / columns
        $columns = array();
        for ($i=0;$i<=29;$i++){
            $columns[] = MyDateUtils::getDate("Y-m-d 23:59",strtotime("today - $i days"));
        }

        return $this->countBookingColumns($columns);
    }

    private function countLast90daysBookings(){

        //y / columns
        $columns = array();
        for ($i=0;$i<=89;$i++){
            $columns[] = MyDateUtils::getDate("Y-m-d 23:59",strtotime("today - $i days"));
        }

        return $this->countBookingColumns($columns);
    }

    private function countLast1yearBookings(){

        //y / columns
        $columns = array();
        for ($i=0;$i<=11;$i++){
            $columns[] = MyDateUtils::getDate("Y-m-d 23:59",strtotime("today - $i month"));
        }

        return $this->countBookingColumns($columns);
    }


    private function countBookingColumns($columns){

        $result = array();
        $result['all'] = array();
        $result['pending'] = array();
        $result['canceled'] = array();
        $result['confirmed'] = array();

        foreach ($columns as $first_column){

            $next_column = next($columns);

            if(empty($next_column))
               continue;

            //all
            $result['all'][$first_column] = $this->countBkStatus($first_column,$next_column);

            //pending
            $result['pending'][$first_column] = $this->countBkStatus($first_column,$next_column,0);

            //canceled
            $result['canceled'][$first_column] = $this->countBkStatus($first_column,$next_column,-1);

            //confirmed
            $result['confirmed'][$first_column] = $this->countBkStatus($first_column,$next_column,1);
        }

        return $result;

    }


    private function countBkStatus($first_column,$next_column,$status=-100000){

        if(SessionManager::isLogged()){
            $this->db->where("booking.user_id",ClientSession::getData("id_user"));
        }else if(SessionManager::isLogged()){
            $this->db->where("store.user_id",SessionManager::getData("id_user"));
            $this->db->join("store","store.id_store=booking.store_id");
        }

        $this->db->where("booking.created_at <= ",$first_column); //small
        $this->db->where("booking.created_at > ",$next_column); //big

        if($status != -100000){
            $this->db->where("booking.status",$status);
        }

        $count =  $this->db->count_all_results("booking");

        return $count;

    }


    public function create_file($id){


        $data = $this->mBookingModel->get_booking_data_doc($id);
        $data['no'] = str_pad($data['no'], 6, "0", STR_PAD_LEFT);

        if(is_null($data['extras'])){
            $data['extras'] = array();
        }

        if(!empty($data['extras'])){
            $extras = array();
            foreach ($data['extras'] as $key => $extra){
                $extras[] = array(
                    'key' => $key,
                    'value' => $extra,
                );
            }
            $data['extras'] = $extras;
        }


        if(!isset($data['amount'])){
            $data['amount'] = $data['sub_amount '];
        }

        //get business owner
        $store = $this->getStore($data['store_id']);
        $bo_language = $this->mUserModel->getFieldById("user_language", $store['user_id']);
        $file = FCPATH.'/application/modules/booking/views/mailing/booking-doc-'.$bo_language.'.htm';

        if(!file_exists($file))
            $file = "booking/mailing/booking-doc.htm";


        return $this->parser->parse($file,$data,TRUE);
    }

    public function sendBookingDetailToBO($booking_id)
    {


        $booking = $this->getBooking($booking_id);
        $doc_html = $this->create_file($booking_id);

        //get email
        $store = $this->getStore($booking['store_id']);
        $destination_email = $this->mUserModel->getFieldById("email", $store['user_id']);

        $subject = Translate::sprintf("You have new booking placed %s",array("#".str_pad($booking['id'], 6, 0, STR_PAD_LEFT))) ;

        //send email
        $result = $this->mMailer->send(array(
            "recipient" => $destination_email,
            "from_email" 	=> ConfigManager::getValue('DEFAULT_EMAIL'),
            "from_name" 	=> ConfigManager::getValue('APP_NAME'),

            "reply_email" 	=> ConfigManager::getValue('DEFAULT_EMAIL'),
            "reply_name" 	=>  ConfigManager::getValue('APP_NAME'),

            "to_email" 		=> $destination_email,
            "to_cc" 		=> array(),
            "to_bcc" 		=> array(),
            "subject" 		=> $subject,
            "content" 		=> $doc_html,
            "attachments" 	=> array(),
        ));


    }

    public function get_booking_data_doc($booking_id)
    {


        $booking = $this->getBooking($booking_id);
        $invoice = $this->mBookingPayment->getInvoice($booking['id']);



        $logo = ImageManagerUtils::getValidImages(APP_LOGO);
        $imageUrl = adminAssets("images/logo.png");
        if (!empty($logo)) {
            $imageUrl = $logo[0]["200_200"]["url"];
        }


        $data['logo'] = $imageUrl;
        $data['doc_name'] = Translate::sprint('Booking');
        $data['no'] = $booking['id'];
        $data['created_at'] = date("D M Y h:i:s A", time()) . ' UTC';
        $data['client_name'] = ucfirst($this->mUserModel->getFieldById("name", $booking['user_id']));
        $data['client_data'] = "";
        $data['booking_link'] = admin_url("booking/view?id=".$booking_id);


        $cf_id = intval($booking['cf_id']);
        $booking['cf_data'] = json_decode($booking['cf_data'], JSON_OBJECT_AS_ARRAY);
        if (isset($booking['cf_data'])) {

            $cf_object = CFManagerHelper::getByID($cf_id);
            $fields = json_decode($cf_object['fields'], JSON_OBJECT_AS_ARRAY);

            foreach ($fields as $key => $field) {

                $cf_data = $booking['cf_data'][$field['label']];

                if ($cf_data == "") {
                    continue;
                }

                if (CFManagerHelper::getTypeByID($cf_id, $key) == "input.location") {
                    if ($key == "") {
                        $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: -- </span><br>";
                    } else {

                        if (preg_match("#;#", $cf_data)) {
                            $l = explode(";", $cf_data);
                            $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: " . $l[0] . "<br>";
                        } else {
                            $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: $cf_data </span><br>";
                        }
                    }
                } else
                    $data['client_data'] = $data['client_data'] . "<span><strong>" . $field['label'] . "</strong>: $cf_data</span><br>";

            }


        }

        $data['items'] = json_decode($booking['cart'], JSON_OBJECT_AS_ARRAY);


        $sub_total = 0;
        $currency = $invoice->currency;

        foreach ($data['items'] as $key => $item) {

            $sub_total = $sub_total + $item['amount'] * intval($item['qty']);

            $callback = NSModuleLinkers::find($item['module'], 'getData');
            if ($callback != NULL) {

                $params = array(
                    'id' => $item['module_id']
                );

                $result = call_user_func($callback, $params);
                $data['items'][$key]['label'] = $result['label'] . " x " . intval($item['qty']);


                if (isset($item['variants'])) {
                    $data['items'][$key]['label'] = $data['items'][$key]['label'] . "<span style='    font-size: 14px;
                            color: grey;'>" . BookingHelper::optionsBuilderString($item['variants']) . "</span>";

                }



                $amount = $data['items'][$key]['amount'] * intval($item['qty']);

                $data['items'][$key]['amount'] = Currency::parseCurrencyFormat(
                    $amount,
                    $currency
                );
            }

        }


        $data['sub_amount'] = Currency::parseCurrencyFormat($sub_total, $currency);

        if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {

            $percent = 0;
            $tax = $this->mTaxModel->getTax(DEFAULT_TAX);

            if ($tax != NULL)
                $percent = $tax['value'];

            $taxed_amount = (($percent / 100) * $sub_total);

            $data['taxes_value'][] = array(
                'tax_value' => Currency::parseCurrencyFormat($taxed_amount, $currency),
                'tax_name' => $tax['name'],
            );

            $sub_total = $taxed_amount + $sub_total;

        }


        $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY);

        if (!empty($extras)){
            foreach ($extras as $key => $value) {
                $extras[$key] = Currency::parseCurrencyFormat($value, $currency);
                $sub_total = $value + $sub_total;
            }

            $data['extras'] = $extras;
        }

        $data['extras'] = $extras;

        $data['amount'] = Currency::parseCurrencyFormat($sub_total, $currency);
        $data['status'] = 0;


        return $data;

    }

    public function getInvoice($id){

        $this->db->where("module","booking_payment");
        $this->db->where("module_id",$id);
        $invoice = $this->db->get("invoice",1);
        $invoice = $invoice->result();

        if(count($invoice)>0)
            return $invoice[0];

        return NULL;

    }

    public function update_payment_status($booking_id,$payment_status_id,$transactionId=""){

        $errors = array();

        $reservation = $this->getBooking($booking_id);
        $pcode = $reservation['payment_status'];

        if ( ($pcode == "cod_paid" && $pcode == "paid")
            OR GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING_CONFIG)){

        }else{
            return;
        }

        $status = Booking_payment::PAYMENT_STATUS;

        if(!isset($status[$payment_status_id])){
            $errors[] = _lang("Payment status not found");
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        }

        //update make it as paid in invoice
        $invoice = $this->mBookingPayment->getInvoice($reservation['id']);

        if($payment_status_id == "cod_paid" && $invoice->status == 0){

        }elseif($payment_status_id == "transferBankPaid" && $invoice->status == 0){
            if($transactionId == ""){
                $errors[] = _lang("Please enter transaction id for bank transfer");
            }
        }


        if(empty($errors)){

            $key = md5("abc-key".$invoice->id);

            $this->mPaymentModel->updateInvoiceSource(
                $invoice->id,
                "Updated ".($invoice->method!=""?" (".$invoice->method.")":""),
                ($transactionId==""?"NO_ID":$transactionId) ,
                $key,
                FALSE
            );


            $this->db->where('id',$booking_id);
            $this->db->update('booking',array(
                'payment_status' => $payment_status_id
            ));

            return array(Tags::SUCCESS=>1);
        }


        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    public function countPending($isOwner = FALSE)
    {
        $owner_id = $this->mUserBrowser->getData("id_user");

        if ($isOwner && $owner_id>0)
            $this->db->where("store.user_id", $owner_id);

        $this->db->where("booking.status", 0);
        $this->db->join("store", "booking.store_id=store.id_store","inner");
        $count = $this->db->count_all_results("booking");


        return array(
            Tags::SUCCESS => 1,
            Tags::COUNT => $count
        );

    }


    public function getStore($store_id){

        $result = $this->mStoreModel->getStores(array(
            "limit"=>1,
            "store_id"=>$store_id,
        ));

        if(!isset($result[Tags::RESULT][0]))
            return NULL;

        return $result[Tags::RESULT][0];
    }

    public function getBookings($params = array(), $whereArray = array(), $callback = NULL)
    {

        extract($params);
        $errors = array();

        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 30;

        if (!isset($order_by))
            $order_by = "recent";

        if (!isset($radius))
            $radius = RADUIS_TRAGET * 1000;


        if (!empty($whereArray))
            foreach ($whereArray as $key => $value) {
                $this->db->where($key, $value);
            }

        if ($callback != NULL)
            call_user_func($callback, $params);


        if (isset($id) and $id > 0) {
            $this->db->where("booking.id", $id);
        }


        if (isset($user_id) and $user_id > 0) {
            $this->db->where("booking.user_id", $user_id);
        }

        if (isset($owner_id) and $owner_id > 0) {
            $this->db->where("store.user_id", $owner_id);
        }

        if (isset($date_start) && $date_start != "" && isset($date_end) && $date_end != "") {
            $this->db->where('booking.updated_at BETWEEN "' . date('Y-m-d', strtotime($date_start)) . '" and "' . date('Y-m-d', strtotime($date_end)) . '"');
        }


        if (isset($order_status) and $order_status > 0) {
            $this->db->where("booking.status", $order_status);
        }


        if (isset($booking_id) and $booking_id > 0) {
            $this->db->where("booking.id", intval($booking_id));
        }


        $this->db->join('store', 'store.id_store=booking.store_id');
        $this->db->join('user', 'user.id_user=booking.user_id');

        $calculated_distance_q = "";

        if (isset($longitude) && isset($latitude) && isset($order_by) && $order_by == "nearby") {


            $longitude = doubleval($longitude);
            $latitude = doubleval($latitude);

            $calculated_distance_q = " , IF( store.latitude = 0,99999,  (1000 * ( 6371 * acos (
                              cos ( radians(" . $latitude . ") )
                              * cos( radians( store.latitude ) )
                              * cos( radians( store.longitude ) - radians(" . $longitude . ") )
                              + sin ( radians(" . $latitude . ") )
                              * sin( radians( store.latitude ) )
                            )
                          ) ) ) as 'distance'  ";


            if (isset($radius) and $radius > 0 && $calculated_distance_q != "")
                $this->db->having('distance <= ' . intval($radius), NULL, FALSE);

        }

        $count = $this->db->count_all_results("booking");

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


        if (isset($id) and $id > 0) {
            $this->db->where("booking.id", $id);
        }


        if (isset($user_id) and $user_id > 0) {
            $this->db->where("booking.user_id", $user_id);
        }


        if (isset($owner_id) and $owner_id > 0) {
            $this->db->where("store.user_id", $owner_id);
        }

        if (isset($date_start) && $date_start != "" && isset($date_end) && $date_end != "") {
            $this->db->where('booking.updated_at BETWEEN "' . date('Y-m-d', strtotime($date_start)) . '" and "' . date('Y-m-d', strtotime($date_end)) . '"');
        }


        if (isset($order_status) and $order_status > 0) {
            $this->db->where("booking.status", $order_status);
        }


        if (isset($booking_id) and $booking_id > 0) {
            $this->db->where("booking.id", intval($booking_id));
        }


        $this->db->where('store.status !=', -1);
        $this->db->join('store', 'store.id_store=booking.store_id');
        $this->db->join('user', 'user.id_user=booking.user_id');

        $this->db->select("booking.*,store.id_store,store.name as 'store_name', store.images as 'store_images', user.name as 'client_name'");
        $this->db->from("booking");
        $this->db->order_by("booking.created_at","DESC");

        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        $bookings = $this->db->get();
        $bookings = $bookings->result_array();


        $new_bookings_results = array();
        foreach ($bookings as $key => $reservation) {

            $new_bookings_results[$key] = $reservation;
            $carts = json_decode($reservation["cart"], JSON_OBJECT_AS_ARRAY);

            foreach ($carts as $cart) {

                if(empty($cart))
                    continue;

                $items = $this->moduleDetailFromId(array(
                    "module_id" => $cart['module_id'],
                    "module" => $cart['module']
                ));

                $items["id"] = $cart['module_id'];
                $items["module"] = $cart['module'];
                $items["qty"] = $cart["qty"];
                $items["amount"] = $cart["amount"];
                $items["options"] = $cart["options"];


                $options = json_decode($cart["options"],JSON_OBJECT_AS_ARRAY);
                $options_array = array();
                foreach ($options as $k => $variant){
                    $options_array[] = $k;
                }

                $items["services"] = implode(", ",$options_array);

                $new_bookings_results[$key]["items"][] = $items;
            }

            $new_bookings_results[$key]["status_id"] = $reservation['status'];

            if($reservation['status'] == 0){
                $new_bookings_results[$key]["status"] = "Pending;#ff8a1e";
            }else if($reservation['status'] == 1){
                $new_bookings_results[$key]["status"] = "Confirmed;#2197e0";
            }else if($reservation['status'] == -1){
                $new_bookings_results[$key]["status"] = "Canceled;#ff3535";
            }


        }



        if (ModulesChecker::isEnabled("booking_payment")) {

            $ps = Booking_payment::PAYMENT_STATUS;

            foreach ($new_bookings_results as $key => $booking) {

                $invoice = $this->getInvoiceID($booking['id']);
                $new_bookings_results[$key]['payment_status_data'] = null;

                if (($booking['payment_status'] == "unpaid" or
                        $booking['payment_status'] == "" or
                        $booking['payment_status'] == "0") && $invoice != NULL) {

                    $new_bookings_results[$key]['invoice'] = $invoice['id'];

                    if (isset($ps[$booking['payment_status']])) {
                        $new_bookings_results[$key]['payment_status_data'] = _lang($ps[$booking['payment_status']]['label']) . ";" . $ps[$booking['payment_status']]['color'];
                    } else {
                        $new_bookings_results[$key]['payment_status_data'] = _lang("unpaid") . ";" . $ps["unpaid"]['color'];
                    }

                }else if (isset($ps[$booking['payment_status']])) {
                    $obj = $ps[$booking['payment_status']];
                    $new_bookings_results[$key]['payment_status_data'] = _lang($obj['label']) . ";" . $obj['color'];
                }

                if($new_bookings_results[$key]['payment_status_data'] == null){
                    $new_bookings_results[$key]['payment_status_data'] = _lang(Booking_payment::PAYMENT_STATUS["unpaid"]["label"]) . ";" . Booking_payment::PAYMENT_STATUS["unpaid"]["color"];
                }

                if( isset($invoice['extras']) && $invoice['extras']!= null)
                    $new_bookings_results[$key]['extras'] = $invoice['extras'];
            }

        }



        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $new_bookings_results);

    }

    public function getInvoiceID($booking_id)
    {


        $this->db->where('module', 'booking_payment');
        $this->db->where('module_id', $booking_id);
        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result_array();

        if (isset($invoice[0]))
            return $invoice[0];

        return NULL;
    }



    private function moduleDetailFromId($params = array())
    {

        $errors = array();
        $data = array();

        extract($params);

        if ((isset($module_id) && intval($module_id) > 0) && (isset($module) && $module != "")) {

            $this->db->where("id", $module_id);
            $c = $this->db->count_all_results($module);

            if ($c > 0) {

                $dataResult = array();
                $this->db->where("id" , $module_id);
                $dbResult = $this->db->get($module, 1);
                $dbResult = $dbResult->result_array();

                if (isset($dbResult)) {
                    $dataResult["name"] = $dbResult[0]["label"];
                    //store images
                    $activeImages = $this->mStoreModel->getAllActiveImages($dbResult[0]["store_id"]);
                    $dataResult["image"] = ImageManagerUtils::getFirstImage($activeImages,ImageManagerUtils::IMAGE_SIZE_200);
                }

                return $dataResult;
            }


        }

        return NULL;

    }



    public function createBooking($params = array()){


        extract($params);

        $errors = array();
        $data = array();

        if (isset($cart) and $cart != "") {

            if (!is_array($cart))
                $cart = json_decode($cart, JSON_OBJECT_AS_ARRAY);

            $data['cart'] = $cart;
        }

        if (isset($store_id) and $store_id > 0) {
            $data['store_id'] = intval($store_id);
        } else {
            $errors['store_id'] = Translate::sprint(Messages::STORE_ID_NOT_VALID);
        }

        if (isset($user_id) and $user_id > 0) {
            $data['user_id'] = intval($user_id);
        } else {
            $errors['user_id'] = Translate::sprint(Messages::USER_NOT_SELECTED);
        }


        if (isset($req_cf_data) && $req_cf_data != "") {

            if (!is_array($req_cf_data))
                $data['cf_data'] = json_decode($req_cf_data, JSON_OBJECT_AS_ARRAY);

            $data['cf_data'] = $req_cf_data;

        } else {
            $errors['cf_data'] = Translate::sprint(Messages::CUSTOM_FIELDS_EMPTY);
        }

        if (isset($req_cf_id) && $req_cf_id > 0) {
            $data['cf_id'] = intval($req_cf_id);
        }else if(isset($data['module_id'])){

            //get cf id from category
            $this->db->select('category_id');
            $this->db->where('id_store',intval($data['module_id']));
            $stores = $this->db->get('store',1);
            $stores = $stores->result_array();

            if(isset($stores[0])){
                $category_id = $stores[0]['category_id'];
                $category = $this->mStoreModel->getCategory($category_id);
                $data['cf_id'] = $category['cf_id'];
            }
        }

        if (empty($errors)) {
            $data['status'] = 0;
        }

        if (empty($errors) and !empty($data)) {

            if (is_array($data['cart']))
                $data['cart'] = json_encode($data['cart'],JSON_FORCE_OBJECT);

            if (is_array($data['cart']))
                $data['cf_data'] = json_encode($data['cart']);

            $date = date("Y-m-d H:i:s", time());
            $data['created_at'] = $date;
            $data['updated_at'] = $date;

            $this->db->insert("booking", $data);
            $booking_id = $this->db->insert_id();

            //$result = $this->mBookingModel->sendBookingDetailToBO($booking_id);

            ActionsManager::add_action("booking","book_created",$booking_id);

            return array(Tags::SUCCESS => 1, Tags::RESULT => $booking_id);
        }

        return array(Tags::SUCCESS => -1, Tags::ERRORS => $errors);

    }

    public function getBooking($booking_id){

        $this->db->where('id',$booking_id);
        $booking = $this->db->get('booking',1);
        $booking = $booking->result_array();

        if(isset($booking[0]))
            return $booking[0];

        return NULL;
    }

    public function change_booking_status($booking_id, $status, $message,$client_id = 0,$business_user_id=0)
    {

        $booking = $this->getBooking($booking_id);

        if ($booking != NULL) {

            //CHECK THE OWNER
            if($business_user_id>0){
                $this->db->where('id',$booking_id);
                $this->db->where('store.user_id',$business_user_id);
                $this->db->join('store','store.id_store=booking.store_id');
                $count = $this->db->count_all_results("booking");
                if($count == 0)
                    return array(Tags::SUCCESS=>0);
            }


            if($client_id>0)
                $this->db->where('user_id',$client_id);


            $this->db->where('id',$booking_id);
            $this->db->update('booking',array(
                'status' => intval($status)
            ));

            $store = $this->getStore($booking['store_id']);
            $store_name = $store["name"];
            $store_image = "";

            if (isset($store["images"])){

                if(is_string($store["images"]))
                    $images  = json_decode($store["images"],JSON_OBJECT_AS_ARRAY);

                if(isset($images[0]) && is_string($images[0])){
                    $images = _openDir($images[0]);
                    if(isset($images['name']))
                        $store_image = $images['name'];
                }else if(isset($images[0]) && is_array($images[0])){
                    $images = $images[0];
                    if(isset($images['name']))
                        $store_image = $images['name'];
                }
            }

            if($status == 0){
               return;
            }else if($status == 1){
                $status_name = "Confirmed";
            }else if($status == -1){
                $status_name = "Declined";
            }

            if ($message == "") {
                $notif_body = Translate::sprintf("Reservation #%s is  %s", array(
                    $booking_id,
                    $status_name
                ));
            } else {
                $notif_body = Translate::sprintf("Reservation #%s is %s , Message   : %s", array(
                    $booking_id,
                    $status_name,
                    $message
                ));
            }


            //add historic
            $historic = NSHistoricManager::refresh(array(
                'module' => "booking",
                'module_id' => $booking_id,
                'auth_type' => "user",
                'auth_id' => $booking['user_id'],
                'image' => json_encode(array($store_image)),
                'label' => $notif_body,
                'label_description' => $store_name,
            ));


            //fcm ,  store_name, status name
            $guest_id = $this->mUserModel->getGuestIDByUserId($booking['user_id']);
            $guest = $this->mUserModel->getGuestData($guest_id);

            if (empty($guest))
                return;

            $fcm_id = $guest['fcm_id'];
            $fcm_platform = $guest['platform'];


            //send notification to the user
            $this->load->model("notification/notification_model", "mNotificationModel");
            $this->mNotificationModel->sendCustomNotification($fcm_platform, $notif_body, $store_name,  $fcm_id);


            //send notification to the email
            $this->mMailer->sendSimpleNotification($booking['user_id'], $notif_body );


            return array(Tags::SUCCESS=>1);

        }

        return array(Tags::SUCCESS=>0);
    }


    public function update_fields(){

        if (!$this->db->field_exists('book', 'store')) {
            $fields = array(
                'book' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            @$this->dbforge->add_column('store', $fields);
        }
    }

    public function create_table(){


        $this->load->dbforge();

        $fields = array(

            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'store_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'cf_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'cf_data' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'cart' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'status' => array(
                'type' => 'INT',
                'default' => 0
            ),

            'updated_at' => array(
                'type' => 'DATETIME'
            ),

            'created_at' => array(
                'type' => 'DATETIME'
            ),

        );

        $this->dbforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('booking', TRUE, $attributes);



    }


}


<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct(){
        parent::__construct();
        //load model

    }


    public function signIn()
    {

        if(reCAPTCHA==TRUE){
            $response =  MyCurl::run("https://www.google.com/recaptcha/api/siteverify",array(
                'secret'    => '6Ld6s4QUAAAAAKKWRIkFKdFU946U3uHOdNhxiG3n',
                'remoteip'  => $this->input->ip_address(),
                'response'  => RequestInput::post('recaptcha_response')
            ));

            $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

            if(isset($response['success']) and $response['success']==false){
                echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                    "error"=>"reCAPTCHA invalid! ".json_encode($response),
                )));
                return;
            }
        }

        //$this->load->model("User/mUserModel");
        $errors = array();

        $login = Security::decrypt(RequestInput::post("login"));
        $password = Security::decrypt(RequestInput::post("password"));
        $token = Security::decrypt(RequestInput::post("token"));

        $params = array(
            "login" => $login,
            "password" => $password,
            "user_language" => Translate::getDefaultLang()
        );


        $data = $this->mUserModel->signIn($params);


        if(isset($data[Tags::SUCCESS]) && $data[Tags::SUCCESS]==1){


            if (isset($data[Tags::RESULT][0])){

                $user = $data[Tags::RESULT][0];

                $callback_user_login_redirection
                    = $this->session->userdata('callback_user_login_redirection');

                if($callback_user_login_redirection!="")
                    $data['url'] = $callback_user_login_redirection;

                if(!GroupAccess::isGrantedUser($user['id_user'],'user',DASHBOARD_ACCESSIBILITY)){

                    $err = Messages::USER_ACCOUNT_ISNT_BUSINESS;

                    if(ModulesChecker::isEnabled("pack")){
                        $this->session->set_userdata(array(
                            'up_acc_user_id' => $user['id_user']
                        ));


                        $this->session->set_userdata(array(
                            "business_manager_callback" => admin_url("business_manager/businesses")
                        ));

                        $err = Messages::USER_ACCOUNT_ISNT_BUSINESS_2.", "."<a href='".site_url("pack/pickpack")."'>".Translate::sprint("Upgrade your account")."</a>";

                        echo json_encode(
                            array(
                                Tags::SUCCESS => -1,
                                Tags::ERRORS => array(
                                    "err"   => $err
                                ),
                                "url"=>site_url("pack/pickpack")
                            )
                        );
                        return;


                    }

                    echo json_encode(
                        array(
                            Tags::SUCCESS => 0,
                            Tags::ERRORS => array(
                                "err"   => $err
                            )
                        )
                    );
                    return;

                }
                //save the session
                if (isset($data[Tags::RESULT][0])){
                    $this->mUserBrowser->setUserData($data[Tags::RESULT][0]);
                    $this->session->set_userdata(array(
                        "savesession"=>array()
                    ));
                }



            }
        }


        echo json_encode(
            $data
        );
        return;

    }


    public function getStores(){

        $params = array(
            "limit"   => 5,
            "search"  => RequestInput::get('search'),
            "user_id"  => SessionManager::getData("id_user"),
            "status"  => 1
        );


        $data = $this->mStoreModel->getStores($params);

        $result = array();

        if(isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object){
                $o = array(
                    'name' =>  Text::output($object['name']),
                    'id' =>  $object['id_store'],

                    'lat' =>  $object['latitude'],
                    'lng' =>  $object['longitude'],
                    'address' =>  $object['address'],
                );
                $result[] = $o;
            }

        echo json_encode($result);return;

    }

    public function load_component(){

        $path = RequestInput::post("path");
        $module = RequestInput::post("module");
        $data = array(
            "module"=>$module
        );

        $html = $this->load->view($path,$data,TRUE);
        $script = AdminTemplateManager::loadScripts();
        echo json_encode(array(Tags::SUCCESS=>1,"html"=>$html,"script"=>$script));

    }


    public function my_booking(){

        $result = $this->mBookingModel->getBookings(array(
            "id" => RequestInput::post("booking_id"),
            "owner_id" => SessionManager::getData("id_user"),
            "limit" => intval(RequestInput::post("limit")),
            "page" => intval(RequestInput::post("page")),
            "except" => RequestInput::post("except"),
            "order_by" => RequestInput::post("order_by"),
        ));

        $html = "";

        $loaded_items = 0;
        foreach ($result[Tags::RESULT] as $object){
            $data['object'] = $object;
            $html .= $this->load->view("business_manager/booking/item-list",$data,TRUE);
            $loaded_items++;
        }

        echo json_encode(array(
            Tags::SUCCESS=>1,
            'html'=>$html,
            'pagination'=>$result[Tags::PAGINATION],
            'loaded_items'=>$loaded_items,
        ));return;

    }

    public function my_stores(){

        $result = $this->mStoreModel->getStores(array(
            'limit' => 20,
            'page' => intval(RequestInput::post('page')),
            'search' => trim(RequestInput::post('q')),
            'store_id' => trim(RequestInput::post('id')),
            "user_id"  => SessionManager::getData("id_user"),
            "order_by" => "recent"
        ));

        $html = "";

        $loaded_items = 0;
        foreach ($result[Tags::RESULT] as $object){
            $data['object'] = $object;
            $html .= $this->load->view("business_manager/store/item-list",$data,TRUE);
            $loaded_items++;
        }

        echo json_encode(array(
            Tags::SUCCESS=>1,
            'html'=>$html,
            'pagination'=>$result[Tags::PAGINATION],
            'loaded_items'=>$loaded_items,
        ));return;

    }


    public function my_offers(){

        $result = $this->mOfferModel->getOffers(array(
            'limit' => 20,
            'page' => intval(RequestInput::post('page')),
            'search' => trim(RequestInput::post('q')),
            "user_id"  => SessionManager::getData("id_user"),
            "order_by" => "recent"
        ));

        $html = "";

        $loaded_items = 0;
        foreach ($result[Tags::RESULT] as $object){
            $data['object'] = $object;
            $html .= $this->load->view("business_manager/offer/item-list",$data,TRUE);
            $loaded_items++;
        }

        echo json_encode(array(
            Tags::SUCCESS=>1,
            'html'=>$html,
            'pagination'=>$result[Tags::PAGINATION],
            'loaded_items'=>$loaded_items,
        ));return;

    }


    public function my_events(){

        $result = $this->mEventModel->getEvents(array(
            'limit' => 20,
            'page' => intval(RequestInput::post('page')),
            'search' => trim(RequestInput::post('q')),
            "user_id"  => SessionManager::getData("id_user"),
            "order_by" => "recent"
        ));

        $html = "";

        $loaded_items = 0;
        foreach ($result[Tags::RESULT] as $object){
            $data['object'] = $object;
            $html .= $this->load->view("business_manager/event/item-list",$data,TRUE);
            $loaded_items++;
        }

        echo json_encode(array(
            Tags::SUCCESS=>1,
            'html'=>$html,
            'pagination'=>$result[Tags::PAGINATION],
            'loaded_items'=>$loaded_items,
        ));return;

    }



}
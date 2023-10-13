<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct()
    {
        parent::__construct();

        $this->load->model("offer/offer_model","mOfferModel");
        $this->load->model("store/store_model","mStoreModel");
        $this->load->model("user/user_model","mUserModel");
        $this->load->model("user/user_browser","mUserBrowser");
    }

    public function getOffersAjax(){


        $params = array(
            "limit"   => 5,
            "store_id" => RequestInput::get('store_id'),
            "search"  => RequestInput::get('search'),
            "user_id"  => $this->mUserBrowser->getData('id_user'),
            "status"  => 1
        );

        if(SessionManager::getData("manager") == 1){
            unset($params['user_id']);
        }

        $data = $this->mOfferModel->getOffers($params);

        $result = array();

        if(isset($data[Tags::RESULT]))
            foreach ($data[Tags::RESULT] as $object){

                $o = array(
                    'text' =>  Text::output($object['name']).' ('.Text::output($object['store_name']).')',
                    'id' =>  $object['id_offer'],

                    'title' =>  $object['name'],
                    'description' =>  strip_tags(Text::output($object['description'])),
                    'image' =>  ImageManagerUtils::getFirstImage( $object['images']),
                );

                if(strlen($o['description'])>100){
                    $o['description'] = substr(strip_tags(Text::output($o['description'])),0,100).' ...';
                }

                $result['results'][] = $o;

            }

        echo json_encode($result,JSON_OBJECT_AS_ARRAY);return;
    }

    public function markAsFeatured(){

        //check if user have permission
        $this->enableDemoMode();

        if(!GroupAccess::isGranted('offer',MANAGE_OFFERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $user_id = $this->mUserBrowser->getData("user_id");

            $id   = intval(RequestInput::post("id"));
            $featured   = intval(RequestInput::post("featured"));

            echo json_encode(
                $this->mOfferModel->markAsFeatured(array(
                    "user_id"  => $user_id,
                    "id" => $id,
                    "featured" => $featured

                ))
            );
            return;

        }

        echo json_encode(array(Tags::SUCCESS=>0));
    }

    public function delete(){

        if(!GroupAccess::isGranted('offer',DELETE_OFFER)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mOfferModel->deleteOffer(
                array( "offer_id" => intval(RequestInput::post("id")))
            );

            echo json_encode($data);

        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
        }

    }

    public function changeStatus(){

        if(!GroupAccess::isGranted('offer',MANAGE_OFFERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        if($this->mUserBrowser->isLogged()){

            $data = $this->mOfferModel->changeStatus(
                array( "offer_id" => intval(RequestInput::get("id")))
            );

            echo json_encode($data);
            exit();

        }

    }



    public function add(){


        if(!GroupAccess::isGranted('offer',ADD_OFFER)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $store_id = RequestInput::post("store_id");
        $description =  RequestInput::post("description",FALSE);
        $price =  RequestInput::post("price");
        $percent =  RequestInput::post("percent");
        $date_start =  RequestInput::post("date_start");
        $date_end =  RequestInput::post("date_end");
        $name =  RequestInput::post("name",FALSE);
        $user_id =  intval($this->mUserBrowser->getData("id_user"));
        $authType =  ($this->mUserBrowser->getData("typeAuth"));
        $images =  RequestInput::post("images");
        $currency =  RequestInput::post("currency");
        $is_deal =  RequestInput::post("is_deal");
        $order_enabled =  RequestInput::post("order_enabled");
        $order_cf_id =  RequestInput::post("order_cf_id");
        $button_template =  RequestInput::post("button_template");

        $offer_coupon_config_type =  RequestInput::post("offer_coupon_config_type");
        $offer_coupon_config_limit =  RequestInput::post("offer_coupon_config_limit");
        $offer_coupon_code =  RequestInput::post("offer_coupon_code");


        $params = array(
            "store_id" => $store_id,
            "description" => $description,
            "price" => $price,
            "percent" => $percent,
            "date_start" => $date_start,
            "date_end" => $date_end,
            "user_id" => $user_id,
            "user_type" => $authType,
            "name" => $name,
            "images" => $images,
            "currency"=> $currency,
            "is_deal"=> $is_deal,
            "order_enabled"=> $order_enabled,
            "order_cf_id"=> $order_cf_id,
            "button_template"=> $button_template,
            "typeAuth"  => $this->mUserBrowser->getData("typeAuth"),
            "offer_coupon_config_type"=> $offer_coupon_config_type,
            "offer_coupon_config_limit"=> $offer_coupon_config_limit,
            "offer_coupon_code"=> $offer_coupon_code,
        );

        echo json_encode(
            $this->mOfferModel->addOffer($params)
        );return;

    }


    public function edit(){

        if(!GroupAccess::isGranted('offer',EDIT_OFFER)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $store_id = RequestInput::post("store_id");
        $offer_id = RequestInput::post("offer_id");
        $description =  RequestInput::post("description",FALSE);
        $price =  RequestInput::post("price");
        $percent =  RequestInput::post("percent");
        $name =  RequestInput::post("name",FALSE);
        $user_id =  intval($this->mUserBrowser->getData("id_user"));
        $images =  RequestInput::post("images");

        $currency =  RequestInput::post("currency");
        $date_end =  RequestInput::post("date_end");
        $date_start =  RequestInput::post("date_start");

        $is_deal =  RequestInput::post("is_deal");

        $order_enabled =  RequestInput::post("order_enabled");
        $order_cf_id =  RequestInput::post("order_cf_id");
        $button_template =  RequestInput::post("button_template");

        $offer_coupon_config_type =  RequestInput::post("offer_coupon_config_type");
        $offer_coupon_config_limit =  RequestInput::post("offer_coupon_config_limit");
        $offer_coupon_code =  RequestInput::post("offer_coupon_code");




        $params = array(

            "store_id" => $store_id,
            "offer_id" => $offer_id,
            "description" => $description,
            "price" => $price,
            "date_end" => $date_end,
            "date_start" => $date_start,
            "percent" => $percent,
            "user_id" => $user_id,
            "images" => $images,
            "name" => $name,
            "currency"=> $currency,
            "is_deal"=> $is_deal,

            "order_enabled"=> $order_enabled,
            "order_cf_id"=> $order_cf_id,

            "button_template"=> $button_template,

            "offer_coupon_config_type"=> $offer_coupon_config_type,
            "offer_coupon_config_limit"=> $offer_coupon_config_limit,
            "offer_coupon_code"=> $offer_coupon_code,

        );


        echo  json_encode(
            $this->mOfferModel->editOffer($params)
        );

    }




}
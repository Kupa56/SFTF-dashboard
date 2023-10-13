<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {



    public function __construct(){
        parent::__construct();
        //load model

        $this->load->model("user/user_model","mUserModel");
        $this->load->model("user/user_browser","mUserBrowser");
        $this->load->model("campaign/campaign_model","mCampaignModel");

        $this->load->model("offer/offer_model","mOfferModel");
        $this->load->model("event/event_model","mEventModel");
        $this->load->model("store/store_model","mStoreModel");



    }

    public function notification_agreement(){


        $bookmark_id = Security::decrypt(RequestInput::post("bookmark_id"));
        $user_id = Security::decrypt(RequestInput::post("user_id"));
        $agreement = Security::decrypt(RequestInput::post("agreement"));

        $result = $this->mCampaignModel->updateNotificationAgreement($bookmark_id,$user_id,$agreement);

        if ($result){
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }

    }

    public function markView(){

        $campaignId = Security::decrypt(RequestInput::post("cid"));

        $guest_id = Security::decrypt(RequestInput::post("guest_id"));
        $user_id = Security::decrypt(RequestInput::post("user_id"));


        $params = array(
            "campaignId"  => intval($campaignId),
            "guest_id"      => intval($guest_id),
            "user_id"      => intval($user_id)
        );

        echo json_encode($this->mCampaignModel->markView($params));

    }


    public function markReceive(){

        $campaignId = Security::decrypt(RequestInput::post("cid"));
        $guest_id = Security::decrypt(RequestInput::post("guest_id"));
        $user_id = Security::decrypt(RequestInput::post("user_id"));

        $params = array(
            "campaignId"  => intval($campaignId),
            "guest_id"      => intval($guest_id),
            "user_id"      => intval($user_id)
        );

        echo json_encode($this->mCampaignModel->markReceive($params));

    }

    



}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct(){
        parent::__construct();
        //load model

        $this->load->model("user/user_model","mUserModel");
        $this->load->model("user/user_browser","mUserBrowser");
        $this->load->model("campaign/campaign_model","mCampaignModel");


    }

    public function saveCampaignConfig(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $RADUIS_TRAGET = RequestInput::post("RADUIS_TRAGET");
        $LIMIT_PUSHED_GUESTS_PER_CAMPAIGN = RequestInput::post("LIMIT_PUSHED_GUESTS_PER_CAMPAIGN");
        $PUSH_CAMPAIGNS_WITH_CRON = RequestInput::post("PUSH_CAMPAIGNS_WITH_CRON");
        $NBR_PUSHS_FOR_EVERY_TIME = RequestInput::post("NBR_PUSHS_FOR_EVERY_TIME");
        $_NOTIFICATION_AGREEMENT_USE = RequestInput::post("_NOTIFICATION_AGREEMENT_USE");

        ConfigManager::setValue("RADUIS_TRAGET",$RADUIS_TRAGET);
        ConfigManager::setValue("LIMIT_PUSHED_GUESTS_PER_CAMPAIGN",$LIMIT_PUSHED_GUESTS_PER_CAMPAIGN);
        ConfigManager::setValue("PUSH_CAMPAIGNS_WITH_CRON",$PUSH_CAMPAIGNS_WITH_CRON);
        ConfigManager::setValue("NBR_PUSHS_FOR_EVERY_TIME",$NBR_PUSHS_FOR_EVERY_TIME);
        ConfigManager::setValue("_NOTIFICATION_AGREEMENT_USE",$_NOTIFICATION_AGREEMENT_USE);

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }


    public function testPush(){

        if(!GroupAccess::isGranted('campaign',PUSH_CAMPAIGNS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $module_name = RequestInput::post("module_name");
        $guest_ids = RequestInput::post("guest_ids");

        $result = $this->mCampaignModel->testPush($module_name,$guest_ids);

        echo json_encode($result); return;

    }

    public function archiveCampaign(){

        if(!GroupAccess::isGranted('campaign',DELETE_CAMPAIGNS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        if($this->mUserBrowser->isLogged()){

            $data = $this->mCampaignModel->archiveCampaign(
                array( "campaign_id" => RequestInput::get("id"))
            );

            if($data[Tags::SUCCESS]==1){
                echo json_encode($data);
            }

        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
        }

    }

    public function duplicateCampaign(){

        if(!GroupAccess::isGranted('campaign',PUSH_CAMPAIGNS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        if($this->mUserBrowser->isLogged()){

            $data = $this->mCampaignModel->duplicateCampaign(
                array( "campaign_id" => RequestInput::get("id"))
            );

            if($data[Tags::SUCCESS]==1){
                echo json_encode($data);
            }

        }else{
            echo json_encode(array(Tags::SUCCESS=>0));
        }

    }

    public function getCampaigns($params=array()){

        $vToExtract = array_key_whitelist($params, [
            'module_name',
            'module_id',
            'page',
            'status',
            'owner'
        ]);
        extract($vToExtract,EXTR_SKIP);

        $params = array(
            "module_name"  => $module_name,
            "module_id"  => $module_id,
            "page"  => $page,
            "status"  => $status,
            "user_id" =>  intval($this->mUserBrowser->getData("id_user"))
        );

        return $this->mCampaignModel->getCampaigns($params);

    }

    public function getEstimation(){

        $custom_parameters =  RequestInput::post("custom_parameters");
        $module_name =  RequestInput::post("module_name");
        $module_id =  RequestInput::post("module_id");
        $user_id =  intval($this->mUserBrowser->getData("id_user"));

        $params = array(
            "user_id" => $user_id,
            "module_id" => $module_id,
            "module_name" => $module_name,
            "custom_parameters" => $custom_parameters
        );


        echo json_encode(
            $this->mCampaignModel->getEstimation($params)
        );return;

    }

    public function createCampaign(){


        if(!GroupAccess::isGranted('campaign',PUSH_CAMPAIGNS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $custom_parameters =  RequestInput::post("custom_parameters");
        $module_name =  RequestInput::post("module_name");
        $module_id =  RequestInput::post("module_id");

        $custom_title =  RequestInput::post("campaign_name");
        $custom_text =  RequestInput::post("campaign_text");


        $t =  RequestInput::post("t");
        $user_id =  intval($this->mUserBrowser->getData("id_user"));

        $params = array(
            "user_id" => $user_id,
            "custom_title"       => strip_tags($custom_title),
            "custom_text"       => strip_tags($custom_text),
            "module_id" => $module_id,
            "module_name" => $module_name,
            "custom_parameters" => json_decode($custom_parameters,JSON_OBJECT_AS_ARRAY),
            "t" => $t,
        );


        echo json_encode(
            $this->mCampaignModel->createCampaign($params)
        );return;

    }

    public  function send_notification ($message,$tokens)
    {

        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = array(
            'registration_ids' => $tokens,
            'data' =>array("message" => $message)
        );
        $headers = array(
            'Authorization:key='.Keys::PUSH_NOTIFICATION_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return $result;

    }


}
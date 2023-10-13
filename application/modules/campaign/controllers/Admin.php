<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::requireEnabled("campaign");

    }


    public function getCampaigns($params=array()){


        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $vToExtract = array_key_whitelist($params, [
            'type',
            'module_id',
            'page',
            'status',
            'owner',
            'limit',
            'campaign_id',
        ]);
        extract($vToExtract,EXTR_SKIP);

        $params = array(
            "type"  => $type,
            "module_id"  => $module_id,
            "page"  => $page,
            "status"  => $status,
            "limit"  => $limit,
            "campaign_id"  => $campaign_id,
        );


        if(!GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS) || (isset($owner) and $owner==1)){
            $params['user_id'] =  intval($this->mUserBrowser->getData("id_user"));
        }


        return $this->mCampaignModel->getCampaigns($params);

    }

    private function getStatusByAction(){

        $action = RequestInput::get('action');

        switch ($action){
            case "all_campaigns":
                return 0;
            case "my_campaigns":
                return -2;
            case "pushed_campaigns":
                return 1;
            case "completed_campaigns":
                return 2;
            case "pending_campaigns":
                return -1;
        }

        return 0;
    }

    public function campaigns(){

        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();

        $cid =intval(RequestInput::get("push"));

        if($cid>0 && GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            $this->mCampaignModel->validateAndPushCampaign($cid);
            redirect(admin_url("campaign/campaigns"));
        }

        $status = $this->getStatusByAction();

        if($status == -2){
            $data['campaigns'] =  $this->getCampaigns(array(
                "page" => RequestInput::get("page"),
                "owner" => SessionManager::getData('id_user'),
            ));
        }else{
            $data['campaigns'] =  $this->getCampaigns(array(
                "page" => RequestInput::get("page"),
                "status" => intval($status)
            ));
        }


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("campaign/backend/html/list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function report(){


        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();

        $cid = intval(RequestInput::get("id"));

        $params = array(
          'limit'=>1,
           'campaign_id'=>$cid,
        );

        if(GroupAccess::isGranted('campaign') && !GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            $params['owner'] = SessionManager::getData('id_user');
        }

        $result = $this->getCampaigns($params);

        if(!isset($result[Tags::RESULT][0]))
            redirect("error404");

        $data['campaign'] = $result[Tags::RESULT][0];


        $data['report_last_week'] = $this->mCampaignModel->getCampaignReport($result[Tags::RESULT][0]['id'],Campaign_model::WEEK);
        $data['last_week'] = $this->mCampaignModel->getLastWeekD();


        $data['report_last_month'] = $this->mCampaignModel->getCampaignReport($result[Tags::RESULT][0]['id'],Campaign_model::MONTH);
        $data['last_month'] = $this->mCampaignModel->getLastMonthD();


        $data['report_last_24h'] = $this->mCampaignModel->getCampaignReport24($result[Tags::RESULT][0]['id']);
        $data['last_24h'] = $this->mCampaignModel->getLast24hD();


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("campaign/backend/html/report");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function create(){

        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();

        $cid =intval(RequestInput::get("push"));

        if($cid>0 && GroupAccess::isGranted('campaign',MANAGE_CAMPAIGNS)){
            $this->mCampaignModel->validateAndPushCampaign($cid);
            redirect(admin_url("campaign/campaigns"));
        }

        $data['campaigns'] =  $this->getCampaigns(array(
            "page" => RequestInput::get("page"),
            "status" => intval(RequestInput::get("status")),
            "owner" => intval(RequestInput::get("owner")),
        ));

        // css
        $libcssdp = AdminTemplateManager::assets("campaign", "css/style.css");
        AdminTemplateManager::addCssLibs($libcssdp);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("campaign/backend/html/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function campaign_config(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        AdminTemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("campaign/backend/html/campaign_config");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function campaign()
    {

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("campaign/backend/campaign");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function logs()
    {

        if(SessionManager::getData('manager')==0)
            redirect("error?page=permission");

        if (!GroupAccess::isGranted('campaign'))
            redirect("error?page=permission");

        $data = array();
        $cid = intval(RequestInput::get("id"));
        $unpushed = intval(RequestInput::get("unpushed"));

        if($unpushed == 1){
            $data['result'] = $this->mCampaignModel->retrieveLogs($cid,TRUE);
        }else{
            $data['result'] = $this->mCampaignModel->retrieveLogs($cid);
        }


        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("campaign/backend/html/logs",$data);
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }






}

/* End of file CampaignDB.php */
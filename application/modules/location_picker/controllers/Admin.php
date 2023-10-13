<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::requireEnabled("location_picker");

    }



    public function config(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        AdminTemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("location_picker/backend/html/config");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }







}

/* End of file CampaignDB.php */
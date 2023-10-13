<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();


          ModulesChecker::requireEnabled("setting");


    }


    public function index()
	{


	}


    public function messagesToTranslate(){

        echo "You need navigate in the website to save all messages<br><br>";

        if(isset($_SESSION['toTranslate'])){
            foreach ($_SESSION['toTranslate'] as $key => $item) {
                echo $key.": $item<br>";
            }
        }

    }

    public function translate(){

        $uri = $this->uri->segment(3);

        if($this->mUserBrowser->isLogged()) {

            if($uri==""){

                $data['default_language'] = Translate::loadLanguageFromYmlToTranslate(
                    RequestInput::get("language")
                );

                $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
                $this->load->view("setting/backend/application/translate");
                $this->load->view(AdminPanel::TemplatePath."/include/footer");

            }else if($uri=="android"){


            }

        }
    }

    public function currencies(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',MANAGE_CURRENCIES))
            redirect("error?page=permission");


        AdminTemplateManager::set_settingActive('currencies');

        $data['currencies'] = $this->mCurrencyModel->getAllCurrencies();
        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("setting/backend/html/currency");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function deeplinking(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting'))
            redirect("error?page=permission");


        AdminTemplateManager::set_settingActive('deeplinking');


        $data = array();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("setting/backend/html/deeplinking");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function api_config(){

        /*
        *  CHECK USER PEMISSIONS
        */



        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        AdminTemplateManager::set_settingActive('application');
        $data['config'] = $this->mConfigModel->getParams();



        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("setting/backend/html/api_config");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function application(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        AdminTemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();
        $data['components'] = SettingViewer::loadComponent();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("setting/backend/html/config");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }
    
    public function global_options(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        AdminTemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();
        $data['components'] = SettingViewer::loadComponent();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("setting/backend/html/config");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }
    public function app_config_xml(){

        if (!GroupAccess::isGranted('setting'))
            redirect("error?page=permission");

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("setting/backend/application/app_config_xml");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function logs(){

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("setting/backend/html/logs");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function edit_timezone(){

        $c = RequestInput::get('c');
        $tz = RequestInput::get('tz');

        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        if(in_array($tz,$timezones)){
            $this->mUserModel->save_user_subscribe_setting('user_timezone',$tz);
        }

        //refresh session
        $this->mUserBrowser->refreshData(
            $this->mUserBrowser->getData('id_user')
        );

        $c = base64_decode($c);
        redirect($c);

    }


    public function clearAll(){


        if(GroupAccess::isGranted('setting')){

            $started_id = 596;
            $this->db->where('id_store >',$started_id);
            $this->db->select('id_store');
            $stores = $this->db->get('store');
            $stores = $stores->result();

            foreach ($stores as $store){
                $this->mStoreModel->delete($store->id_store);
            }

            echo count($stores)." was removed";


        }
    }


    public function cronjob(){

        if (!GroupAccess::isGranted('setting',MANAGE_CURRENCIES))
            redirect("error?page=permission");

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("setting/backend/html/cronjob");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");
    }





}

/* End of file SettingDB.php */
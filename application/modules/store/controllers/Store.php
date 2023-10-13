<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine
 * Date: {date}
 * Time: {time}
 */

class Store extends MAIN_Controller {

    public function __construct(){
        parent::__construct();
        /////// register module ///////
        $this->init("store");

    }

    public function onLoad()
    {

        define('ADD_STORE','add');
        define('EDIT_STORE','edit');
        define('DELETE_STORE','delete');
        define('MANAGE_STORES','manage_stores');

        define('DISPLAY_RECENTLY_ADDED','recentlyAdded');
        define('KS_NBR_STORES','nbr_stores');

        $this->load->model("store/store_model","mStoreModel");
        $this->load->helper("store/store");
    }

    public function onCommitted($isEnabled)
    {
        if(!$isEnabled)
            return;

        ConfigManager::setValue("DATE_FORMAT","24",TRUE);
        ConfigManager::setValue("OPENING_TIME_ENABLED",TRUE,TRUE);
        ConfigManager::setValue("SETTING_AUTO_LOC_DETECT",TRUE,TRUE);
        ConfigManager::setValue("ANYTHINGS_APPROVAL",TRUE,TRUE);

        //Setup User Config
        AdminTemplateManager::registerMenu(
            'store',
            "store/menu",
            1
        );


        UserSettingSubscribe::set('store',array(
            'field_name' => KS_NBR_STORES,
            'field_type' => UserSettingSubscribeTypes::INT,
            'field_default_value' => -1,
            'config_key' => 'NBR_STORES',
            'field_label' => 'Number stores allowed',
            'field_comment' => '',
            'field_sub_label' => '( -1 Unlimited )',
        ));

        if($this->mUserBrowser->isLogged() && GroupAccess::isGranted('store')){

            SimpleChart::add('store','chart_v1_home',function ($months){

                if(GroupAccess::isGranted('store',MANAGE_STORES)){
                    return $this->mStoreModel->getStoresAnalytics($months);
                }else{
                    return $this->mStoreModel->getStoresAnalytics($months,$this->mUserBrowser->getData('id_user'));
                }

            });
        }


        $this->generateViewHomePage();


        //User action listener
        ActionsManager::register('user','user_switch_to',function ($args){
            $this->mStoreModel->switchTo($args['from'], $args['to']);
        });


        //register store to campaign program
        CampaignManager::register(array(
            'module' => $this,
            'api'    => site_url('ajax/store/getStoresAjax'),
            'callback_input' => function($args){
                return $this->mStoreModel->campaign_input($args);
            },
            'callback_output' => function($args){
                return $this->mStoreModel->campaign_output($args);
            },

            'custom_parameters' => array(
                'html' => $this->load->view('store/backend/campaign/html',array('module'=>'store'),TRUE),
                'script' => $this->load->view('store/backend/campaign/script',array('module'=>'store'),TRUE),
                'var' => "store_custom_parameters",
            )
        ));

        //register setting component
        $this->registerSetting();

        //register upload clear folder
        $this->onClearUploadFolder();

        //disable active stores
        $this->disableStoresCallback();

    }


    private function disableStoresCallback(){

        ActionsManager::register("user","userShutDown",function($user_id){
            //disable all active stores
            return $this->mStoreModel->disableActiveStores($user_id);
        });

    }


    private function onClearUploadFolder()
    {
        ActionsManager::register("uploader","onClearFolder",function(){
            //get all active images
            return $this->mStoreModel->getAllActiveImages();
        });
    }

    private function registerSetting(){

        //register component for setting viewer
        SettingViewer::register("store","store/setting_viewer/html",array(
            'title' => _lang("Store & Location"),
        ));

    }

    private function generateViewHomePage(){

        CMS_Display::setHTML(
            "widget_bottom",
            "<div class=\"row\">"
        );

        CMS_Display::set(
            "widget_bottom",
            "store/backend/recently_added/recently_stores_added"
        );

        CMS_Display::set(
            "widget_bottom",
            "store/backend/recently_added/recently_reviews_added"
        );

        CMS_Display::setHTML(
            "widget_bottom",
            "</div>"
        );
    }

    private function registerModuleActions(){

        GroupAccess::registerActions("store",array(
            ADD_STORE,
            EDIT_STORE,
            DELETE_STORE,
            MANAGE_STORES
        ));

    }


    private function init_create_checkout(){

        $pdc_cf_id = intval(ConfigManager::getValue("store_default_checkout_cf"));

        if($pdc_cf_id == 0){
            $pdc_cf_id = $this->mStoreModel->create_default_checkout_fields();
            if($pdc_cf_id>0){
                ConfigManager::setValue("store_default_checkout_cf",intval($pdc_cf_id));
            }
        }

    }

    public function index(){

    }


    public function id(){

        $this->load->library('user_agent');

        $id = intval($this->uri->segment(3));

        if($id==0)
            redirect("?err=1");

        $platform =  $this->agent->platform();

        if(/*Checker::user_agent_exist($user_agent,"ios")*/ strtolower($platform)=="ios"){

            $link = site_url("store/id/$id");
            $link = str_replace('www.', '', $link);
            $link = str_replace('http://', 'nsapp://', $link);
            $link = str_replace('https://', 'nsapp://', $link);

            $this->session->set_userdata(array(
               "redirect_to" =>  $link
            ));

            redirect("");
        }

        redirect("");

    }

    public function onEnable()
    {
        $this->registerModuleActions();

        $this->init_create_checkout();

    }

    public function onUpgrade()
    {
        // TODO: Implement onUpgrade() method.
        parent::onUpgrade();
        $this->mStoreModel->addOpeningTimeTable();
        $this->mStoreModel->updateFields();
        $this->mStoreModel->add_store_country_field();

        $this->registerModuleActions();

        $this->init_create_checkout();


        return TRUE;

    }

    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub
        $this->mStoreModel->addOpeningTimeTable();
        $this->mStoreModel->updateFields();
        $this->mStoreModel->add_store_country_field();

        $this->registerModuleActions();

        $this->init_create_checkout();

        return TRUE;

    }


}

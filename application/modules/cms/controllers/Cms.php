<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Cms extends MAIN_Controller {

    public function __construct(){
        parent::__construct();
        $this->init("cms");
    }



    public function onLoad()
    {

        /////// init module ///////
        $this->load->helper('cms/charts');
        $this->load->helper('cms/CMS');
        $this->load->helper('cms/CMSUtils');
        $this->load->helper('cms/CMSSession');

        $this->load->library('parser');
        $this->load->model('Cms/Cms_model','mCMS');

        //init hook
        CMS_Display::createHook("overview_chart_months");
        CMS_Display::createHook("overview_counter");

        CMS_Display::createHook("widget_top");
        CMS_Display::createHook("widget_middle");
        CMS_Display::createHook("widget_bottom");

        //init charts
        SimpleChart::init("chart_v1_home");


        define('MANAGE_PAGES','manage_pages');
        define('MANAGE_MENU','manage_menu');


    }

    public function onCommitted($isEnabled)
    {

        if(!$isEnabled)
            return;

        if(!ModulesChecker::isEnabled("webapp")){
            $this->registerSetting("Mobile information");
            return;
        }


        AdminTemplateManager::registerMenu(
            'cms',
            "cms/menu",
            24
        );

        //register setting component
        $this->registerSetting();


    }


    private function registerSetting($title="Mobile & WebApp information"){

        //include css
        $libcssdp = AdminTemplateManager::assets("cms", "css/template-manager.css");
        AdminTemplateManager::addCssLibs($libcssdp);

        //register component for setting viewer
        SettingViewer::register("cms","cms/setting_viewer/html",array(
            'title' => _lang($title),
        ));

    }

    public function error404(){

        if($this->mUserBrowser->isLogged()){
            $this->load->view(AdminPanel::TemplatePath."/include/header");
            $this->load->view(AdminPanel::TemplatePath."/error404");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");
        }else{
            redirect(site_url("user/login"));
        }

    }

    public function onInstall()
    {

        //create table
        $this->mCMS->createTables();
        //update fields
        $this->mCMS->updateFields();

        //generate grp
        GroupAccess::registerActions("cms",array(
            MANAGE_PAGES,
            MANAGE_MENU,
        ));

        //create or update webapp permission
        $this->mCMS->createPermission(array(
            "payment" => array(
                "display_billing" => TRUE
            ),
            "user" => array(
                "dashboard_accessibility" => TRUE
            ),
            "messenger" => array(
                "send_and_receive" => TRUE
            ),
            "booking" => array(
                GRP_MANAGE_MY_BOOKING => TRUE
            ),
        ),FALSE);


        //update translation
        Translate::updateLanguages("cms");


        return TRUE;
    }

    public function onUpgrade()
    {

        //create table if needed
        $this->mCMS->createTables();
        //update fields
        $this->mCMS->updateFields();

        //generate grp
        GroupAccess::registerActions("cms",array(
            MANAGE_PAGES,
            MANAGE_MENU,
        ));


        //create or update webapp permission
        $this->mCMS->createPermission(array(
            "payment" => array(
                "display_billing" => TRUE
            ),
            "user" => array(
                "dashboard_accessibility" => TRUE
            ),
            "messenger" => array(
                "send_and_receive" => TRUE
            ),
            "booking" => array(
                GRP_MANAGE_MY_BOOKING => TRUE
            ),
        ),TRUE);

        //update translation
        Translate::updateLanguages("cms");

        return TRUE;
    }

    public function onEnable()
    {

        return TRUE;
    }


}

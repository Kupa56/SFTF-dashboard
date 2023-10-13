<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();


        ModulesChecker::requireEnabled("cms");
    }

    public function recompileTranslate(){

        //check new version and do updates
        $config = TemplateUtils::getCurrentTemplate();
        TemplateLanguageUtils::recompileTemplateTranslate($config['templateId']);

        //redirect
        redirect(admin_url("webapp/template"));

    }

    public function updateTemplate(){

        //check new version and do updates
        $this->mCMS->checkAndUpdateTemplate();

        //redirect
        redirect(admin_url("webapp/template"));

    }

    public function installTemplate(){

        //install template
        $this->mCMS->installTemplate(ConfigManager::getValue("DEFAULT_TEMPLATE"));

        //redirect
        redirect(admin_url("webapp/template"));

    }



    public function home(){

        if(!$this->mUserBrowser->isLogged()){
            redirect(site_url("user/login"));
        }

        $myData['chart_v1_home'] = SimpleChart::get('chart_v1_home');

        if(!empty($myData['chart_v1_home'])){

            CMS_Display::set(
                "widget_top",
                "cms/backend/charts/counter_v1",
                $myData
            );

            CMS_Display::set(
                "widget_top",
                "cms/backend/charts/charts_v1",
                $myData
            );

        }

        $data = array();


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cms/backend/html/home");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }



    public function managePages(){

        if (!GroupAccess::isGranted('cms')  )
            redirect("error?page=permission");

        $data['title'] = _lang("Manage Pages");

        $data['result'] = $this->mCMS->getPages(array(
            'limit' => 30,
            'page' => intval(RequestInput::get('page')),
            'q' => RequestInput::get('q'),
        ));


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cms/backend/html/manage-pages");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function edit(){

        if (!GroupAccess::isGranted('cms')  )
            redirect("error?page=permission");

        $result = $this->mCMS->getPages(array(
            'id' => intval(RequestInput::get("id"))
        ));

        if(!isset($result[Tags::RESULT][0])){
            redirect("error404");
        }

        $data['title'] = Translate::sprintf("Edit page \"%s\"",array($result[Tags::RESULT][0]['title']));
        $data['content'] = $result[Tags::RESULT][0];


        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets('cms','trumbowyg/ui/trumbowyg.css')
        );

        $data['templates'] = CMSUtils::getPTemplates();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cms/backend/html/edit-page");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function addPage(){

        if (!GroupAccess::isGranted('cms')  )
            redirect("error?page=permission");

        $data['title'] = _lang("Create page");

        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets('cms','trumbowyg/ui/trumbowyg.css')
        );

        $data['templates'] = CMSUtils::getPTemplates();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cms/backend/html/add-page");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function manageMenu(){

        if (!GroupAccess::isGranted('cms')  )
            redirect("error?page=permission");

        $data['pages'] = $this->mCMS->getAllPages();
        $data['menu'] = $this->mCMS->getMenu();
        $data['title'] = _lang("Manage Menu");


        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets('cms','css/style.css')
        );

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cms/backend/html/manage-menu");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function preview(){

        $result = $this->mCMS->getPages(array(
            'id' => intval(RequestInput::get("id"))
        ));

        if(!isset($result[Tags::RESULT][0])){
            redirect("error404");
        }

        echo file_get_contents(webapp_url(Translate::getDefaultLangCode()."/".$result[Tags::RESULT][0]['slug']));
        return;


    }

    public function error404(){
        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("cms/backend/error404");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");
    }



    public function groupAccessExampleAdmin(){


        $modules = GroupAccess::getModuleActions();

        $data = array();
        foreach ($modules as $key => $ac){
            $data[$key] = array();
            foreach ($ac as $key1 => $value){
                $data[$key][$value] = 1;
            }
        }

        echo "Admin<br><br>";
        echo json_encode($data,JSON_FORCE_OBJECT);

        $data = array();
        foreach ($modules as $key => $ac){
            $data[$key] = array();
            foreach ($ac as $key1 => $value){
                $data[$key][$value] = 0;
            }
        }

        echo "<br><br>MobileUser<br><br>";
        echo json_encode($data,JSON_FORCE_OBJECT);
        die();


    }

}

/* End of file CmsDB.php */
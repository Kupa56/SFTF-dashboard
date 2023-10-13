<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function languages(){

        if (!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE))
            redirect("error?page=permission");

        //make setting side bar opening
        AdminTemplateManager::set_settingActive('nstranslator');

        //get codes list
        $languages = Translate::getLangsCodes();
        $data['languages'] = $languages;

        $script = $this->load->view('nstranslator/backend/script/languages-script',$data,TRUE);
        AdminTemplateManager::addScript($script);

        //render views
        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("nstranslator/backend/html/languages");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function sync(){

       $elements = Translate::loadLanguageFromYml("bn");
       echo json_encode($elements);

    }

    public function remove()
    {

        $this->enableDemoMode();

        if (!GroupAccess::isGranted('nstranslator', TRANSLATOR_MANAGE))
            redirect("error?page=permission");

        $code = RequestInput::get('lang');

        Translate::remove($code);

        redirect(admin_url("nstranslator/languages"));

    }

    public function edit(){

        $this->enableDemoMode();

        if (!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE))
            redirect("error?page=permission");

        //make setting side bar opening
        AdminTemplateManager::set_settingActive('nstranslator');

        $lang = RequestInput::get('lang');
        $data['lang'] = $lang;


        //check validate of input language
        if(!preg_match("#[a-zA-Z]{2}#",$lang))
            redirect("error?page=notfound");


        $language_cached = Translate::loadLanguageFromCache($lang);
        $language_uncached = Translate::loadLanguageFromYml($lang);
        $data['merged_data'] = Translate::merge($language_uncached,$language_cached);

        if(isset($data['merged_data']['config'])){
            $data['config'] = $data['merged_data']['config'];
        }else{
            $data['config'] = $language_uncached['config'];
        }


        //render views
        AdminTemplateManager::addCssLibs(adminAssets("plugins/datatables/dataTables.bootstrap.css"));
        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets('nstranslator','css/style.css')
        );

        $script = $this->load->view('nstranslator/backend/script/edit-script',$data,TRUE);
        AdminTemplateManager::addScript($script);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("nstranslator/backend/html/edit");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

}
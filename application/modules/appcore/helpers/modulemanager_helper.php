<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 3/5/2018
 * Time: 21:12
 */

class NSModuleLoader{

    public static function loadModel($module,$model_path,$name=""){

        if(!ModulesChecker::isRegistred($module))
            return;

        $context = &get_instance();

        if($name != ""){
            $context->load->model($module."/".$model_path,$name);
        }else{
            $context->load->model($module."/".$model_path);
        }

    }


}


class NotesManager{

    private static $notes=array();

    public static function addNew($object){
        if(!isset(self::$notes[$object->getId()])){
            self::$notes[$object->getId()] = $object;
        }
    }

    public static function fetchAllNotes(){
        foreach (self::$notes as $note){
            echo $note->getView();
        }
    }

}


class TM_Note{

    public static function newInstance($module,$HTML){
        $o = new TM_Note();
        $o->setModule($module);
        $o->setId(time().rand(0000,1000000));
        $o->setView($HTML);
        return $o;
    }

    private $id;
    private $module;
    private $view;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param mixed $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }


}

class AdminPanel{
    const TemplatePath = "backend/". Appcore::AdminTemplate;
}

class AdminTemplateManager{

    private static $sidebar_menu = array();
    private static $sidebar_menu_settings = array();
    private static $scripts = array();
    private static $html = array();
    private static $scriptsLibs = array();
    private static $cssLibs = array();
    private static $cssStyle = array();

    public static function addHeadStyle($style){
        self::$cssStyle[] = $style;
    }

    public static function addHtml($html){
        self::$html[] = $html;
    }

    public static function addScript($html){
        self::$scripts[] = $html;
    }

    public static function addScriptLibs($lib){

        $key = new KeysManager();
        $key->setKey($lib);
        $key = $key->getKey($lib);

        self::$scriptsLibs[$key] = $lib;
    }

    public static function addCssLibs($lib){
        self::$cssLibs[$lib] = $lib;
    }

    public static function loadCssLibs(){

        foreach (self::$cssLibs as $lib){
            echo '<link rel="stylesheet" href="'.$lib.'">';
        }
    }

    public static function loadHeadStyle(){
        foreach (self::$cssStyle as $css){
            echo $css;
        }
    }

    public static function loadScripts(){

        $scripts = "";
        foreach (self::$scripts as $script){
            $scripts = $scripts."\n".$script;
        }
        return $scripts;
    }


    public static function loadHTML(){
        $html2 = "";
        foreach (self::$html as $html){
            $html2 = $html2."\n".$html;
        }
        return $html2;
    }

    public static function loadScriptsLibs(){
        foreach (self::$scriptsLibs as $lib){
            echo '<script async src="'.$lib.'"></script>';
        }
    }

    public static function registerMenu($module,$path,$_order){

        if(!isset(self::$sidebar_menu[$module])){


            self::$sidebar_menu[$module] = array();
            self::$sidebar_menu[$module]["path"] = $path;
            self::$sidebar_menu[$module]["order"] = $_order;
        }

    }


    public static function registerMenuSetting($module,$path,$_order){
        if(!isset(self::$sidebar_menu_settings[$module])){
            self::$sidebar_menu_settings[$module] = array();
            self::$sidebar_menu_settings[$module]["path"] = $path;
            self::$sidebar_menu_settings[$module]["order"] = $_order;
        }
    }


    public static function loadMenu($html=FALSE){


        $sortedMenuList = array();

        foreach (self::$sidebar_menu as $menu){

            $orderId = $menu['order'];
            if(isset($sortedMenuList[$orderId])){
                $orderId = ($orderId.'.1');
            }

            $sortedMenuList[$orderId] = $menu;
        }


        ksort($sortedMenuList);

        self::$sidebar_menu = $sortedMenuList;


        if(!$html){
            return self::$sidebar_menu;
        }else{

            $html = "";
            $context = &get_instance();
            if(!empty(self::$sidebar_menu)){
                foreach (self::$sidebar_menu as $menu){
                    foreach ($menu as $li){
                        $html = $context->load->view($li['path'],NULL,TRUE);
                    }
                }
            }

            return $html;
        }

    }


    public static function loadMenuSetting(){

        usort(self::$sidebar_menu_settings,function($first, $second){
            if($first['order'] > $second['order']){
                return $first;
            }
        });

        return self::$sidebar_menu_settings;
    }


    private static $setting_active =  NULL;
    public static function set_settingActive($pack){
        self::$setting_active = $pack;
    }

    public static function isSettingActive($pack=''){
        if(self::$setting_active!=NULL)
            return TRUE;
        else
            return FALSE;
    }


    public static function assets($module,$path){
        return base_url("application/modules/".$module."/views/assets/".$path);
    }

}

class ViewLoader{

    private static $headerPATH=NULL;
    private static $bodyPATH=NULL;
    private static $footerPATH=NULL;

    private static $viewDATA=NULL;


    public static function loadHeader($viewPATH,$data=NULL){

        self::$headerPATH = $viewPATH;
        self::$viewDATA = $data;

    }


    public static function loadBody($viewPATH){

        self::$bodyPATH = $viewPATH;

    }

    public static function loadFooter($viewPATH){

        $context = &get_instance();

        self::$footerPATH = $viewPATH;

        $context = &get_instance();

        $body = $context->load->view(self::$bodyPATH,self::$viewDATA,TRUE);
        $header = $context->load->view(self::$headerPATH,self::$viewDATA,TRUE);
        $footer = $context->load->view(self::$footerPATH,self::$viewDATA,TRUE);

        echo $header;
        echo $body;
        echo $footer;

    }


}

//create a custom assets function
if(!function_exists("adminAssets")){
    function adminAssets($path=""){
        if($path !="" )
            return "views/".AdminPanel::TemplatePath."/assets/".$path;
        else
            return "views/".AdminPanel::TemplatePath."/assets";
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 12/6/2017
 * Time: 17:14
 */


class FModuleLoader{

    private static $initialized = FALSE;

    private static $registredModules = array();

    //to use in the callbacks
    private static $onUpgradeList = array();
    private static $onInstallList = array();
    private static $onCommitedList = array();
    private static $onRegisteredlList = array();
    private static $onLoadList = array();


    private static $module_exists = array();

    public static function getRegistredModules(){
        return self::$registredModules;
    }

    public static function init(){

        if(self::$initialized==TRUE)
            return;

        $_cxt = &get_instance();

        $external = FModuleLoader::loadExternalModules();
        $core = FModuleLoader::loadCoreModules();

        foreach ($external as $value)
            self::$module_exists[] = $value;

        foreach ($core as $value)
            self::$module_exists[] = $value;


        //load & register modules
        foreach (self::$module_exists as $module){
            //instance of module
            $_cxt->load->module($module);
        }

        //commit after loading modules
        FModuleLoader::commit();

        self::$initialized = TRUE;

    }

    public static function loadAllModules(){

        $modules = array();

        $external = FModuleLoader::loadExternalModules();
        $core = FModuleLoader::loadCoreModules();

        foreach ($external as $value)
            $modules[] = $value;

        foreach ($core as $value)
            $modules[] = $value;

        return $modules;
    }

    public static function register($context, $options){


        $_cxt = &get_instance();

        if(!is_string($context))
            $moduleName = strtolower(get_class($context));
        else
            $moduleName = strtolower($context);

        if(!isset($options['order'])){
            echo 'Undefined (' . $moduleName . ') config file, please try to create a file in config folder config/'.$moduleName.'.json ';
            exit();
        }

        if (!isset($options['order']) and $options['order'] != 0) {

            $keys = array_keys(self::$registredModules);
            $last_key = end($keys);
            $last_key = ($last_key + 1);


            self::fetchModules();
            echo 'You should register module (' . $moduleName . ') with order (Try to add this number <b>"' . $last_key . '"</b> as order )';
            exit();

        }

        if (isset(self::$registredModules[$options['order']]) and $options['order'] != 0 and $moduleName!=self::$registredModules[$options['order']]['module_name']) {

            $keys = array_keys(self::$registredModules);
            $last_key = end($keys);
            $last_key = ($last_key + 1);

            self::fetchModules();
            echo 'Module name: <b>"' . $moduleName . '"</b> already defined by same order ' . $options['order'] . '  (Try to add this number <b>"' . $last_key . '"</b> as order )';
            exit();

        }

        $opt = array();
        $opt['module_name'] = $moduleName;
        $opt['version_code'] = $options['version_code'];
        $opt['version_name'] = $options['version_name'];
        $opt['order'] = $options['order'];

        //refresh module in database

        $_cxt->load->model('appcore/appcoremodel');
        $_cxt->appcoremodel->refreshModules($opt);

        //put it in array
        //check module is found with requirements
        if (isset($options['requirement'])) {
            foreach ($options['requirement'] as $value) {
                if (!self::moduleIsExists($value)) {
                    echo "Module <b>\"" . $moduleName . "\"</b> needs \"<b>"
                        . $value . "\"</b> module";
                    exit();
                }
            }

        }

        //check is registred and registred
        if (!self::isRegistred($moduleName)) {

            self::$registredModules[$opt['order']] = array();
            self::$registredModules[$opt['order']]['module_name'] = $moduleName;

            if (isset($options['menu']) AND $options['menu'] != NULL)
                self::$registredModules[$opt['order']]['menu'] = $options['menu'];

            if (isset($options['setting_menu']) AND $options['setting_menu'] != NULL)
                self::$registredModules[$opt['order']]['setting_menu'] = $options['setting_menu'];

            ksort(self::$registredModules);
        }


    }


    public static function  getModuleDetail($module_name){

        if(!is_string($module_name)){
            $module_name = strtolower(get_class($module_name));
        }

        $path = Path::getPath(array('application','modules',$module_name,'config',$module_name.'.json'));

        if(file_exists($path)){
            $data = url_get_content($path);
            $data = json_decode($data,JSON_OBJECT_AS_ARRAY);

            return $data;
        }

        return array();
    }




    private static function isRegistred($moduleName)
    {

        foreach (self::$registredModules as $key => $module) {
            if ($module['module_name'] == $moduleName) {
                return TRUE;
            }
        }

        return FALSE;
    }

    private static function moduleIsExists($moduleName)
    {

        if (in_array($moduleName, self::$module_exists)) {
            return TRUE;
        }

        return FALSE;
    }


    private static function fetchModules()
    {

        foreach (self::$registredModules as $key => $value) {
            echo $key . '=>' . $value['module_name'] . "<br>";
        }
    }

    public static function getModules()
    {
        return self::$registredModules;
    }

    public static function onUpgrade($module){
        self::$onUpgradeList[$module] = $module;
    }

    public static function onInstall($module){
        self::$onInstallList[$module] = $module;
    }

    public static function onCommitted($module){
        self::$onCommitedList[$module] = $module;
    }

    public static function onRegistered($module){
        self::$onRegisteredlList[$module] = $module;
    }

    public static function onLoad($module){
        self::$onLoadList[$module] = $module;
    }

    public static function commit(){

        $context = &get_instance();

        //define all variables and constances
        foreach (self::$onLoadList as $module){
            $context->{$module}->onLoad();
        }


        $loaded_installed_modules = array();
        $modules = ModulesChecker::getLoadedModules();

        foreach ($modules as $object){
            $loaded_installed_modules[ $object['module_name'] ] = $object;
        }


        $configFileList = ModuleManager::fetch();
        $loaded_from_config = array();
        foreach ($configFileList as $object){
            $loaded_from_config[ $object['module_name'] ] = $object;
        }

        $committedListModule = array();

        //sort modules by orders
        foreach (self::$onCommitedList as $moduleName){
            $moduleConfig = self::findConfigByName($moduleName,$configFileList);
            if($moduleConfig != NULL){
                $committedListModule[$moduleConfig['_order']] = $moduleName;
            }else{
                $committedListModule[$moduleName] = $moduleName;
            }
        }


        ksort($committedListModule);

        //make that the modules ares enabled
        foreach ($committedListModule as $module){

            if(isset($loaded_from_config[$module])
                && $loaded_from_config[$module]['version_code'] == $loaded_installed_modules[$module]['version_code']){

                if(ModulesChecker::isEnabled($module))
                    $context->{$module}->onCommitted(TRUE);
                else
                    $context->{$module}->onCommitted(FALSE);

            }else if(!isset($loaded_from_config[$module])){

                if(ModulesChecker::isEnabled($module))
                    $context->{$module}->onCommitted(TRUE);
                else
                    $context->{$module}->onCommitted(FALSE);
            }

        }

    }

    public static function findConfigByName($key, $moduleConfigList){

        foreach ($moduleConfigList as $m){
            if($m['module_name'] == $key){
                return $m;
            }
        }
        return NULL;
    }

    public static function modulesToInstall(){
       return self::$onInstallList;
    }

    public static function modulesToUpgrade(){
        return self::$onUpgradeList;
    }

    public static function loadExternalModules(){

        $path = Path::getPath(array("modules"));
        return self::loadFromDir($path);

    }
    public static function loadCoreModules(){

        $path = Path::getPath(array("application","modules"));
        return self::loadFromDir($path);

    }



    private static function loadFromDir($path){

        $data = array();

        if(is_dir($path))
        if ($handle = opendir($path) AND $path!="") {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    if(!preg_match('#^\.#',$entry) and !preg_match('#^\_#',$entry) ){
                        $data[] = $entry;
                    }

                }
            }
        }


        return $data;

    }


    public static function getCurrentModule(){

        $ctx = &get_instance();

        $uri1 = $ctx->uri->segment(1);
        $uri2 = $ctx->uri->segment(2);

        if($uri1 == __ADMIN){
            echo $uri2;
        }else{
            echo $uri1;
        }

    }


}

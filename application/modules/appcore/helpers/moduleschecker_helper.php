<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 12/8/2017
 * Time: 21:52
 */


class ModulesChecker{


    public static function whenIsActive($module){

        if(!is_string($module)){
            $module = strtolower(get_class($module));
        }

        $cxt = &get_instance();

        $uri0 = $cxt->uri->segment(1);
        $uri1 = $cxt->uri->segment(2);

        if($uri0 == __ADMIN && $uri1==$module){
            return TRUE;
        }else if($uri0 == "ajax" && $uri1==$module){
            return TRUE;
        }else if($uri0 == $module && $uri1=="ajax"){
            return TRUE;
        }else if($uri0 == "api" && $uri1==$module){
            return TRUE;
        }else if($uri0 == $module && $uri1=="api"){
            return TRUE;
        }else if($uri0==$module){
            return TRUE;
        }else if($uri0 == __ADMIN && $uri1=="" && $module=="cms"){
            return TRUE;
        }

        return FALSE;
    }


    public static function requireRegistred($module){
        if(!ModulesChecker::isRegistred($module)){
            die("This module \"$module\" is required to be enabled");
        }
    }

    public static function requireEnabled($module){

        if(!ModulesChecker::isEnabled($module)){
            die("This module \"$module\" is required to be enabled");
        }

    }

    public static function checkRequirements($module){

        $module_data = FModuleLoader::getModuleDetail($module);

        if(isset($module_data['requirements']) AND !empty($module_data['requirements'])){
            foreach ($module_data['requirements'] as $req_module){

                $req_module = explode(":",$req_module);
                $module_name = $req_module[0];

                if(!ModulesChecker::isEnabled($module_name)){
                    $msg = Translate::sprintf("Please make sure if this module (%s) is installed",array($req_module));
                    die($msg);
                }
            }
        }

    }

    public static function getField($module,$key){

        $module_data = FModuleLoader::getModuleDetail($module);

        if(isset($module_data[$key]))
            return $module_data[$key];

        return NULL;
    }

    public static function isRegistred($moduleName){

        if(!is_string($moduleName)){
            $moduleName = strtolower(get_class($moduleName));
        }

        foreach (FModuleLoader::getRegistredModules() as $key => $module) {
            if ($module['module_name'] == $moduleName) {
                return TRUE;
            }
        }

        return FALSE;
    }

    private static $loadedModules = NULL;

    public static function getLoadedModules(){
        self::init();
        return self::$loadedModules;
    }

    public static function getLoadedModulesCFile(){
        self::init();
        return self::$loadedModulesCFile;
    }

    public static function init(){

        if(self::$loadedModules==NULL){
            $context = &get_instance();
            $data = $context->db->get('modules');
            $data = $data->result_array();
            self::$loadedModules = $data;
        }


    }

    public static function isEnabled($moduleName){

        self::init();

        if(!is_string($moduleName)){
            $moduleName = strtolower(get_class($moduleName));
        }

        if(!ModulesChecker::isRegistred($moduleName)){
            return FALSE;
        }


        if(self::$loadedModules!=NULL)
            foreach (self::$loadedModules as $module){
                if($moduleName==$module['module_name'] AND $module['_enabled']==1){
                    return TRUE;
                }
            }

        return FALSE;
    }




}
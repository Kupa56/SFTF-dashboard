<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH . "third_party/MX/Controller.php";

class MAIN_Controller extends MX_Controller implements ModuleLoader {

    public function __construct()
    {
        parent::__construct();
    }

    public function init($module_name){
        $data = FModuleLoader::getModuleDetail($module_name);
        FModuleLoader::register($module_name,$data);
    }



    public function onRegistered()
    {
        // TODO: Implement onInstall() method.
    }

    public function onInstall()
    {
        // TODO: Implement onInstall() method.
    }

    public function cron()
    {
        // TODO: Implement cron() method.
    }

    public function onUpgrade()
    {
        // TODO: Implement onUpgrade() method.
    }

    public function onUninstall()
    {
        // TODO: Implement onUninstall() method.
    }

    public function onEnable()
    {
        // TODO: Implement onEnable() method.
        return TRUE;
    }

    public function onDisable()
    {
        // TODO: Implement onDisable() method.
    }

    public function onCommitted($isEnabled)
    {
        // TODO: Implement onLoaded() method.
    }

    public function register()
    {
        // TODO: Implement register() method.
    }

    public function onLoad()
    {
        // TODO: Define your variables and load all models
    }

    public function settings()
    {
        return NULL;
    }


    public function enableDemoMode(){

        if(!ModulesChecker::isEnabled("demo"))
            return;

        $manager = $this->mUserBrowser->getData("manager");
        $authType = $this->mUserBrowser->getData("typeAuth");

        if($manager==1) {
            return;
        }else if($authType=="manager"){
            return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION_DEMO)
            )));
            exit();
        }
    }
}

interface ModuleLoader{
    public function onInstall();
    public function onUpgrade();
    public function onUninstall();
    public function onCommitted($isEnabled); //call after loading
    public function onLoad();
    public function onEnable();
    public function onDisable();
    public function cron();
    public function register();
    public function settings();
}

interface AdminModuleLoader{
    public function onAdminLoaded($module);
}





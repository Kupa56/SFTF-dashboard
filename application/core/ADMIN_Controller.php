<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */

class ADMIN_Controller extends MAIN_Controller {

    public function __construct()
    {
        parent::__construct();

        /*
         * Redirect unsigned users
         */
        $this->load->module('user');
        if(!$this->mUserBrowser->isLogged()){
            redirect(site_url("user/login"));exit();
        }


        /*
        * Refresh & Check admin permission
        */
        GroupAccess::initGrant();


        /*
        * Execute admin callback
        */
        $this->setupAdminLoaderCallback();

    }

    /*
       * call on registered module
       */

    private function setupAdminLoaderCallback(){

        $uri = explode('/',$this->router->default_controller);
        $current_module =  reset($uri);
        $modules = FModuleLoader::getModules();

        foreach ($modules as $module){

            if (method_exists($this->{$module['module_name']}, 'onAdminLoaded')){
                $result = $this->{$module['module_name']}->onAdminLoaded($current_module);
                if($result != false){
                    call_user_func($result);
                }
            }

        }
    }

    public function cron(){

    }


    protected function loadAdmin(){



    }


}




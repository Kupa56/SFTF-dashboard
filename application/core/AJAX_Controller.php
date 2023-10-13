<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */

class AJAX_Controller extends MAIN_Controller   {

    public function __construct()
    {
        parent::__construct();
        GroupAccess::initGrant();


    }

    private function checkToken(){

        $token = RequestInput::get("csrfToken01");
        if($token == "")
            $token = RequestInput::post("csrfToken01");

        $sessToken = TokenSessManager::getSessToken("webDashboardAp_13");

        if($token != $sessToken){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => "Invalid token (csrfToken01)"
            )));
            exit();
        }


    }

    public function enableDemoMode(){

        if(ModulesChecker::isEnabled("demo")){

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




}

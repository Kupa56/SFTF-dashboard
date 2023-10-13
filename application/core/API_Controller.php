<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */

class API_Controller extends MAIN_Controller   {

    public function __construct()
    {
        parent::__construct();

        $lang = Security::decrypt($this->input->get_request_header('Language', Translate::getDefaultLangCode()));
        Translate::changeSessionLang($lang);

        if(!$this->checkTokenIsValide()){
            echo json_encode(array(Tags::SUCCESS=>-1,Tags::ERRORS=>array("Err"=>Translate::sprint("You don't have permission to server try later!"))));
            die();
        }

        GroupAccess::initGrant();

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


    protected function requireAuth(){


        $sess_uid = Security::decrypt($this->input->get_request_header('Session-User-Id', 0));
        $authorization = Security::decrypt($this->input->get_request_header('Authorization', ''));

        //use alternative header
        if($authorization == '')
            $authorization = Security::decrypt($this->input->get_request_header('Authentication', ''));


        if($authorization == null){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION)
            )));
            exit();
        }

        $authorization = explode(" ",$authorization);

        if(isset($authorization[0]) && $authorization[0] == "Bearer"){
            $sess_token = $authorization[1];
        }else{
            $sess_token = "";
        }

        $object = TokenSetting::get_by_token($sess_token,NULL);

        if($object == NULL){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION)
            )));
            exit();
        }

        if(intval($object->uid) != intval($sess_uid)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION)
            )));
            exit();
        }

        //refresh session
        SessionManager::refresh(intval($object->uid));

        return intval($object->uid);
    }

    private function checkTokenIsValide(){


        $headers = $this->input->request_headers();

        if(Checker::isValid($headers))
            return TRUE;
        else
            return FALSE;


    }



}

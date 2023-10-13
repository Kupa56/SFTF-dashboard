<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Ajax extends AJAX_Controller  {


    public function __construct(){
        parent::__construct();
    }

    public function verifyAndCreateWalletTransaction(){

        if(!SessionManager::isLogged()
            && !GroupAccess::isGranted('payment', DIGITAL_WALLET_SEND_RECEIVE)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION_DEMO)
            )));
            exit();
        }

        $SendAsadmin = (RequestInput::post("SendAsadmin"));
        $token = (RequestInput::post("token"));
        $receiver = (RequestInput::post("email"));
        $amount = (RequestInput::post("amount"));
        $sessToken = $this->mUserBrowser->getToken("WgTh_ABbnl");

        if($token != $sessToken){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION_DEMO)
            )));
            exit();
        }


        if($SendAsadmin==true && SessionManager::getData("manager")==1){

            try {

                $result = $this->mWalletModel->sendMoneyAdmin($receiver,$amount);

                if($result[Tags::SUCCESS]==1){
                    $this->mUserBrowser->cleanToken("WgTh_ABbnl");
                }
                echo json_encode($result);return;
            }catch (Exception $e){
                echo json_encode(array(
                    Tags::SUCCESS=>0,
                    Tags::ERRORS=>array("err"=>_lang($e->getMessage()))
                ));return;
            }

        }

        $sender = SessionManager::getData("email");

        try {
            $result = $this->mWalletModel->verifyAndSend($sender,$receiver,$amount);

            if($result[Tags::SUCCESS]==1){
                $this->mUserBrowser->cleanToken("WgTh_ABbnl");
            }

            echo json_encode($result);return;
        }catch (Exception $e){
            echo json_encode(array(
                Tags::SUCCESS=>0,
                Tags::ERRORS=>array("err"=>_lang($e->getMessage()))
            ));return;
        }




    }

}

/* End of file PackmanagerDB.php */
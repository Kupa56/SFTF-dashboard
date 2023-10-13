<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Api extends API_Controller  {


    public function __construct(){
        parent::__construct();
    }

    public function getWallet(){

        $auth_user_id = $this->requireAuth();
        $user_id = RequestInput::post("user_id");

        if($user_id != $auth_user_id){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION_DEMO)
            )));
            exit();
        }

        $result = $this->mWalletModel->getBalance($user_id);
        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=> $result));

    }

}

/* End of file PackmanagerDB.php */
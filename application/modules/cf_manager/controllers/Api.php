<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();

    }


    public function getCF(){

        $cf_id = Security::decrypt(RequestInput::post("cf_id"));
        $result = $this->mCFManager->getList0($cf_id);

        echo json_encode($result);return;
    }


}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Api extends API_Controller {

    public function __construct(){
        parent::__construct();
        //load model
        $this->load->model("uploader/uploader_model");
        $this->load->model("user/user_model");

    }

    //upload images



    public function uploadImage64(){



        $data = $this->uploader_model->uploadImage64(RequestInput::post('image'));

        $module_id = intval(RequestInput::post("module_id"));
        $type = (RequestInput::post("type"));

        if($type=="user" and $module_id>0 AND isset($data["result"])){
            if(isset($data['result']['image'])){

                echo json_encode($this->mUserModel->updatePhotosProfile(array(
                    "image"  => $data['result']['image'],
                    "user_id"   => intval($module_id)
                )),JSON_FORCE_OBJECT);
                exit();
            }
        }
        echo json_encode($data);
    }


}

/* End of file UploaderDB.php */
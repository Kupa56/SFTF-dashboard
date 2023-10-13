<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->enableDemoMode();

    }


    public function edit(){

        $fields = RequestInput::post("fields");
        $label = RequestInput::post("label");
        $id = intval(RequestInput::post("id"));

        $result = $this->mCFManager->editCustomFields(array(
            "fields" => $fields,
            "label" => $label,
            "user_id" => SessionManager::getData("id_user"),
            "id" => $id,
        ));

        echo json_encode($result);return;
    }


    public function add(){

        $fields = RequestInput::post("fields");
        $label = RequestInput::post("label");

        $result = $this->mCFManager->createCustomFields(array(
            "fields" => $fields,
            "label" => $label,
            "user_id" => SessionManager::getData("id_user"),
        ));

        echo json_encode($result);return;
    }


    public function remove(){

        $id = intval(RequestInput::post("id"));
        $user_id = SessionManager::getData("id_user");

        $result = $this->mCFManager->delete($id,$user_id);

        echo json_encode($result);return;
    }



}

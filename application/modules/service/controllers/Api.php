<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {

    public function __construct()
    {
        parent::__construct();
    }


    public function getServices(){

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));
        $store_id = intval(RequestInput::post("store_id"));

        $result = $this->mService->getGroupedList(
            $store_id
        );

        echo json_encode(
            array(
                Tags::SUCCESS=>1,
                Tags::RESULT=>$result
            )
        ,JSON_FORCE_OBJECT);

    }



}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {



    public function __construct(){
        parent::__construct();
        //load model


    }



    public function getBanners(){

        $limit = intval(RequestInput::post("limit"));
        $page = intval(RequestInput::post("page"));

        $params = array(
            "status"             =>     1,
            "limit"             =>      $limit,
            "page"              =>      $page,
        );

        $data =  $this->nsbanner_model->getBanners($params);

        if($data[Tags::SUCCESS]==1){
            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);
            echo Json::convertToJson($data[Tags::RESULT],  Tags::RESULT,TRUE,array(Tags::COUNT=>$data[Tags::COUNT]));
        }else{

            echo json_encode($data);
        }

    }


}
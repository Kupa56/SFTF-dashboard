<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Api extends API_Controller {

    public function __construct(){
        parent::__construct();
        //load model
        $this->load->model("category/category_model","mCategoryModel");
        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("user/user_browser", "mUserBrowser");

    }

    public function getCategories(){

        $latitude = doubleval(RequestInput::post("latitude"));
        $longitude = doubleval(RequestInput::post("longitude"));

        $data = $this->mCategoryModel->getCategories(array(
            "latitude" => $latitude,
            "longitude" => $longitude,
        ));

        if($data[Tags::SUCCESS]==1){

            $data[Tags::RESULT] = Text::outputList($data[Tags::RESULT]);

            foreach ($data[Tags::RESULT] as $key => $job){

                if($data[Tags::RESULT][$key]['image']!="")
                    $data[Tags::RESULT][$key]['image'] = _openDir($data[Tags::RESULT][$key]['image']);

                if($data[Tags::RESULT][$key]['icon'])
                    $data[Tags::RESULT][$key]['icon'] = _openDir($data[Tags::RESULT][$key]['icon']);

                $data[Tags::RESULT][$key]['name'] = Text::output($data[Tags::RESULT][$key]['name']);
                $data[Tags::RESULT][$key]['name'] = Translate::sprint($data[Tags::RESULT][$key]['name'],$data[Tags::RESULT][$key]['name']);

            }


            if(availableVersion("8.0",">")){
                usort($data[Tags::RESULT],function($first,$second){
                    return strtolower($first['_order']) <=> strtolower($second['_order']);
                });

            }else{
                usort($data[Tags::RESULT],function($first,$second){
                    return strtolower($first['_order']) > strtolower($second['_order']);
                });
            }


            echo Json::convertToJson($data[Tags::RESULT],  Tags::RESULT,TRUE,array());
        }else{
            echo json_encode($data);
        }

    }


}

/* End of file CategoryDB.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct()
    {
        parent::__construct();


    }

    public function re_order_list(){

        $result = $this->mService->re_order_list(array(
            'store_id'=> RequestInput::post('store_id'),
            'user_id'=> SessionManager::getData('id_user'),
            'list'=> RequestInput::post('list'),
        ));

        echo json_encode($result);return;

    }


    public function removeService(){

        $result = $this->mService->removeService(array(
            'service_id'=> RequestInput::post('service_id'),
            'user_id'=> SessionManager::getData('id_user'),
        ));

        echo json_encode($result);return;

    }

    public function createGroup(){

        $result = $this->mService->createGrp(array(
            'user_id'=> SessionManager::getData('id_user'),
            'store_id'=> RequestInput::post('store_id'),
            'label'=> RequestInput::post('label'),
            'option_type'=> RequestInput::post('option_type'),
        ));

        if($result[Tags::SUCCESS]==1){
            $data['grp'] = $result[Tags::RESULT];
            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=> $this->load->view('service/plug/options/group_row',$data,TRUE)
            ));
        }else
            echo json_encode($result);


    }

    public function updateGroup(){

        $result = $this->mService->updateGrp(array(
            'option_id'=> RequestInput::post('option_id'),
            'user_id'=> SessionManager::getData('id_user'),
            'store_id'=> RequestInput::post('store_id'),
            'label'=> RequestInput::post('label'),
            'option_type'=> RequestInput::post('option_type'),
        ));

        if($result[Tags::SUCCESS]==1){
            $data['grp'] = $result[Tags::RESULT];
            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=> $this->load->view('service/plug/options/group_row',$data,TRUE)
            ));
        }else
            echo json_encode($result);


    }



    public function createOption(){

        $result = $this->mService->createOption(array(
            'user_id'=> SessionManager::getData('id_user'),
            'store_id'=> RequestInput::post('store_id'),
            'service_id'=> RequestInput::post('service_id'),
            'option_price'=> RequestInput::post('option_price'),
            'option_name'=> RequestInput::post('option_name'),
            'option_description'=> RequestInput::post('option_description'),
        ));

        if($result[Tags::SUCCESS]==1){
            $data['opt'] = $result[Tags::RESULT];
            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=> $this->load->view('service/plug/options/option_row',$data,TRUE)
            ));
        }else
            echo json_encode($result);


    }

    public function updateOption(){

        $params = array(
            'option_id'=> RequestInput::post('option_id'),
            'user_id'=> SessionManager::getData('id_user'),
            'store_id'=> RequestInput::post('store_id'),
            'option_price'=> RequestInput::post('option_price'),
            'option_name'=> RequestInput::post('option_name'),
            'option_description'=> RequestInput::post('option_description'),
        );


        $result = $this->mService->updateOption($params);

        if($result[Tags::SUCCESS]==1){
            $data['opt'] = $result[Tags::RESULT];
            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=> $this->load->view('service/plug/options/option_row',$data,TRUE)
            ));
        }else
            echo json_encode($result);

    }




}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller  {

    public function __construct(){
        parent::__construct();
        //load model

        $this->load->model("nshistoric/notification_history_model","mHistoric");

    }




}
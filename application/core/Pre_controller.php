<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');


class Pre_controller extends MX_Controller  {

    public function __construct()
    {
        parent::__construct();
    }

    public function load_all_dependencies(){

        $this->load->module("appcore");

        $this->appcore->load_all_config();
        $this->appcore->load_all_dependencies();

    }



}



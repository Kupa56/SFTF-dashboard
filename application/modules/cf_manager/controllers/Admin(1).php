<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model



    }


    public function cf_list(){


        $data = array();

        $data['list'] = $this->mCFManager->getList(
            SessionManager::getData("id_user")
        );

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cf_manager/backend/list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function add(){


        $data = array();

        $data['map'] = $this->mCFManager->getFieldsSchema();

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cf_manager/backend/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function edit(){


        $data = array();

        $data['map'] = $this->mCFManager->getFieldsSchema();
        $data['data'] = $this->mCFManager->get(
            intval(RequestInput::get("id")),
            SessionManager::getData("id_user")
        );

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("cf_manager/backend/edit");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


}

/* End of file EventDB.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::requireEnabled("nsbanner");

    }


    public function all(){

        if(!GroupAccess::isGranted('nsbanner')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $result = $this->nsbanner_model->getBanners(array(
            "limit"   => 30,
            "page"   => intval(RequestInput::get('page')),
        ));


        $data["banners"] = $result[Tags::RESULT];
        $data["pagination"] = $result[Tags::PAGINATION];

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("nsbanner/backend/html/banners");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function add(){

        if(!GroupAccess::isGranted('nsbanner', NS_BANNER_GRP_ACTION_ADD)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("nsbanner/backend/html/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function edit(){

        if(!GroupAccess::isGranted('nsbanner', NS_BANNER_GRP_ACTION_EDIT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $result = $this->nsbanner_model->getBanners(array(
            "limit"   => 1,
            "banner_id"   => intval(RequestInput::get('id')),
        ));

        $data = array();
        $data['banner'] = $result[Tags::RESULT][0];


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("nsbanner/backend/html/edit",$data);
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


}

/* End of file CampaignDB.php */
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load models
        $this->load->model("booking/Booking_model", "mBookingModel");
    }

    public function all_reservations(){


        if (!GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING_CONFIG))
            redirect("error?page=permission");

        $page = intval(RequestInput::get('page'));
        $limit = intval(RequestInput::get('limit'));

        if($limit==0)
            $limit = 30;

        $params = array(
            "limit"   =>$limit,
            "page"    =>$page
        );

        $data['pagination_url'] = admin_url("booking/all_reservations");
        $data['data'] = $this->mBookingModel->getBookings($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("booking/backend/list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function client_bookings(){


        /*
         * Check Compatibity
         */
        $version = ModulesChecker::getField("cms","version_name");


        if (version_compare($version, '2.0.2', '<'))
          redirect("error?page=permission&err=booking&r=incompatibility");


       if (!ModulesChecker::isEnabled("booking"))
            redirect("error?page=permission&err=booking");

       AdminTemplateManager::addCssLibs(
           AdminTemplateManager::assets("booking","css/bookings.css")
       );

        $page = intval(RequestInput::get('page'));
        $limit = intval(RequestInput::get('limit'));

        if($limit==0)
            $limit = 10;

        $params = array(
            "limit"   =>    $limit,
            "page"    =>    $page,
            "user_id" =>    SessionManager::getData("id_user")
        );

        $data['pagination_url'] = admin_url("booking/client_bookings");
        $data['data'] = $this->mBookingModel->getBookings($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("booking/backend/client_bookings_list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function my_reservations(){

        if (!GroupAccess::isGranted('booking', GRP_MANAGE_BOOKING))
            redirect("error?page=permission");


        $page = intval(RequestInput::get('page'));
        $limit = intval(RequestInput::get('limit'));

        if($limit==0)
            $limit = 30;

        $params = array(
            "limit"   =>$limit,
            "page"    =>$page,
            "owner_id"    =>SessionManager::getData("id_user"),
        );

        $data['pagination_url'] = admin_url("booking/my_reservations");
        $data['data'] = $this->mBookingModel->getBookings($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("booking/backend/list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function view(){

        if (!GroupAccess::isGranted('booking', GRP_MANAGE_BOOKING))
            redirect("error?page=permission");

        $id = intval(RequestInput::get('id'));

        $params = array(
            "limit"   =>1,
            "page"    =>1,
            "id"    =>$id,
        );


        $data['pagination_url'] = admin_url("booking/reservations");

        $d = $this->mBookingModel->getBookings($params);
        if(isset($d[Tags::RESULT][0])){
            $data['reservation'] = $d[Tags::RESULT][0];
        }else{
            redirect("error?page=permission");
        }

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("booking/backend/booking_detail");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

}

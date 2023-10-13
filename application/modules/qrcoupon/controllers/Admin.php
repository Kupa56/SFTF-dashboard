<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function coupons(){

        if (!GroupAccess::isGranted('qrcoupon',GRP_MANAGE_QRCOUPONS_KEY))
            redirect("error?page=permission");

        $params = array(
            "page" => intval(RequestInput::get("page")),
            "offer_id" => intval(RequestInput::get("id")),
            "limit" => 30,
        );

        if(SessionManager::getData("manager") == 0
            && SessionManager::getData("username") != "admin"){
            $params['business_user_id'] = SessionManager::getData("id_user");
        }



        $data['result'] = $this->mQrcouponModel->getOfferCoupons($params);


        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("qrcoupon/backend/html/list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

}

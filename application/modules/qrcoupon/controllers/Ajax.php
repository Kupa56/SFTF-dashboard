<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{


    public function __construct()
    {
        parent::__construct();
    }



    public function updateStatus()
    {

        if (!GroupAccess::isGranted('qrcoupon', GRP_MANAGE_QRCOUPONS_KEY)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $coupon_id =  intval(RequestInput::post("coupon_id"));
        $offer_id =  intval(RequestInput::post("offer_id"));
        $status =  intval(RequestInput::post("status"));


        $result = $this->mQrcouponModel->updateStatus(array(
            "coupon_id" =>$coupon_id,
            "offer_id" =>$offer_id,
            "status" =>$status,
            "business_user_id" =>SessionManager::getData("id_user"),
        ));


        echo json_encode($result);

    }


}
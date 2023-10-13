<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Qrcoupon extends MAIN_Controller implements AdminModuleLoader
{
    const COUPON_DISABLED = "disabled";
    const COUPON_LIMITED = "limited";
    const COUPON_UNLIMITED = "unlimited";

    public function __construct()
    {
        parent::__construct();
        $this->init('qrcoupon');
    }

    public function onCommitted($isEnabled)
    {

        if($isEnabled == FALSE)
            return;

    }

    public function setup_widget_coupon_config($params=array()){

        if(isset($params['offer_id']) && $params['offer_id']>0){
            $params = array(
                "offer_id"  => $params['offer_id'],
                "limit"     => 1
            );

            $offer = $this->mOfferModel->getOffers($params);

            $params = array(
                'offer' => $offer[Tags::RESULT][0]
            );

        }else{

            $params = array(
                'offer' => array(
                    "coupon_config"=> Qrcoupon::COUPON_DISABLED,
                    "coupon_redeem_limit"=> 0,
                    "coupon_code"=> "",
                )
            );

        }




        return array(
            'html' => $this->load->view("qrcoupon/plug/coupon-offer-widget",$params,TRUE),
            'script' => $this->load->view("qrcoupon/plug/coupon-offer-widget-script",NULL,TRUE),
        );
    }

    public function onLoad()
   {
       //load dependencies
       //models
       $this->load->model("qrcoupon/qrcoupon_model", 'mQrcouponModel');
       //helper
       $this->load->helper("qrcoupon/coupon");

       define('GRP_MANAGE_QRCOUPONS_KEY','manage_offer_coupons');
       define('GRP_SCAN_QRCODE_MOBILE','scan_qrcode_mobile');
   }

    private function registerModuleActions(){

        GroupAccess::registerActions("qrcoupon",array(
            GRP_MANAGE_QRCOUPONS_KEY,
            GRP_SCAN_QRCODE_MOBILE,
        ));

    }

    public function onInstall()
    {
        $this->mQrcouponModel->createTable();
        $this->mQrcouponModel->updateOfferFields();

        return TRUE;
    }

    public function onUpgrade()
    {
        $this->mQrcouponModel->createTable();
        $this->mQrcouponModel->updateOfferFields();

        $this->registerModuleActions();

        return TRUE;
    }

    public function onEnable()
    {
        $this->registerModuleActions();
        return TRUE;
    }


    public function onAdminLoaded($module)
    {
        /*
         * return bool / function
         */

        return function (){


        };
    }
}
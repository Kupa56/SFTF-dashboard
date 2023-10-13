<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Paytm extends MAIN_Controller {

    public $_api_context;

    function  __construct()
    {
        parent::__construct();

    }


    function paytm_request($data) {

        $this->load->library("payment/PaytmChecksum");

        $paytmParams = array();

        $paytmParams["body"] = array(
            "requestType"   => "Payment",
            "mid"           => PAYTM_KEY_ID,
            "websiteName"   => APP_NAME,
            "orderId"       => "OREDRID_".$data['order_id'],
            "callbackUrl"   => $data['callback'],
            "txnAmount"     => array(
                "value"     => $data['amount'],
                "currency"  => "INR",
            ),
            "userInfo"      => array(
                "custId"    => "CUST_".SessionManager::getData("id_user"),
                "email"    => SessionManager::getData("email"),
            ),
        );

        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
        */
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), PAYTM_SECRET_KEY);


        $paytmParams["head"] = array(
            "signature"    => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        if(PAYTM_CONFIG_DEV_MODE == TRUE){
            /* for Staging */
            $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=".PAYTM_KEY_ID."&orderId=OREDRID_".$data['order_id'];

        }else{
            /* for Production */
            $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=".PAYTM_KEY_ID."&orderId=OREDRID_".$data['order_id'];

        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);

        return $response;
    }



}
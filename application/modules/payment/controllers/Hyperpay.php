<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Hyperpay extends MAIN_Controller {

    public $_api_context;

    function  __construct()
    {
        parent::__construct();

    }



    function hyperpay_request($amount) {

        if (HYPERPAY_CONFIG_DEV_MODE == TRUE){
            $server = "test";
        }else{
            $server = "live";
        }

        $url = "https://".$server.".oppwa.com/v1/checkouts";
        $data = "entityId=".HYPERPAY_KEY_ID . //8a8294174b7ecb28014b9699220015ca
            "&amount=".$amount .
            "&currency=".DEFAULT_CURRENCY .
            "&paymentType=DB";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer '.HYPERPAY_SECRET_KEY)); //OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg=
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);

        return $responseData;
    }


    function get_hyperpay_status($cid) {

        if (HYPERPAY_CONFIG_DEV_MODE == TRUE){
            $server = "test";
        }else{
            $server = "live";
        }

        $url = "https://".$server.".oppwa.com/v1/checkouts/".$cid."/payment";
        $url .= "?entityId=".HYPERPAY_KEY_ID;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer '.HYPERPAY_SECRET_KEY));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if(curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return $responseData;
    }

}
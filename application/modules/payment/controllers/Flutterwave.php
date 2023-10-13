<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Flutterwave extends MAIN_Controller {

    public $_api_context;

    function  __construct()
    {
        parent::__construct();

    }



    public function verify(){


        $transaction_id = intval(RequestInput::get('transaction_id'));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/".$transaction_id."/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer ".FLUTTERWAVE_SECRET_KEY
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $result = json_decode($response,JSON_OBJECT_AS_ARRAY);

        if(isset($result['status']) && $result['status']=="success"){

            /**
             * Call this function from where ever you want
             * to save save data before of after the payment
             */

            $callback_success_url = $_SESSION['callback_success_url'];
            $callback_success_url = $callback_success_url."&method=flutterwave&paymentId=".$transaction_id."&PayerID=0";

            $_SESSION['callback_success_url'] = "";
            redirect($callback_success_url);

        } else {

            $callback_error_url= $_SESSION['callback_error_url'];
            $_SESSION['callback_error_url'] = "";
            redirect($callback_error_url);
        }

    }

}
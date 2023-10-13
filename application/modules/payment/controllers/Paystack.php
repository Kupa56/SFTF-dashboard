<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Paystack extends MAIN_Controller {

    public $_api_context;

    function  __construct()
    {
        parent::__construct();
     }


    public function verify(){

        $reference = RequestInput::get('reference');
        $item_data = $this->session->payment_paystack_cart;

        $url = "https://api.paystack.co/transaction/initialize";

        $fields = [
            'email' => $item_data['client']['email'],
            'amount' => $item_data['details_subtotal'] * 100
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();


        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".ConfigManager::getValue('PAYSTACK_SECRET_KEY'),
            "Cache-Control: no-cache",
        ));
        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $result = json_decode($result);

        if(!isset($result->status) or !$result->status){
            redirect($item_data['callback_error_url'].'#error-paystack-verify01');
            return;
        }


     //open connection
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/".$reference,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".ConfigManager::getValue('PAYSTACK_SECRET_KEY'),
                "Cache-Control: no-cache",
            ),
        ));

        //execute post
        $result = curl_exec($curl);
        $result = json_decode($result);

        if($result->status == 1){
            $transaction_id = $result->reference;
            $callback = $item_data['callback_success_url']."&method=paystack@mp=".PaymentsProvider::PAY_STACK."&paymentId=".$transaction_id."&PayerID=";
            redirect($callback);
        }else{
            redirect($item_data['callback_error_url'].'#error-paystack-verify02');
        }

    }



}
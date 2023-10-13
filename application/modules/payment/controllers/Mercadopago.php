<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//https://www.mercadopago.com.ar/developers/en/docs/checkout-api-v1/receiving-payment-by-card#editor_1

class process_payment extends MAIN_Controller {

    public $_api_context = array();

    public function __construct()
    {


    }

    public function process_payment(){

        $item_data = $this->session->payment_paystack_cart;


        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.mercadopago.com/v1/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'transaction_amount' => RequestInput::post("transactionAmount"),
                'token' => RequestInput::post("token"),
                'description' => RequestInput::post("description"),
                'installments' => RequestInput::post("installments"),
                'payment_method_id' => RequestInput::post("paymentMethodId"),
                'issuer_id' => RequestInput::post("issuer"),
                'payer' => array(
                    'email' => RequestInput::post("email")
                )
            ),
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer ".ConfigManager::getValue('MERCADO_PAGO_ACCESS_TOKEN'),
                "Cache-Control: no-cache",
            ),
        ));

        //execute post
        $result = curl_exec($curl);
        $result = json_decode($result);

        if($result->status == "approved"){
            $transaction_id = $result->id;
            $payerId = $result->payer->id;
            $callback = $item_data['callback_success_url']."&method=mercadopago@mp=".PaymentsProvider::MERCADO_PAGO."&paymentId=".$transaction_id."&PayerID=".$payerId;
            redirect($callback);
        }else{
            redirect($item_data['callback_error_url'].'#error-mercadopago-verify02');
        }

    }


}
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mycoolpay extends MAIN_Controller
{

    public $_api_context;

    function __construct()
    {
        parent::__construct();
    }

    public function success()
    {

        $sess = RequestInput::get('sess');
        $invoiceID = RequestInput::get('invoice');

        if(SessionManager::getData('hash_id') != $sess){
            echo "Error Session";
            return;
        }

        $sessData = $this->session->userdata('sess_data_'.$sess);
        $successCallback = $sessData['success'];
        $errorCallback =  $sessData['error'];

        $invoice = $this->mPaymentModel->getInvoice($invoiceID);
        redirect($successCallback);

    }


    public function error()
    {

        $sess = RequestInput::get('sess');
        $invoiceID = RequestInput::get('invoice');


        if(SessionManager::getData('hash_id') != $sess){
            echo "Error Session";
            return;
        }

        $sessData = $this->session->userdata('sess_data_'.$sess);
        $successCallback = $sessData['success'];
        $errorCallback =  $sessData['error'];


        $invoice = $this->mPaymentModel->getInvoice($invoiceID);
        redirect($errorCallback);
    }

    public function cancel()
    {

        $sess = RequestInput::get('sess');
        $invoiceID = RequestInput::get('invoice');
        $successCallback = base64_decode(RequestInput::get('successCallback'));
        $errorCallback = base64_decode(RequestInput::get('successCallback'));

        if(SessionManager::getData('hash_id') != $sess){
            echo "Error Session";
            return;
        }

        $invoice = $this->mPaymentModel->getInvoice($invoiceID);
        $callback = PaymentsProvider::getErrorCallback($invoice->module);
        redirect($callback);
    }

    public function callback()
    {

        $sess = RequestInput::get('sess');
        $invoiceID = RequestInput::get('invoice');
        $successCallback = base64_decode(RequestInput::get('successCallback'));
        $errorCallback = base64_decode(RequestInput::get('successCallback'));

        if(SessionManager::getData('hash_id') != $sess){
            echo "Error Session";
            return;
        }



    }

    public function verify($params)
    {

        $this->session->set_userdata(array(
            'sess_data_'.$params['client']['sess'] => array(
                'success' => $params['callback_success_url'],
                'error' => $params['callback_error_url'],
            )
        ));

        $public_key = ConfigManager::getValue("MY_COOLPAY_KEY_ID");

        $url = "https://my-coolpay.com/api/{$public_key}/paylink?invoice=".$params['invoice']['id'].'&sess='.$params['client']['sess'];


        $data = array(
            "transaction_amount" => $params['details_subtotal'],
            "transaction_currency" => $params['currency'],
            "transaction_reason" => $params['items'][0]['name'].' x '.$params['items'][0]['quantity'],
            "app_transaction_ref" => "invoice_".$params['invoice']['id']."--".time(),
            "customer_phone_number" =>  $params['client']['phone'],
            "customer_name" => $params['client']['name'],
            "customer_email" => $params['client']['email'],
            "customer_lang" => $params['client']['lang'],
        );

        $data_string = json_encode($data);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json'
        ));

        $result = curl_exec($ch);

        curl_close($ch);

        $resultDecoded = json_decode($result,JSON_OBJECT_AS_ARRAY);

        if($resultDecoded['status'] == 'success'){
            redirect($resultDecoded['payment_url']);
        }else{
            echo $result;exit();
        }

    }


}
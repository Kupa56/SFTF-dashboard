<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH."modules/payment/libraries/razorpay/razorpay-php/Razorpay.php");
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;


class Razorpay extends MAIN_Controller {

    public $_api_context;

    function  __construct()
    {
        parent::__construct();
        $this->_api_context = new Api(RAZORPAY_KEY_ID, RAZORPAY_SECRET_KEY);
    }

    public function create_order($params){


        if(!isset($params['details_subtotal']))
            die( "'details_subtotal' doesn't exists!");


        $extra_amount = 0;

        if(isset($params["extras"]))
        foreach ($params["extras"] as $key => $value) {
            $extra_amount = $extra_amount + doubleval($value);
        }


        $_SESSION['payable_amount'] = $params['details_subtotal'];
        $_SESSION['callback_success_url'] = $params['callback_success_url'];
        $_SESSION['callback_error_url'] = $params['callback_error_url'];

        $amount = ceil( $params['details_subtotal'] * 100);

        $razorpayOrder = $this->_api_context->order->create(array(
            'receipt'         => rand(),
            'amount'          => $amount, // 2000 rupees in paise
            'currency'        => $params['currency'],
            'payment_capture' => 1 // auto capture
        ));

        $amount = $razorpayOrder['amount'];
        $razorpayOrderId = $razorpayOrder['id'];
        $options = $this->prepareData($amount,$razorpayOrderId);
        $options['callback_url'] = $params['callback_success_url'];
        $data['options'] = json_encode($options,JSON_FORCE_OBJECT);
        $data['amount'] = Currency::parseCurrencyFormat($params['details_subtotal'],$params['currency']);

        $this->load->view("payment/razorpay/charge",$data);

        $_SESSION['razorpay_order_id'] = $razorpayOrderId;

    }


    /**
     * This function preprares payment parameters
     * @param $amount
     * @param $razorpayOrderId
     * @return array
     */
    public function prepareData($amount,$razorpayOrderId)
    {

        $logo = ImageManagerUtils::getValidImages(APP_LOGO);
        $imageUrl = adminAssets("images/logo.png");
        if(!empty($logo)){
            $imageUrl = $logo[0]["560_560"]["url"];
        }

        $data = array(
            "key" => RAZORPAY_KEY_ID,
            "amount" => $amount,
            "name" => APP_NAME,
            "description" => "",
            "image" => $imageUrl,
            "prefill" => array(
                "name"  => RequestInput::post('name'),
                "email"  => RequestInput::post('email'),
                "contact" => RequestInput::post('contact'),
            ),
            "notes"  => array(
                "address"  => "Hello World",
                "merchant_order_id" => rand(),
            ),
            "theme"  => array(
                "color"  => DASHBOARD_COLOR
            ),
            "order_id" => $razorpayOrderId,
        );
        return $data;
    }

    /**
     * This function verifies the payment,after successful payment
     */
    public function verify()
    {
        $success = true;
        $error = "payment_failed";
        if (empty($_POST['razorpay_payment_id']) === false) {
            $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_SECRET_KEY);
            try {
                $attributes = array(
                    'razorpay_order_id' => $_SESSION['razorpay_order_id'],
                    'razorpay_payment_id' => $_POST['razorpay_payment_id'],
                    'razorpay_signature' => $_POST['razorpay_signature']
                );
                $api->utility->verifyPaymentSignature($attributes);
            } catch (SignatureVerificationError $e) {
                $success = false;
                $error = 'Razorpay_Error : ' . $e->getMessage();
            }
        }
        if ($success === true) {
            /**
             * Call this function from where ever you want
             * to save save data before of after the payment
             */

            $callback_success_url = $_SESSION['callback_success_url'];
            $callback_success_url = $callback_success_url."&method=razorpay&paymentId=".$attributes['razorpay_payment_id']."&PayerID=0";

            $_SESSION['callback_success_url'] = "";
            redirect($callback_success_url);
        } else {

            $callback_error_url= $_SESSION['callback_error_url'];
            $_SESSION['callback_error_url'] = "";
            redirect($callback_error_url);
        }


    }



}
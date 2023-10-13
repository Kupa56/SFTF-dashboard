<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'modules/payment/libraries/paypal-php-sdk/paypal/rest-api-sdk-php/sample/bootstrap.php'); // require paypal files

use PayPal\Api\ItemList;
use PayPal\Api\Payment;
use PayPal\Api\Amount;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RefundRequest;
use PayPal\Api\Sale;
use PayPal\Api\Item;
use PayPal\Api\Details;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\AgreementStateDescriptor;


use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;


use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\ShippingAddress;


use \PayPal\Api\VerifyWebhookSignature;
use \PayPal\Api\WebhookEvent;
use PayPal\Exception\PayPalConnectionException;


use PayPal\Api\Refund;

class Paypal extends MAIN_Controller
{
    public $_api_context;

    function  __construct()
    {
        parent::__construct();
        $this->load->model('payment/paypal_model', 'paypal');
        $this->load->config('payment/paypal-config');
        // paypal credentials

        $this->_api_context = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                $this->config->item('paypal_client_id'),
                $this->config->item('paypal_secret')
            )
        );


        // setup PayPal api context
        $this->_api_context->setConfig($this->config->item('paypal_settings'));

    }


    function index(){
        $this->load->view('content/payment_credit_form');
    }



    function create_payment_with_paypal($params,$return=FALSE)
    {
        print_r($params);

        // setup PayPal api context
        $this->_api_context->setConfig($this->config->item('paypal_settings'));


        // ### Payer
        // A resource representing a Payer that funds a payment
        // For direct credit card payments, set payment method
        // to 'credit_card' and add an array of funding instruments.
        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod('paypal');


        // ### Itemized information
        // (Optional) Lets you specify item wise
        // information


        $itemList = new ItemList();

        if(isset($params["items"])){
            foreach ($params["items"] as $itemValue){
                $item = new \PayPal\Api\Item();
                $item->setCurrency($itemValue['name']);
                $item->setQuantity(1);
                $item->setPrice($itemValue['price']*$itemValue['quantity']);
                $item->setSku($itemValue['sku']);
                $item->setDescription($itemValue['description']);
                $item->setCurrency($itemValue['currency']);
                $itemList->addItem($item);
            }
        }

        if(isset($params["extras"])){

            $extra_amount = 0;

            foreach ($params["extras"] as $key => $value){

                $extra_amount = $extra_amount + doubleval($value);

                $item = new \PayPal\Api\Item();
                $item->setCurrency($params['currency']);
                $item->setQuantity(1);
                $item->setPrice($extra_amount);
                $item->setSku(uniqid());
                $item->setDescription("");
                $itemList->addItem($item);

               // $params['details_subtotal'] = $params['details_subtotal'] + $extra_amount;

            }

        }


        //
        $amount = new \PayPal\Api\Amount();
        $amount->setTotal($params['details_tax'] + $params['details_subtotal']);
        $amount->setCurrency($params['currency']);



       // $payee = new \PayPal\Api\Payee();
       // $payee->setEmail("x3@domain.me");

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);
       // $transaction->setPayee($payee);
        $transaction->setInvoiceNumber(uniqid());

        $redirectUrls = new \PayPal\Api\RedirectUrls();
        $redirectUrls->setReturnUrl($params['callback_success_url'])
            ->setCancelUrl($params['callback_error_url']);


        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);



        try {
            $payment->create($this->_api_context);
        } catch (Exception $ex) {
            echo "Error in Transaction";
            if(ENVIRONMENT == "development"){
                echo "<pre>";
                print_r($ex->getMessage());
            }
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            //ResultPrinter::printError("Created Payment Using PayPal. Please visit the URL to Approve.", "Payment", null, $ex);
            exit(1);
        }
        foreach($payment->getLinks() as $link) {
            if($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }


        if($return==FALSE){
            if(isset($redirect_url)) {
                /** redirect to paypal **/
                redirect($redirect_url);
            }

            echo 'Unknown error occurred';
            redirect($params['callback_error_url']);
        }else{
            if(isset($redirect_url)) {
                /** redirect to paypal **/
                return $redirect_url;
            }

            return $params['callback_error_url'];
        }


    }

    public function getPaymentStatus($data=array())
    {

        // paypal credentials
        /** Get the payment ID before session clear **/
        $payment_id = $data['paymentId'] ;
        $PayerID =  $data['payerID'] ;
        $token =  $data['token'] ;
        /** clear the session payment ID **/

        if (empty($PayerID) || empty($token)) {
            $this->session->set_flashdata('success_msg','Payment failed');
            echo 0;return;
        }

        $payment = Payment::get($payment_id,$this->_api_context);

        /** PaymentExecution object includes information necessary **/
        /** to execute a PayPal account payment. **/
        /** The payer_id is added to the request query parameters **/
        /** when the user is redirected from paypal back to your site **/
        $execution = new PaymentExecution();
        $execution->setPayerId(RequestInput::get('PayerID'));

        /**Execute the payment **/
        $result = $payment->execute($execution,$this->_api_context);

        //  DEBUG RESULT, remove it later **/
        if ($result->getState() == 'approved') {

            $trans = $result->getTransactions();

            // item info
            $Subtotal = $trans[0]->getAmount()->getDetails()->getSubtotal();
            $Tax = $trans[0]->getAmount()->getDetails()->getTax();

            $payer = $result->getPayer();
            // payer info //
            $PaymentMethod =$payer->getPaymentMethod();
            $PayerStatus =$payer->getStatus();
            $PayerMail =$payer->getPayerInfo()->getEmail();

            $relatedResources = $trans[0]->getRelatedResources();
            $sale = $relatedResources[0]->getSale();
            // sale info //
            $saleId = $sale->getId();
            $CreateTime = $sale->getCreateTime();
            $UpdateTime = $sale->getUpdateTime();
            $State = $sale->getState();
            $Total = $sale->getAmount()->getTotal();
            /** it's all right **/
            /** Here Write your database logic like that insert record or value in database if you want **/
            $this->paypal->create($Total,$Subtotal,$Tax,$PaymentMethod,$PayerStatus,$PayerMail,$saleId,$CreateTime,$UpdateTime,$State);
            $this->session->set_flashdata('success_msg','Payment success');
            echo 1;return;
        }
        $this->session->set_flashdata('success_msg','Payment failed');

        echo -1;return;
    }

    function success(){
        $this->load->view("content/success");
    }
    function cancel(){
        $this->load->view("content/cancel");
    }

    function load_refund_form(){
        $this->load->view('content/Refund_payment_form');
    }

    function refund_payment(){

        $refund_amount = RequestInput::post('refund_amount');
        $saleId = RequestInput::post('sale_id');
        $paymentValue =  (string) round($refund_amount,2); ;

        // ### Refund amount
        // Includes both the refunded amount (to Payer)
        // and refunded fee (to Payee). Use the $amt->details
        // field to mention fees refund details.
                $amt = new Amount();
                $amt->setCurrency('USD')
                    ->setTotal($paymentValue);

        // ### Refund object
                $refundRequest = new RefundRequest();
                $refundRequest->setAmount($amt);

        // ###Sale
        // A sale transaction.
        // Create a Sale object with the
        // given sale transaction id.
                $sale = new Sale();
                $sale->setId($saleId);
                try {
                    // Refund the sale
                    // (See bootstrap.php for more on `ApiContext`)
                    $refundedSale = $sale->refundSale($refundRequest, $this->_api_context);
                } catch (Exception $ex) {
                    // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                    ResultPrinter::printError("Refund Sale", "Sale", null, $refundRequest, $ex);
                    exit(1);
                }

        // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
                ResultPrinter::printResult("Refund Sale", "Sale", $refundedSale->getId(), $refundRequest, $refundedSale);

        return $refundedSale;
    }


    function make_refund($params=array()){


        // ### Refund object
        $refundRequest = new RefundRequest();
        $refundRequest->setReason("Refund amount!");

        $sale = new Sale();
        $sale->setId($params['transaction_id']);

        try {
            // Create a new apiContext object so we send a new
            // Refund the sale
            // (See bootstrap.php for more on `ApiContext`)

            $refundedSale = $sale->refundSale($refundRequest, $this->_api_context);

            echo json_encode(array(
                Tags::SUCCESS=>1,
                Tags::RESULT=>$refundedSale
            ));return;
        } catch (Exception $ex) {

            echo json_encode(array(
                Tags::SUCCESS=>0,
                Tags::ERRORS=> array(
                    "refund" => $ex->getMessage()
                )
            ));return;
            exit(1);
        }
        /*
        // get PayPal access token via cURL
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $client_id.":".$secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");


        $output = curl_exec($ch);
        $json = json_decode($output);


        $token = $json->access_token; // this is our PayPal access token

        // refund PayPal sale via cURL
        $header = Array(
            "Content-Type: application/json",
            "Authorization: Bearer $token",
        );
        $ch = curl_init($params['link']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{}');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $res = json_decode(curl_exec($ch));
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // if res has a state, retrieve it
        if(isset($res->state)){
            $state = $res->state;
        }else{
            $state = NULL; // otherwise, set to NULL
        }

        // if we have a state in the response...
        if($state == 'completed'){
            // the refund was successful
            echo json_encode(array(
                Tags::SUCCESS=>1
            ));return;
        }else{

            // the refund failed
            $errorName = $res->name; // ex. 'Transaction Refused.'
            $errorReason = $res->message; // ex. 'The requested transaction has already been fully refunded.'

            echo json_encode(array(
                Tags::SUCCESS=>0,
                Tags::ERRORS=> array(
                    $errorName => $errorReason
                )
            ));return;
        }
        */

    }

}
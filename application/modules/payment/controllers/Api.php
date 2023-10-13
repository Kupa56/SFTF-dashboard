<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {


    public function __construct()
    {
        parent::__construct();
    }


   public function getPayments(){

        $module = RequestInput::post('module');

   }

   public function updateInvoiceApplePay(){

       $invoiceId = RequestInput::post('invoiceId');
       $transactionId = RequestInput::post('transactionId');
       $paymentMethod = RequestInput::post('paymentMethod');
       $user_id = RequestInput::post('user_id');


       $result = $this->mPaymentModel->updateInvoiceApplePay($invoiceId,$paymentMethod,$transactionId,$user_id);

       if($result){
           echo json_encode(array(Tags::SUCCESS=>1));return;
       }else{
           echo json_encode(array(Tags::SUCCESS=>0));return;
       }

   }

   public function getInvoiceForApplePay(){

       $invoiceId = RequestInput::post('invoiceId');
       $result = $this->mPaymentModel->getInvoiceForApplePay($invoiceId);

       if($result != NULL){
           echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$result));return;
       }else{
           echo json_encode(array(Tags::SUCCESS=>0,"s"=>$invoiceId));return;
       }



   }

}
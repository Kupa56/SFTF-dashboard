<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller  {


    public function __construct()
    {
        parent::__construct();


    }

    public function get_payment_link()
    {

        $payment_method = RequestInput::post('payment');

        //booking to invoice
        $booking_id = RequestInput::post('booking_id');
        $user_id = RequestInput::post('user_id');
        $user_token = RequestInput::post('user_token');


        $result = $this->mBookingPayment->convert_booking_to_invoice($user_id,$booking_id);

        if($result[Tags::SUCCESS]==1 && $result[Tags::RESULT]>0){

            if(TokenSetting::isValid($user_id,"logged",$user_token)){

                $token = TokenSetting::getValid($user_id,"logged",$user_token);
                if($token!=NULL){
                    $this->mUserBrowser->refreshData($token->uid);
                }
            }

            //process_payment
            $payment_link = site_url("payment/process_payment?invoiceid=".$result[Tags::RESULT]."&mp=".$payment_method);
            $result[Tags::RESULT] = $payment_link;
        }else{
            $result[Tags::RESULT] = "";
        }

        echo json_encode($result);

    }



    public function getPayments(){

        $logged_user_id = $this->requireAuth();

        $payments = PaymentsProvider::getPayments("booking_payment");

        foreach ($payments as $k => $payment){
            if($payment['id'] == PaymentsProvider::WALLET_ID){
                $balance = $this->mWalletModel->getBalance($logged_user_id);
                $payments[$k]['description'] = Translate::sprintf("Pay using your balance: %s",array(Currency::parseCurrencyFormat($balance,DEFAULT_CURRENCY)));
            }
        }


        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$payments),JSON_FORCE_OBJECT);return;

   }

}
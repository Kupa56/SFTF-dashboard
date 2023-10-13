<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();


    }


    public function payment_update_status(){

        if(!GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $booking_id = intval(RequestInput::post('booking_id'));
        $payment_status_id = RequestInput::post('payment_status');
        $transactionId = RequestInput::post('transactionId');

        $result = $this->mBookingModel->update_payment_status($booking_id,$payment_status_id, $transactionId);

        echo json_encode($result);
    }

    public function change_booking_status(){

        if(!GroupAccess::isGranted('booking',GRP_MANAGE_BOOKING)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $booking_id = RequestInput::post('booking_id');
        $status_id = RequestInput::post('status');
        $message = RequestInput::post('message');


        $this->mBookingModel->change_booking_status($booking_id, $status_id, $message);


        echo json_encode(array(Tags::SUCCESS=>1));
    }

    public function cancelBookingClient(){

        $booking_id = intval(RequestInput::post('booking_id'));
        $status_id = -1;
        $client_id = SessionManager::getData("id_user");
        $message = Translate::sprintf("Client: Order Canceled #%s",array($booking_id));
        $this->mBookingModel->change_booking_status($booking_id, $status_id, $message,$client_id);

        echo json_encode(array(Tags::SUCCESS=>1));
    }

    public function sendMessageBookingClient(){

        $booking_id = intval(RequestInput::post('booking_id'));
        $booking = $this->mBookingModel->getBooking($booking_id);

        if($booking == NULL){
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }

        $userData = $this->mUserModel->getUserData($booking['store_id']);

        if($userData == NULL){
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }


        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>admin_url("messenger/messages?username=".$userData['username'])));
    }




}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("booking/Booking_model", "mOrderModel");
    }


    public function createBooking()
    {

        $this->requireAuth();

        echo json_encode($this->mBookingModel->createBooking(array(
            "store_id" => intval(RequestInput::post("store_id")),
            "req_cf_data" => RequestInput::post("req_cf_data"),
            "req_cf_id" => RequestInput::post("req_cf_id"),
            "cart" => RequestInput::post("cart"),
            "user_id" => RequestInput::post("user_id"),
            "user_token" => RequestInput::post("user_token"),
        )));

    }


    public function getStatus()
    {
        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>Booking_payment::PAYMENT_STATUS));

    }



    public function getBookings()
    {

        $result = $this->mBookingModel->getBookings(array(
            "id" => RequestInput::post("booking_id"),
            "user_id" => intval(RequestInput::post("user_id")),
            "limit" => intval(RequestInput::post("limit")),
            "page" => intval(RequestInput::post("page")),
            "except" => RequestInput::post("except"),
            "order_by" => RequestInput::post("order_by"),
        ));

        echo json_encode($result, JSON_FORCE_OBJECT);
    }

    public function checkBooking()
    {

        $user_id = $this->requireAuth();

        $booking_id = intval(RequestInput::post("id"));
        $business_user_id = intval(RequestInput::post("business_user_id"));

        $params = array(
            'owner_id' => $business_user_id,
            'booking_id' => $booking_id,
            'limit' => 1,
        );

        $result = $this->mBookingModel->getBookings($params);

        echo json_encode($result, JSON_FORCE_OBJECT);
    }


    public function updateBooking()
    {

        $this->requireAuth();

        $booking_id = intval(RequestInput::post("id"));
        $business_user_id = intval(RequestInput::post("business_user_id"));
        $status_id = intval(RequestInput::post("status"));

        if($business_user_id == 0){
            echo json_encode(array(Tags::SUCCESS=>0), JSON_FORCE_OBJECT);return;
        }

        $result = $this->mBookingModel->change_booking_status($booking_id, $status_id, "",0,$business_user_id);

        echo json_encode($result, JSON_FORCE_OBJECT);
    }

    public function updateBookingClient()
    {

        $this->requireAuth();

        $booking_id = intval(RequestInput::post("id"));
        $user_id = intval(RequestInput::post("user_id"));
        $status_id = intval(RequestInput::post("status"));


        $result = $this->mBookingModel->change_booking_status($booking_id, $status_id, _lang("You've changed status of your reservation"),$user_id);

        echo json_encode($result, JSON_FORCE_OBJECT);
    }




}

/* End of file CategoryDB.php */
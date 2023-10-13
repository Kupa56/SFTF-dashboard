<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stripe_api extends MAIN_Controller {

    public $_api_context;

    function  __construct()
    {
        parent::__construct();
        $this->load->config('payment/stripe-config');


    }

    public function getAmount(){

        $item_data = $this->session->payment_stripe_cart;
        return Currency::parseCurrencyFormat($item_data['details_subtotal'],$item_data['currency']);

    }

    public function create_payment_with_stripe(){


        if(!SessionManager::isLogged()){
            echo "Login is required";
            return;
        }

        $stripeToken = RequestInput::post('stripeToken');
        $user_email = SessionManager::getData("email");
        $item_data = $this->session->payment_stripe_cart;
        $items = $item_data["items"];


        try {


            $extra_amount = 0;

            if(isset($item_data["extras"])){
                foreach ($item_data["extras"] as $key => $value) {
                    //$extra_amount = $extra_amount + doubleval($value);
                }
            }

            $this->load->library("payment/stripeapi");

            \Stripe\Stripe::setApiKey($this->config->item('stripe_secret_key'));

            $customer = \Stripe\Customer::create(array(
                'email' => $user_email,
                'description' => 'Payment date: '.date("Y-m-d H:i:s",time()),
                'source'   => $stripeToken
            ));


            $amount = ceil(($item_data['details_subtotal']+$extra_amount) * 100);

            $result = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $amount,
                'currency' => $item_data['items'][0]['currency']
            ));


            if($result->paid == 1){

                $transaction_id = $result->id;
                $callback = $item_data['callback_success_url']."&method=stripe&paymentId=".$transaction_id."&PayerID=".$customer->id;
                redirect($callback);

            }


        } catch (Exception $e) {
            echo "<h3>Error</h3>";
            print_r($e->getMessage());
        }

    }


	public function webhook(){
		$this->load->library('stripeapi');
		\Stripe\Stripe::setApiKey(get_option('stripe_secret_key'));

		$payload = @file_get_contents('php://input');
		$event = null;

		$data = json_decode($payload);

		if(is_object($data)){
			try {
			    $event = \Stripe\Event::retrieve($data->id);
			} catch(\UnexpectedValueException $e) {
			    // Invalid payload
			    http_response_code(400);
			    exit();
			}

			// Handle the event
			switch ($event->type) {
			    case 'payment_intent.succeeded':
			        $result = $event->data->object; // contains a \Stripe\PaymentIntent

					$customer = $result->customer;
					$subscription = $this->model->get("*", "general_payment_subscriptions", "", "id", "DESC");

					$id = $subscription->package;
					$plan = $subscription->plan;
					$paymentId = $result->id;

					$package = $this->model->get("*", $this->tb_packages, "id= '".$id."'  AND status = 1");
					if(!empty($package)){
						$data = array(
							'ids' => ids(),
							'uid' => $subscription->uid,
							'package' => $package->id,
							'type' => 'stripe_charge',
							'transaction_id' => $paymentId,
							'amount' => $result->amount/100,
							'plan' => $plan,
							'status' => 1,
							'created' => NOW
						);
						$this->db->insert($this->tb_payment_history, $data);
						$this->update_package($package, $plan, $subscription->uid);

						echo "true";
					}else{
						echo "false";
					}
			        break;
			    case 'payment_method.attached':
			        break;
			    default:
			        http_response_code(400);
			        exit();
			}

			http_response_code(200);
		}
	}

}
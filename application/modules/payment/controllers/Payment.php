<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

require_once(APPPATH . 'modules/payment/libraries/paypal-php-sdk/autoload.php');


class Payment extends MAIN_Controller
{

    public $_api_context;

    const MODULE = "payment";
    const PAID_SUCCESS = "p_success";
    const PAID_FAILED = "p_failed";

    public function __construct()
    {
        parent::__construct();

        /////// register module ///////
        $this->init("payment");

    }

    public function onLoad()
    {
        define('CONFIG_PAYMENT', 'config_payment');
        define('DISPLAY_LIST_TRANSACTIONS', 'display_transactions');
        define('DISPLAY_LIST_BILLING', 'display_billing');
        define('MANAGE_TAXES', 'manage_taxes');

        //load model
        $this->load->model("payment/payment_model", 'mPaymentModel');
        $this->load->model("setting/currency_model", 'mCurrencyModel');
        $this->load->model("payment/tax_model", 'mTaxModel');
        $this->load->helper("payment/payment");

    }


    public function onCommitted($isEnabled)
    {

        if (!$isEnabled)
            return;

        $this->load->config('config');

        //init config
        if (!defined('DEFAULT_TAX'))
            $this->mConfigModel->save('DEFAULT_TAX', 0);


        AdminTemplateManager::registerMenu(
            'payment',
            "payment/menu",
            11
        );


        if ($this->mUserBrowser->isLogged()) {
            $user_id = $this->mUserBrowser->getData("id_user");
        }

        //setup payment for waller
        $payment_redirection = site_url("payment/make_payment");
        $payment_callback_success = site_url("payment/payment_success");
        $payment_callback_error = site_url("payment/payment_error");


        $payments = array(
            array(
                'id' => PaymentsProvider::PAYPAL_ID,
                'payment' => _lang("PayPal"),
                'image' => AdminTemplateManager::assets("payment", "img/paypal-logo.png"),
                'description' => 'Pay using PayPal.com'
            ),
            array(
                'id'=> PaymentsProvider::STRIPE_ID,
                'payment'=>  _lang("Debit & Credit Card"),
                'image'=> AdminTemplateManager::assets("payment","img/stripe.png"),
                'description'=>  _lang('Pay using Stripe.com')
            ),
            array(
                'id' => PaymentsProvider::RAZORPAY_ID,
                'payment' => _lang("Debit & Credit Card"),
                'image' => AdminTemplateManager::assets("payment", "img/razorpay-logo.png"),
                'description' => _lang('Pay using razorpay.com')
            ),
            array(
                'id'=> PaymentsProvider::FLUTTERWAVE,
                'payment'=> _lang('Debit & Credit Card'),
                'image'=> AdminTemplateManager::assets("payment","img/flutterwave-logo.png"),
                'description'=> 'Pay using flutterwave.com'
            ),
            array(
                'id'=> PaymentsProvider::PAY_STACK,
                'payment'=> _lang('Debit & Credit Card'),
                'image'=> AdminTemplateManager::assets("payment","img/paystack.png"),
                'description'=> 'Pay using paystack.com'
            ),
            array(
                'id'=> PaymentsProvider::MERCADO_PAGO,
                'payment'=> _lang('Debit & Credit Card'),
                'image'=> AdminTemplateManager::assets("payment","img/mercadopago.png"),
                'description'=> 'Pay using mercadopago.com'
            ),

           /* array(
                'id'=> PaymentsProvider::MY_COOLPAY,
                'payment'=> _lang('Paiement Mobile (Orange Money, MTN Momo...)'),
                'image'=> AdminTemplateManager::assets("payment","img/my-coolpay.png"),
                'description'=> 'Pay using my-coolpay.com'
            ),*/

            array(
                'id'=> PaymentsProvider::BANK_TRANSFER,
                'payment'=> _lang('Transfer bank'),
                'image'=> AdminTemplateManager::assets("payment","img/bank.png"),
                'description'=> 'Make transfer to the bank'
            ),
            array(
                'id'=> PaymentsProvider::WALLET_ID,
                'payment'=> _lang('MyWallet'),
                'image'=> AdminTemplateManager::assets("payment","img/wallet-logo.png"),
                'description'=> 'Pay using your digital wallet'
            ),
            array(
                'id'=> PaymentsProvider::COD_ID,
                'payment'=> _lang('Cash payment'),
                'image'=> AdminTemplateManager::assets("payment","img/cod-logo.png"),
                'description'=> 'Pay when you receive your item(s)'
            ),
        );



        PaymentsProvider::provide("default",$payments ,
            $payment_redirection,
            $payment_callback_success,
            $payment_callback_error
        );


        //add components
        AdminTemplateManager::addScript($this->load->view('payment/plug/header/header_script', NULL, TRUE));

    }

    private function setupWalletPayments(){

        //setup payment for waller
        $payment_redirection = site_url("payment/make_payment");
        $payment_callback_success = site_url("payment/wallet_payment_confirm");
        $payment_callback_error = site_url("payment/payment_error");


        $payments = PaymentsProvider::getPayments("default");


        PaymentsProvider::provide("wallet",$payments,
            $payment_redirection,
            $payment_callback_success,
            $payment_callback_error
        );

        PaymentsProvider::excludePayments('wallet',array(PaymentsProvider::WALLET_ID,PaymentsProvider::COD_ID));

        //disable taxes
        TaxManager::disable('wallet');

    }

    public function onEnable()
    {
        $this->registerModuleActions();
        $this->mPaymentModel->setup_config();
    }

    public function onInstall()
    {

        $this->mWalletModel->createTables();
        $this->mWalletModel->createTableWT();

        $this->mPaymentModel->createTables();
        $this->mPaymentModel->update_fields();
        $this->mPaymentModel->setup_config();


        return TRUE;
    }

    public function onUpgrade()
    {
        $this->mWalletModel->createTables();
        $this->mWalletModel->createTableWT();

        $this->mPaymentModel->createTables();
        $this->mPaymentModel->update_fields();
        $this->mPaymentModel->setup_config();

        $this->registerModuleActions();

        return TRUE;
    }

    private function registerModuleActions()
    {

        GroupAccess::registerActions("payment", array(
            CONFIG_PAYMENT,
            DISPLAY_LIST_TRANSACTIONS,
            DISPLAY_LIST_BILLING,
            MANAGE_TAXES
        ));

    }


    public function make_payment()
    {

        $css = AdminTemplateManager::assets("payment", 'css/style.css');
        AdminTemplateManager::addCssLibs($css);

        $id = RequestInput::get("id");
        $data['invoice'] = $this->mPaymentModel->getInvoice($id);
        $data['title'] = Translate::sprint("Payment");

        $data['cancel_url'] = admin_url();

        if ($data['invoice'] != NULL) {
            $this->load->view("payment/client_view/html/make-payment", $data);
        } else
            redirect(admin_url());

    }

    public function process_payment()
    {

        $id = intval(RequestInput::get("invoiceid"));

        $callback_s1 = RequestInput::get("callback_s1");
        $callback_e1 = RequestInput::get("callback_e1");

        $method_payment = intval(RequestInput::get("mp"));
        $invoice = $this->mPaymentModel->getInvoice($id);



        if ($invoice == NULL) {
            redirect(site_url('payment/payment_error?invoiceid=' . $id));
        }


        if ($invoice->status == 1){
            redirect("error404");
        }


        $token = TokenSetting::generateToken("processPayment", $id);
        $callback = PaymentsProvider::getErrorCallback($invoice->module);

        $params = array(
            'currency' => $invoice->currency,
            'details_tax' => 0,
            'details_subtotal' => $invoice->amount,
            'callback_error_url' => $callback . '?invoiceid=' . $id,
        );


        $callback = PaymentsProvider::getSuccessCallback($invoice->module);


        //create call payment when it's done
        if ($method_payment == 1)
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=paypal&key=' . $token;
        else if ($method_payment == 2)
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=stripe&key=' . $token;
        else
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method='.$method_payment.'&key=' . $token;

        if($method_payment>0){
            $params['callback_success_url'] .= '&mp='.$method_payment;
        }

        //configure alternative callback for successful message
        if($callback_s1 != ""){
            $params['callback_success_url'] = $params['callback_success_url'].'&callbackAltSuccess='.$callback_s1;
        }

        //configure alternative callback for error message
        if($callback_e1 != ""){
            $params['callback_error_url'] = $params['callback_error_url'].'&callbackAltError='.$callback_e1;
        }

        $params['details_subtotal'] = 0;

        //adjust total
        $items = json_decode($invoice->items);
        foreach ($items as $k => $item) {
            $params['details_subtotal'] = $params['details_subtotal'] + ($item->price * $item->qty);
        }

        if (!TaxManager::isDisabled($invoice->module)) {
            if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {
                $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                if ($tax != NULL) {
                    $tax_value = (($tax['value'] / 100) * $params['details_subtotal']);
                    $params['details_subtotal'] = $params['details_subtotal'] + $tax_value;
                }
            } else if (defined('DEFAULT_TAX') and DEFAULT_TAX == -2) {
                if (defined('MULTI_TAXES') and count(MULTI_TAXES) > 0)
                    $litTaxes = json_decode(MULTI_TAXES, JSON_OBJECT_AS_ARRAY);
                $newAmount = $params['details_subtotal'];
                $multiTaxes = 0;
                foreach ($litTaxes as $value) {
                    $mTax = $this->mTaxModel->getTax($value);
                    if ($mTax != NULL) {
                        $tax_value = (($mTax['value'] / 100) * $params['details_subtotal']);
                        $multiTaxes = $multiTaxes + $tax_value;
                        $newAmount = $newAmount + $tax_value;
                    }
                }
                $params['details_subtotal'] = $newAmount;
            }
        }


        $extras = jsonDecode($invoice->extras, JSON_OBJECT_AS_ARRAY);
        if ($extras != null && is_array($extras)) {
            foreach ($extras as $k => $value) {
                $params['extras'][$k] = doubleval($value);
                $params['details_subtotal'] = $params['details_subtotal'] + doubleval($value);
            }
        }


        if (isset($params['details_subtotal'])) {
            $this->db->where('id', $invoice->id);
            $this->db->update('invoice', array(
                'amount' => $params['details_subtotal']
            ));
        }

        if ($method_payment == PaymentsProvider::PAYPAL_ID) { //paypal

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;

            }


            Modules::run('payment/paypal/create_payment_with_paypal', $params);

        } else if ($method_payment == PaymentsProvider::STRIPE_ID) { //stripe

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_stripe_cart' => $params
            ));

            $this->load->view("payment/stripe/charge");

        } else if ($method_payment == PaymentsProvider::COD_ID) {

            $this->mPaymentModel->updateInvoiceCOD($invoice->id, "cod");

            if(isset($params['callback_success_url'])){
                $callback = $params['callback_success_url'];
            }else{
                $callback = PaymentsProvider::getSuccessCallback($invoice->module);
                $callback = $callback . '?invoiceid=' . $id . '&method=cod&key=' . $token;
            }

            $result = Modules::run($invoice->module . '/payment_success', array(
                'invoiceId' => $invoice->id
            ));


            if ($result == TRUE) {
                redirect($callback);
            } else {
                $callback_error = PaymentsProvider::getErrorCallback($invoice->module);
                redirect($callback_error);
            }

        } else if ($method_payment == PaymentsProvider::WALLET_ID) {

            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=wallet&key=' . $token;

            $result = $this->mWalletModel->releaseBalanceTransaction(
                SessionManager::getData("id_user"),
                $invoice->amount
            );

            if ($result) {
                $this->mPaymentModel->updateInvoice($invoice->id, "wallet", "wallet:" .$result, $token);
                redirect($callback);
            } else {
                $callback_error = PaymentsProvider::getErrorCallback($invoice->module);
                redirect($callback_error);
            }

        } else if ($method_payment == PaymentsProvider::RAZORPAY_ID) { //stripe

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_razorpay_cart' => $params
            ));


            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=razorpay&key=' . $token;

            echo Modules::run("payment/razorpay/create_order", $params);


        } else if ($method_payment == PaymentsProvider::FLUTTERWAVE) { //FLUTTERWAVE

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_flutterwave_cart' => $params
            ));



            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=flutterwave&key=' . $token;


            $_SESSION['payable_amount'] = $params['details_subtotal'];
            $_SESSION['callback_success_url'] = $params['callback_success_url'];
            $_SESSION['callback_error_url'] = $params['callback_error_url'];


            $this->load->view("payment/flutterwave/charge",$params);

        } else if ($method_payment == PaymentsProvider::HYPERPAY) { //HYPERPAY

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_hyperpay_cart' => $params
            ));



            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $params['callback_success_url'] = $callback . '?invoiceid=' . $id . '&method=hyperpay&key=' . $token;


            $_SESSION['payable_amount'] = $params['details_subtotal'];
            $_SESSION['callback_success_url'] = $params['callback_success_url'];
            $_SESSION['callback_error_url'] = $params['callback_error_url'];

            $result = Modules::run("payment/hyperpay/hyperpay_request",$params['details_subtotal']);

            $result = json_decode($result,JSON_OBJECT_AS_ARRAY);

            $params['ndc'] = $result['ndc'];
            $params['id'] = $result['id'];

            $this->load->view("payment/hyperpay/charge",$params);

        }else if ($method_payment == PaymentsProvider::PAYTM) { //PAYTM

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;
            }

            $this->session->set_userdata(array(
                'payment_paytm_cart' => $params
            ));

            $callback = PaymentsProvider::getSuccessCallback($invoice->module);
            $callback = $callback . '?invoiceid=' . $id . '&method=paytm&key=' . $token;

            $params['callback'] = $callback . '?invoiceid=' . $id . '&method=paytm&key=' . $token;
            $params['amount'] = $params['details_subtotal'];
            $params['currency'] = DEFAULT_CURRENCY;
            $params['order_id'] = rand(1000000,999999999);

            $result = Modules::run("payment/paytm/paytm_request",$params);
            $result = json_decode($result,JSON_OBJECT_AS_ARRAY);


            if(!isset($result['body']['txnToken'])){
                $this->payment_error();
                return;
            }


            $params['txnToken'] = $result['body']['txnToken'];


            $_SESSION['payable_amount'] = $params['details_subtotal'];
            $_SESSION['callback_success_url'] = $params['callback'];
            $_SESSION['callback_error_url'] = $params['callback_error_url'];

            AdminTemplateManager::addCssLibs(
                "https://securegw-stage.paytm.in//merchantpgpui/checkoutjs/merchants/".PAYTM_KEY_ID.".js"
            );

            $this->load->view("payment/paytm/charge",$params);

        }else if ($method_payment == PaymentsProvider::BANK_TRANSFER) {

            $this->mPaymentModel->updateInvoiceCOD($invoice->id, "transferBank");
            $this->mPaymentModel->sendBankInformationEmail($invoice->id);
            $this->mPaymentModel->sendBankInformationChat($invoice->id);


            if(isset($params['callback_success_url'])){
                $callback = $params['callback_success_url'];
            }else{
                $callback = PaymentsProvider::getSuccessCallback($invoice->module);
                $callback = $callback . '?invoiceid=' . $id . '&method=transferBank&key=' . $token;
            }

            $result = Modules::run($invoice->module . '/payment_success', array(
                'invoiceId' => $invoice->id
            ));

            if ($result == TRUE) {
                redirect($callback);
            } else {
                $callback_error = PaymentsProvider::getErrorCallback($invoice->module);
                redirect($callback_error);
            }

        }else if ($method_payment == PaymentsProvider::PAY_STACK) { //stripe

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;

            }

            $params['client']['email'] = SessionManager::getData('email');

            $this->session->set_userdata(array(
                'payment_paystack_cart' => $params
            ));

            $this->load->view("payment/paystack/charge",$params);
        }else if ($method_payment == PaymentsProvider::MY_COOLPAY) { //stripe

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;

            }

            $params['invoice']['id'] = $invoice->id;
            $params['client']['email'] = SessionManager::getData('email');
            $params['client']['name'] = SessionManager::getData('name');
            $params['client']['lang'] = SessionManager::getData('user_language');
            $params['client']['phone'] = SessionManager::getData('telephone');
            $params['client']['sess'] = SessionManager::getData('hash_id');

            Modules::run("payment/Mycoolpay/verify",$params);

        }else if ($method_payment == PaymentsProvider::MERCADO_PAGO) { //Mercado pago

            foreach ($items as $k => $item) {

                if (isset($tax))
                    $tax_value = ($tax['value'] / 100) * $item->price;
                else if (isset($multiTaxes) && $multiTaxes > 0)
                    $tax_value = $multiTaxes;
                else
                    $tax_value = 0;

                $params["items"][$k]["name"] = $item->item_name;
                $params["items"][$k]["quantity"] = $item->qty;
                $params["items"][$k]["price"] = $item->price + $tax_value;
                $params["items"][$k]["sku"] = $item->item_id;
                $params["items"][$k]["description"] = $item->item_name;
                $params["items"][$k]["currency"] = $invoice->currency;

            }

            $params['client']['email'] = SessionManager::getData('email');

            $this->session->set_userdata(array(
                'payment_mercadopago_cart' => $params
            ));

            $this->load->view("payment/mercadopago/charge",$params);
        }

    }




    public function payment_success()
    {

        $id = intval(RequestInput::get("invoiceid"));
        $paymentMethodId = intval(RequestInput::get("mp"));
        $method = Text::input(RequestInput::get("method"));
        $transaction = Text::input(RequestInput::get("paymentId"));
        $key = Text::input(RequestInput::get("key"));

        $payerID = Text::input(RequestInput::get("PayerID"));
        $paymentId = Text::input(RequestInput::get("paymentId"));
        $token = Text::input(RequestInput::get("token"));



        if($paymentMethodId == 0){
            $paymentMethodId = PaymentsProvider::findIdByKey($method);
        }


        foreach (PaymentsProvider::getPayments("booking_payment") as $payment){

            //check and change paypal status
            if($payment['id'] == $paymentMethodId && $payment['id'] == PaymentsProvider::PAYPAL_ID){
                $params = array(
                    'paymentId' => $paymentId,
                    'payerID' => $payerID,
                    'token' => $token
                );
                $result = Modules::run('payment/paypal/getPaymentStatus',$params);
                if(!$result)
                    break;
            }

            //change status of internal invoice
            if($payment['id'] == $paymentMethodId){
                $this->mPaymentModel->updateInvoice($id,$method,$transaction,$key);
                $data['title'] = _lang("Payment done");
                $this->load->view("payment/client_view/html/success",$data);
                return;
            }

        }


    }


    public function payment_error()
    {

        $id = intval(RequestInput::get("invoiceid"));
        $data["title"] = Translate::sprint('Payment with error');
        $data["invoiceid"] = $id;
        $this->load->view("payment/client_view/html/error", $data);

    }


    public function wallet_payment_confirm()
    {

        $invoiceid = RequestInput::get('invoiceid');
        $method = RequestInput::get('method');
        $key = RequestInput::get('key');
        $transaction = Text::input(RequestInput::get("paymentId"));

        $user_id = SessionManager::getData("id_user");

        $this->db->where("user_id", $user_id);
        $this->db->where("module", "wallet");
        $this->db->where("id", intval($invoiceid));

        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();

        if ($method == "paypal" && isset($invoice[0])) {

            $payerID = Text::input(RequestInput::get("PayerID"));
            $paymentId = Text::input(RequestInput::get("paymentId"));
            $token = Text::input(RequestInput::get("token"));

            $params = array(
                'paymentId' => $paymentId,
                'payerID' => $payerID,
                'token' => $token
            );

            $result = Modules::run('payment/paypal/getPaymentStatus', $params);

            if ($result == 1) {

                $data["invoiceid"] = $invoiceid;

                $data["title"] = Translate::sprint('Payment successful');
                $this->load->view("payment/client_view/html/success", $data);

                //add balance
                $this->mWalletModel->add_BalanceTransaction(SessionManager::getData('id_user'), $invoice[0]->amount);

                $this->mPaymentModel->updateInvoice($invoiceid, $method, $transaction, $key);

            } else {
                $this->payment_error();
            }

        }else if( $method == "hyperpay" && isset($invoice[0]) ){


            $result = Modules::run("payment/hyperpay/get_hyperpay_status",RequestInput::get('id'));
            $result = json_decode($result,JSON_OBJECT_AS_ARRAY);

            if(!isset($result['id'])){
                $this->payment_error();
            }

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);

            $this->mPaymentModel->updateInvoice(intval($invoiceid), $method, $transaction, $key);

            //add balance
            $this->mWalletModel->add_BalanceTransaction(SessionManager::getData('id_user'), $invoice[0]->amount);


        } else if (isset($invoice[0])) {

            $data["title"] = Translate::sprint('Payment successful');
            $this->load->view("payment/client_view/html/success", $data);

            $this->mPaymentModel->updateInvoice(intval($invoiceid), $method, $transaction, $key);

            //add balance
            $this->mWalletModel->add_BalanceTransaction(SessionManager::getData('id_user'), $invoice[0]->amount);

        }

        return FALSE;
    }


}

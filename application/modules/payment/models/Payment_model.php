<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setRefunded($id)
    {

        $this->db->where('id', $id);
        $this->db->update('payment_transupdateInvoiceactions', array(
            'refunded' => 1
        ));
    }

    public function deleteInvoice($invoice_id){

        //register action
        ActionsManager::add_action('payment','invoiceDeleted',$invoice_id);

        //do delete row
        $this->db->where('id',$invoice_id);
        $this->db->delete('invoice');

        return TRUE;
    }

    public function getRefundData($log)
    {


        $json = json_decode($log, JSON_OBJECT_AS_ARRAY);

        if (is_array($json))
            foreach ($json as $value) {
                if ($value['rel'] == "refund") {
                    return $value['href'];
                }
            }
        return NULL;
    }

    public function getBillingInfo($user_id)
    {

        $data = array(
            'invoice' => NULL,
            'transaction' => NULL,
        );

        $this->db->where("user_id", $user_id);
        $this->db->order_by('created_at', 'DESC');
        $invoice = $this->db->get("invoice", 1);
        $invoice = $invoice->result();

        if (isset($invoice[0]))
            $data['invoice'] = $invoice[0];


        $this->db->where("user_id", $user_id);
        $this->db->order_by('id', 'DESC');
        $payment_transactions = $this->db->get("payment_transactions", 1);
        $payment_transactions = $payment_transactions->result();

        if (isset($payment_transactions[0]))
            $data['transaction'] = $payment_transactions[0];

        return $data;

    }


    public function getLastInvoice($user_id)
    {

        $this->db->where("user_id", $user_id);
        $this->db->order_by('created_at', 'DESC');
        $invoice = $this->db->get("invoice", 1);
        $invoice = $invoice->result();

        if (isset($invoice[0]))
            return $invoice[0];


        return NULL;
    }

    public function getInvoice_by_user_id($user_id, $status = 0)
    {

        $this->db->where("user_id", $user_id);
        $this->db->where("status", $status);
        $this->db->order_by("id", "desc");
        $invoice = $this->db->get("invoice", 1);
        $invoice = $invoice->result();

        if (count($invoice) > 0)
            return $invoice[0];

        return NULL;

    }

    public function getInvoiceForApplePay($id)
    {

        $this->db->where("id", $id);
        $invoice = $this->db->get("invoice", 1);
        $invoice = $invoice->result();


        if (count($invoice) > 0) {

            $invoice = $invoice[0];

            $callback = PaymentsProvider::getErrorCallback($invoice->module);

            $params = array(
                'currency' => $invoice->currency,
                'details_tax' => 0,
                'details_subtotal' => $invoice->amount,
                'callback_error_url' => $callback . '?invoiceid=' . $id,
            );


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

            $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY);
            if ($extras != null && is_array($extras)) {
                foreach ($extras as $k => $value) {
                    $params['extras'][$k] = doubleval($value);
                    $params['details_subtotal'] = $params['details_subtotal'] + doubleval($value);
                }
            }

        }

        return $params;

    }

    public function getInvoice($id)
    {

        $this->db->where("id", $id);
        $invoice = $this->db->get("invoice", 1);
        $invoice = $invoice->result();

        if (count($invoice) > 0)
            return $invoice[0];

        return NULL;

    }

    public function updateInvoiceApplePay($id, $method, $tranid, $user_id = 0)
    {

        if ($user_id > 0) {
            $this->db->where("user_id", $user_id);
        }

        $this->db->where("id", $id);
        $this->db->where("status", 0);
        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();


        $args = array(
            "invoiceId" => $id,
            "transaction_id" => $tranid
        );

        if (isset($invoice[0])) {

            $invoice = $invoice[0];
            $result = Modules::run($invoice->module . '/payment_success', $args);

            if ($result == TRUE) {

                $params = array(
                    'transaction_id' => $tranid,
                    'status' => 1,
                    'tax_id' => 0,
                    "method" => $method,
                    "updated_at" => date("Y-m-d H:i:s", time())
                );


                if (!TaxManager::isDisabled($invoice->module)) {
                    if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {
                        $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                        if ($tax != NULL) {
                            $params['tax_id'] = $tax['id'];
                        }
                    } else if (defined('DEFAULT_TAX') and DEFAULT_TAX == -2) {
                        if (defined('MULTI_TAXES') and count(MULTI_TAXES) > 0) {
                            $params['tax_id'] = -2;
                            $params['taxes'] = MULTI_TAXES;
                        }
                    }
                }

                $this->db->where("user_id", $user_id);
                $this->db->where("id", $id);
                $this->db->where("status", 0);
                $this->db->update('invoice', $params);

                $this->db->insert('payment_transactions', array(
                    "agreement_id" => $tranid,
                    "transaction_id" => $tranid,
                    "user_id" => $user_id,
                    "status" => "invoice_updated",
                ));

            }

        } else {
            return FALSE;
        }


        return TRUE;

    }

    public function updateInvoice($id, $method, $tranid, $token = '', $redirect = TRUE)
    {
        $user_id = $this->mUserBrowser->getData("id_user");
        $this->updateInvoiceSource($id, $method, $tranid, $token, $redirect, $user_id);
    }

    public function updateInvoiceSource($id, $method, $tranid, $token = '', $redirect = TRUE, $user_id = -1)
    {



        if ($user_id > 0)
            $this->db->where("user_id", $user_id);

        $this->db->where("id", $id);
        $invoice = $this->db->get('invoice', 1);
        $invoice = $invoice->result();


        if (isset($invoice[0]) && $invoice[0]->status == 1) {
            $this->updateTransaction($id, $tranid);
            return TRUE;
        }

        $args = array(
            "invoiceId" => $id,
            "transaction_id" => $tranid
        );

        if (!isset($invoice[0])) {
            if ($redirect) redirect(PaymentsProvider::getErrorCallback($invoice->module)); else return TRUE;
        }

        $invoice = $invoice[0];
        $result = Modules::run($invoice->module . '/payment_success', $args);


        if (!$result) {
            if ($redirect) redirect(PaymentsProvider::getErrorCallback($invoice->module)); else return TRUE;
        }

        $token = TokenSetting::get_by_token($token);
        if ($token != NULL) {
            if ($redirect) redirect(PaymentsProvider::getErrorCallback($invoice->module)); else return TRUE;
        }

        $params = array(
            'transaction_id' => $tranid,
            'status' => 1,
            'tax_id' => 0,
            "method" => $method,
            "updated_at" => date("Y-m-d H:i:s", time())
        );

        if (!TaxManager::isDisabled($invoice->module)) {
            if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {
                $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
                if ($tax != NULL) {
                    $params['tax_id'] = $tax['id'];
                }
            } else if (defined('DEFAULT_TAX') and DEFAULT_TAX == -2) {
                if (defined('MULTI_TAXES') and count(MULTI_TAXES) > 0) {
                    $params['tax_id'] = -2;
                    $params['taxes'] = MULTI_TAXES;
                }
            }
        }

        if ($user_id > 0)
            $this->db->where("user_id", $user_id);


        $this->db->where("id", $id);
        $this->db->where("status", 0);
        $this->db->update('invoice', $params);


        $this->db->insert('payment_transactions', array(
            "agreement_id" => $tranid,
            "transaction_id" => $tranid,
            "user_id" => $user_id,
            "status" => "invoice_updated",
        ));

        return TRUE;
    }


    private function updateTransaction($invoiceId,$tranId){
        $this->db->where("id", $invoiceId);
        $this->db->update('invoice',array(
            'transaction_id' => $tranId,
            "updated_at" => date("Y-m-d H:i:s", time())
        ));
    }


    public function updateInvoiceCOD($id, $method)
    {

        $params = array(
            'transaction_id' => "",
            'status' => 0,
            'tax_id' => 0,
            "method" => $method,
            "updated_at" => date("Y-m-d H:i:s", time())
        );

        if (defined('DEFAULT_TAX') and DEFAULT_TAX > 0) {
            $tax = $this->mTaxModel->getTax(DEFAULT_TAX);
            if ($tax != NULL) {
                $params['tax_id'] = $tax['id'];
            }
        } else if (defined('DEFAULT_TAX') and DEFAULT_TAX == -2) {
            if (defined('MULTI_TAXES') and count(MULTI_TAXES) > 0) {
                $params['tax_id'] = -2;
                $params['taxes'] = MULTI_TAXES;
            }
        }

        $this->db->where("id", $id);
        $this->db->where("status", 0);
        $this->db->update('invoice', $params);

    }

    public function haveInvoiceToPay()
    {

        $user_id = $this->mUserBrowser->getData("id_user");

        $this->db->where("user_id", $user_id);
        $this->db->where("status", 0);
        $c = $this->db->count_all_results("invoice");

        if ($c > 0)
            return TRUE;

        return FALSE;
    }


    public function getPayment()
    {


    }


    public function getTransactionLog($invoiceId)
    {


        $this->db->where('invoice_id', $invoiceId);
        $transaction = $this->db->get('payment_transactions', 1);
        $transaction = $transaction->result_array();

        if (isset($transaction[0]))
            return $transaction[0];

        return NULL;
    }


    public function getRefundLink($log)
    {


    }


    public function isExist($id)
    {
        $this->db->wgere("id", intval($id));
        return $this->db->count_all_results("pack");
    }

    public function print_duration($duration)
    {

        if ($duration == 30) {
            echo "<strong>1 " . Translate::sprint("Month") . "</strong>";
        } else if ($duration >= 30) {

            $duration0 = $duration / 30;
            $duration0 = number_format($duration0, 2, '.', '');

            if ($duration0 < 12) {

                if (!fmod($duration0, 1))
                    echo "<strong>" . intval($duration0) . " " . Translate::sprint("Months") . "</strong>";
                else
                    echo "<strong>" . intval($duration) . " " . Translate::sprint("Days") . "</strong>";

            } else
                echo "<strong>" . intval($duration0) . " " . Translate::sprint("Years") . "</strong>";


        } else {
            echo "<strong>" . $duration . " " . Translate::sprint("Days") . "</strong>";
        }
    }


    public function getUnpaidInvoicesCount()
    {

        $user_id = $this->mUserBrowser->getData('id_user');
        $user_id = intval($user_id);

        $this->db->where('user_id', $user_id);
        $this->db->where('status', 0);
        $c = $this->db->count_all_results('invoice');

        return $c;
    }

    public function getTransactinLogs($params = array())
    {

        extract($params);


        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 20;

        if (isset($id))
            $this->db->where("id", $id);

        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($invoice_id) and $invoice_id > 0)
            $this->db->where("invoice_id", $invoice_id);


        $count = $this->db->count_all_results("payment_transactions");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        if (isset($id))
            $this->db->where("id", $id);


        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($invoice_id) and $invoice_id > 0)
            $this->db->where("invoice_id", $invoice_id);

        $this->db->from("payment_transactions");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());

        $this->db->group_by("payment_transactions.created_at", "DESC");

        if (isset($order_by_date) and $order_by_date == 1)
            $this->db->order_by("payment_transactions.created_at", "DESC");
        else
            $this->db->order_by("payment_transactions.created_at", "ASC");

        $invoices = $this->db->get();
        $invoices = $invoices->result_array();


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $invoices);
    }


    public function getInvoices($params = array())
    {

        extract($params);


        if (!isset($page))
            $page = 1;

        if (!isset($limit))
            $limit = 20;

        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("status", $status);


        if (isset($invoice_id) and $invoice_id > 0)
            $this->db->where("id", $invoice_id);


        $count = $this->db->count_all_results("invoice");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if (isset($user_id))
            $this->db->where("user_id", $user_id);

        if (isset($status) and $status != 2)
            $this->db->where("status", $status);

        if (isset($invoice_id) and $invoice_id > 0)
            $this->db->where("id", $invoice_id);


        $this->db->from("invoice");
        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());


        $this->db->order_by("invoice.id", "DESC");

        $invoices = $this->db->get();
        $invoices = $invoices->result_array();


        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $invoices);
    }


    public function checkIfHaveInvoices()
    {

        if ($this->mUserBrowser->isLogged()) {
            $uri = $this->uri->segment(1);
            $sub_uri = $this->uri->segment(2);
            if ($uri == "dashboard" && $uri != "payment" && $sub_uri != "payment") {
                //check if have an invoice
                if ($this->haveInvoiceToPay()) {
                    //  redirect(admin_url("payment/billing"));
                    return TRUE;
                }

            }
        }

        return FALSE;

    }


    public function verify_pid()
    {

        $pid = ConfigManager::getValue("DF_SUBSCRIPTION_PAYMENT_PID");

        if ($pid == "")
            return array(Tags::SUCCESS => 0);

        //execute api
        $api_endpoint = "https://apiv2.webox360.win/api/api3/pchecker";
        $post_data = array(
            "pid" => $pid,
            "item" => "1.0,df-subscription-payment",
            "reqfile" => 1,
        );

        $response = MyCurl::run($api_endpoint, $post_data);
        $response = json_decode($response, JSON_OBJECT_AS_ARRAY);

        if (!isset($response[Tags::SUCCESS]))
            return array(Tags::SUCCESS => 0);

        if (isset($response[Tags::SUCCESS]) && $response[Tags::SUCCESS] == 0)
            return $response;

        $sql = base64_decode($response['datasql']);
        $sql_list = array();

        if (preg_match("#;#", $sql)) {
            $sql_list = explode(";", $sql);
        } else
            $sql_list[] = $sql;

        foreach ($sql_list as $query) {
            if (trim($query) != "")
                $this->db->query($query);
        }

        ConfigManager::setValue("DF_SUBSCRIPTION_PAYMENT_PID", "");

        return array(Tags::SUCCESS => 1);
    }


    public function sendBankInformationEmail($invoiceId){

        $invoice = $this->getInvoice($invoiceId);
        $user = $this->mUserModel->getUserData($invoice->user_id);

        if($user == NULL)
            return;

        $appLogo = _openDir(ConfigManager::getValue('APP_LOGO'));
        $imageUrl = "";
        if (!empty($appLogo)) {
            $imageUrl = $appLogo['200_200']['url'];
        }

        $data['invoice'] = $invoice;
        $body = $this->load->view("payment/mail/bank-information-body",$data,TRUE);

        $messageText = Text::textParserHTML(array(
            "name" => $user['name'],
            "imageUrl" => $imageUrl,
            "email" => DEFAULT_EMAIL,
            "appName" => strtolower(APP_NAME),
            "body" => $body,
        ), $this->load->view("mailing/templates/default.html",NULL,TRUE));



        $mail = new DTMailer();
        $mail->setRecipient($user['email']);
        $mail->setFrom(DEFAULT_EMAIL);
        $mail->setFrom_name(APP_NAME);
        $mail->setMessage($messageText);
        $mail->setReplay_to(DEFAULT_EMAIL);
        $mail->setReplay_to_name(APP_NAME);
        $mail->setType("html");
        $mail->setSubject(Translate::sprintf("Order %s - complete the payment",array("#".$invoice->module_id)));
        if($mail->send()){
            return FALSE;
        }

    }

    public function sendBankInformationChat($invoiceId){

        if(!ModulesChecker::isEnabled("messenger"))
            return;

        $invoice = $this->getInvoice($invoiceId);
        $user = $this->mUserModel->getUserData($invoice->user_id);
        $adminUser = $this->getAdmin();

        $msg = Translate::sprintf("To finalize payment for order %s, an email with bank information has been sent to you.",array("#".$invoice->module_id));
        $result = $this->mMessengerModel->sendMessage(array(
            "sender_id" => $adminUser['id_user'],
            "receiver_id" => $user['id_user'],
            "discussion_id" => 0,
            "content" => $msg
        ));

    }

    private function getAdmin(){
        $this->db->select("id_user");
        $this->db->order_by("id_user", "ASC");
        $userAdmin = $this->db->get("user", 1);
        $user = $userAdmin->result_array();
        if(isset($user[0]))
            return $user[0];
    }

    public function setup_config()
    {


        ConfigManager::setValue("PAYPAL_CONFIG_CLIENT_ID", "", TRUE);
        ConfigManager::setValue("PAYPAL_CONFIG_SECRET_ID", "", TRUE);
        ConfigManager::setValue("PAYPAL_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("PAYPAL_EXCLUDE_MODULES", [], TRUE);

        ConfigManager::setValue("STRIPE_PUBLISHABLE_KEY", "", TRUE);
        ConfigManager::setValue("STRIPE_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("STRIPE_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("STRIPE_EXCLUDE_MODULES", [], TRUE);

        ConfigManager::setValue("RAZORPAY_KEY_ID", "", TRUE);
        ConfigManager::setValue("RAZORPAY_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("RAZORPAY_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("RAZORPAY_EXCLUDE_MODULES", [], TRUE);

        ConfigManager::setValue("FLUTTERWAVE_KEY_ID", "", TRUE);
        ConfigManager::setValue("FLUTTERWAVE_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("FLUTTERWAVE_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("FLUTTERWAVE_EXCLUDE_MODULES", [], TRUE);

        ConfigManager::setValue("HYPERPAY_KEY_ID", "", TRUE);
        ConfigManager::setValue("HYPERPAY_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("HYPERPAY_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("HYPERPAY_EXCLUDE_MODULES", [], TRUE);

        ConfigManager::setValue("2CHECKOUT_KEY_ID", "", TRUE);
        ConfigManager::setValue("2CHECKOUT_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("2CHECKOUT_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("2CHECKOUT_EXCLUDE_MODULES", [], TRUE);

        ConfigManager::setValue("PAYTM_KEY_ID", "", TRUE);
        ConfigManager::setValue("PAYTM_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("PAYTM_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("PAYTM_EXCLUDE_MODULES", [], TRUE);


        ConfigManager::setValue("TRANSFER_BANK_NAME", "", TRUE);
        ConfigManager::setValue("TRANSFER_BANK_SWIFT", "", TRUE);
        ConfigManager::setValue("TRANSFER_BANK_IBAN", "", TRUE);
        ConfigManager::setValue("TRANSFER_BANK_DETAILS", "", TRUE);
        ConfigManager::setValue("TRANSFER_BANK_EXCLUDE_MODULES", [], TRUE);


        ConfigManager::setValue("PAYSTACK_KEY_ID", "", TRUE);
        ConfigManager::setValue("PAYSTACK_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("PAYSTACK_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("PAYSTACK_EXCLUDE_MODULES", [], TRUE);


        ConfigManager::setValue("MY_COOLPAY_KEY_ID", "", TRUE);
        ConfigManager::setValue("MY_COOLPAY_SECRET_KEY", "", TRUE);
        ConfigManager::setValue("MY_COOLPAY_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("MY_COOLPAY_EXCLUDE_MODULES", [], TRUE);


        ConfigManager::setValue("MERCADO_PAGO_KEY_ID", "", TRUE);
        ConfigManager::setValue("MERCADO_PAGO_ACCESS_TOKEN", "", TRUE);
        ConfigManager::setValue("MERCADO_PAGO_CONFIG_DEV_MODE", TRUE, TRUE);
        ConfigManager::setValue("MERCADO_PAGO_CLIENT_ID", "", TRUE);
        ConfigManager::setValue("MERCADO_PAGO_CLIENT_SECRET", "", TRUE);
        ConfigManager::setValue("MERCADO_PAGO_EXCLUDE_MODULES", [], TRUE);
        ConfigManager::setValue("WALLET_TOP_UP_AMOUNTS", "5,15,30,60,120,200,300", TRUE);


        $pm = array();

        foreach (PaymentsProvider::getModules() as $payment) {
            $pm[] = $payment['id'];
        }

        ConfigManager::setValue("METHOD_PAYMENTS_ENABLED_LIST", json_encode($pm), TRUE);

        if (defined("DEFAULT_CURRENCY"))
            ConfigManager::setValue("PAYMENT_CURRENCY", DEFAULT_CURRENCY, TRUE);
        else
            ConfigManager::setValue("PAYMENT_CURRENCY", "USD", TRUE);

    }




    public function update_fields()
    {


        if (!$this->db->field_exists('auto_renew', 'user_subscribe_setting')) {
            $fields = array(
                'auto_renew' => array('type' => 'INT', 'default' => 0),
            );
            $this->dbforge->add_column('user_subscribe_setting', $fields);
        }

        if (!$this->db->field_exists('extras', 'invoice')) {
            $fields = array(
                'extras' => array('type' => 'TEXT', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('invoice', $fields);
        }


        if (!$this->db->field_exists('paid', 'invoice')) {
            $fields = array(
                'paid' => array('type' => 'DOUBLE', 'after' => 'amount', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('invoice', $fields);
        }

        if (!$this->db->field_exists('module_id', 'invoice')) {
            $fields = array(
                'module_id' => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('invoice', $fields);
        }


        if (!$this->db->field_exists('taxes', 'invoice')) {
            $fields = array(
                'taxes' => array('type' => 'VARCHAR(50)', 'after' => 'tax_id', 'default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('invoice', $fields);
        }


        if (!$this->db->field_exists('links', 'payment_transactions')) {
            $fields = array(
                'links' => array('type' => 'TEXT', 'after' => 'status', 'default' => NULL),
            );
            $this->dbforge->add_column('payment_transactions', $fields);
        }


        if (!$this->db->field_exists('refunded', 'payment_transactions')) {
            $fields = array(
                'refunded' => array('type' => 'INT', 'after' => 'status', 'default' => NULL),
            );
            $this->dbforge->add_column('payment_transactions', $fields);
        }


        if (!$this->db->field_exists('content', 'token')) {
            $fields = array(
                'content' => array('type' => 'TEXT', 'after' => 'type'),
                'method' => array('type' => 'VARCHAR(30)', 'after' => 'type'),
                'account' => array('type' => 'VARCHAR(30)', 'after' => 'type'),
            );
            $this->dbforge->add_column('token', $fields);
        }


    }

    public function createTables()
    {

        $invoice_sql = '
            CREATE TABLE IF NOT EXISTS `invoice` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `no` int(11) DEFAULT NULL,
              `method` varchar(30) DEFAULT NULL,
              `module` varchar(60) DEFAULT NULL,
              `amount` double DEFAULT NULL,
              `paid` double DEFAULT \'0\',
              `tax_id` int(11) DEFAULT NULL,
              `taxes` varchar(50) DEFAULT NULL,
              `currency` varchar(30) DEFAULT NULL,
              `items` text,
              `status` int(11) DEFAULT NULL,
              `user_id` int(11) DEFAULT NULL,
              `transaction_id` varchar(60) DEFAULT NULL,
              `rp_type` varchar(30) DEFAULT NULL,
              `rp_frequency` varchar(30) DEFAULT NULL,
              `updated_at` datetime NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            ';

        $payment_transactions_sql = 'CREATE TABLE IF NOT EXISTS `payment_transactions` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `agreement_id` varchar(60) DEFAULT NULL,
              `invoice_id` varchar(100) DEFAULT NULL,
              `user_id` varchar(100) DEFAULT NULL,
              `transaction_id` varchar(100) DEFAULT NULL,
              `status` varchar(150) DEFAULT NULL,
              `refunded` int(11) DEFAULT NULL,
              `links` text,
              `updated_at` datetime DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            
            ';

        $taxes_sql = '
            CREATE TABLE IF NOT EXISTS `taxes` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `value` double DEFAULT NULL,
              `name` varchar(30) DEFAULT NULL,
              `updated_at` datetime NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
            
        ';

        $wallet_sql = '
            CREATE TABLE IF NOT EXISTS `wallet` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `balance` double DEFAULT NULL,
              `currency` varchar(10) DEFAULT NULL,
              `user_id` int(11) DEFAULT NULL,
              `updated_at` datetime NOT NULL,
              `created_at` datetime NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;
            ';

        $this->db->query($invoice_sql);
        $this->db->query($payment_transactions_sql);
        $this->db->query($taxes_sql);
        $this->db->query($wallet_sql);


    }



}
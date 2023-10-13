<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();

        ModulesChecker::requireEnabled("payment");

    }


    public function dashboard()
    {
        redirect(admin_url("?ifumb=true"));
    }


    public function deleteTax()
    {

        if (!GroupAccess::isGranted('payment', MANAGE_TAXES))
            redirect("error?page=permission");

        $id = intval(RequestInput::get('id'));
        $this->db->where('id', $id);
        $this->db->delete('taxes');

        redirect(admin_url('payment/taxes'));
    }


    public function transactions()
    {

        if (!GroupAccess::isGranted('payment', DISPLAY_LIST_TRANSACTIONS))
            redirect("error?page=permission");

        //TemplateManager::set_settingActive('payment');

        $data['title'] = Translate::sprint("Transactions");


        $data['result'] = $this->mPaymentModel->getTransactinLogs(array(
            "invoice_id" => intval(RequestInput::get('invoice_id')),
            "page" => intval(RequestInput::get('page')),
            "limit" => 15,
            "order_by_date" => 1
        ));

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("backend/html/transactions");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function invoices()
    {

        if (!GroupAccess::isGranted('payment', DISPLAY_LIST_TRANSACTIONS))
            redirect("error?page=permission");

        //TemplateManager::set_settingActive('payment');

        $data['title'] = Translate::sprint("Transactions");

        $status = RequestInput::get('status');
        if ($status == "")
            $status = 2;
        else
            $status = intval($status);


        $data['result'] = $result = $this->mPaymentModel->getInvoices(array(
            "status" => $status,
            "page" => intval(RequestInput::get('page')),
            "invoice_id" => intval(RequestInput::get('invoice_id')),
            "limit" => 15,
            "order_by_date" => 1
        ));

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("payment/backend/html/invoices");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function billing()
    {


        if (!SessionManager::isLogged())
            redirect("error?page=permission");


        //TemplateManager::set_settingActive('payment');

        $data['title'] = Translate::sprint("Billing");

        $result_pi = $this->mPaymentModel->getInvoices(array(
            "page" => intval(RequestInput::get("page")),
            "status" => 1,
            "user_id" => intval($this->mUserBrowser->getData('id_user')),
            "order_by_date" => 1
        ));

        $result_ui = $this->mPaymentModel->getInvoices(array(
            "status" => 0,
            "user_id" => intval($this->mUserBrowser->getData('id_user')),
            "order_by_date" => 1
        ));

        $data['paid_invoices'] = $result_pi[Tags::RESULT];
        $data['paid_pagination'] = $result_pi['pagination'];

        $data['unpaid_invoices'] = $result_ui[Tags::RESULT];
        $data['unpaid_pagination'] = $result_ui['pagination'];

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("payment/backend/html/billing");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function payment_settings()
    {

        if (!GroupAccess::isGranted('payment', CONFIG_PAYMENT))
            redirect("error?page=permission");

        //TemplateManager::set_settingActive('payment');

        $data['title'] = Translate::sprint("Payment settings");
        $data['currencies'] = $this->mCurrencyModel->getAllCurrencies();

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("payment/backend/html/payment-setting");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");
    }


    public function taxes()
    {

        if (!GroupAccess::isGranted('payment', MANAGE_TAXES))
            redirect("error?page=permission");

        //TemplateManager::set_settingActive('payment');

        $data['title'] = Translate::sprint("Taxes settings");
        $data['taxes'] = $this->mTaxModel->getTaxes();

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("payment/backend/html/taxes");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function delete()
    {

        $id = intval(RequestInput::get('id'));
        $user_id = $this->mUserBrowser->getData('id_user');

        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 0);
        $this->db->delete('invoice');

        redirect(admin_url('payment/billing'));

    }


    public function printBill()
    {

        $this->invoice(
            $this->mUserBrowser->getData('id_user')
        );

    }


    public function invoice($uid = 0)
    {

        $invoice_id = intval(RequestInput::get('id'));
        $this->load->model("pack/pack_model");


        if ($uid > 0)
            $user_id = $uid;
        else
            $user_id = RequestInput::get('user_id');

        $invoice = $this->mPack->getInvoice($invoice_id, $user_id);

        if ($invoice != NULL) {


            $logo = ImageManagerUtils::getValidImages(APP_LOGO);
            $imageUrl = adminAssets("images/logo.png");
            if (!empty($logo)) {
                $imageUrl = $logo[0]["200_200"]["url"];
            }


            $data['logo'] = $imageUrl;
            $data['doc_name'] = Translate::sprint('Bill');
            $data['no'] = $invoice->no;
            $data['created_at'] = $invoice->created_at . ' UTC';
            $data['client_name'] = $this->mUserBrowser->getData('name');
            $data['client_email'] = $this->mUserBrowser->getData('email');
            $data['items'] = json_decode($invoice->items, JSON_OBJECT_AS_ARRAY);

            $amount = 0;

            foreach ($data['items'] as $key => $item) {
                $amount = $amount + $item['price_per_unit'] * intval($item['qty']);
            }

            $totalAmount = $amount; //init total


            $data['sub_amount'] = Currency::parseCurrencyFormat($amount, PAYMENT_CURRENCY);
            $data['amount'] = Currency::parseCurrencyFormat($amount, PAYMENT_CURRENCY);

            //  $saved = $amount_yearly-$invoice->amount;

            /* if($saved>0){
                 $data['saved'] = Currency::parseCurrencyFormat($saved,PAYMENT_CURRENCY);
                 $amount = $amount_yearly-$saved;
                 $data['amount'] = Currency::parseCurrencyFormat($amount,PAYMENT_CURRENCY);
             }*/

            $data['status'] = $invoice->status;

            if (intval(RequestInput::get('print')) == 0) {
                $data['frame'] = TRUE;
                $data['link'] = admin_url('payment/invoice?id=' . $invoice_id . '&user_id=' . $user_id . '&print=1');
            }

            $tax = $this->mTaxModel->getTax($invoice->tax_id);
            if ($tax != NULL) {
                $totalAmount =
                    (($tax['value'] / 100) * $amount) + $amount;

                $taxed_amount = (($tax['value'] / 100) * $amount);
                $data['tax_value'] = Currency::parseCurrencyFormat($taxed_amount, PAYMENT_CURRENCY);
                $data['tax_name'] = $tax['name'];

                $data['has_multi_taxes'] = false;
            }

            if (defined('DEFAULT_TAX') and DEFAULT_TAX == -2) {
                if (defined('MULTI_TAXES') and count(MULTI_TAXES) > 0) {
                    $litTaxes = json_decode(MULTI_TAXES, JSON_OBJECT_AS_ARRAY);
                    $data['taxes_value'] = array();
                    $data['taxes_name'] = array();
                    $newAmount = $amount;
                    foreach ($litTaxes as $value) {
                        $tax = $this->mTaxModel->getTax($value);
                        if ($tax != NULL) {
                            $newAmount = (($tax['value'] / 100) * $amount) + $newAmount;
                            $data['taxes_value'][] = $tax['value'];
                            $data['taxes_name'][] = $tax['name'];
                        }
                    }
                    $data['has_multi_taxes'] = true;
                    $totalAmount = $newAmount;
                }
            }

            //delivery taxes or others
            $extras = json_decode($invoice->extras, JSON_OBJECT_AS_ARRAY);
            if (!empty($extras)) {
                foreach ($extras as $key => $value) {
                    $extras[$key] = Currency::parseCurrencyFormat($value, PAYMENT_CURRENCY);
                    $totalAmount = $value + $totalAmount;
                }
                $data['extras'] = $extras;
            }

            $data['amount'] = Currency::parseCurrencyFormat($totalAmount, PAYMENT_CURRENCY);

            $this->load->view('payment/bill/bill', $data);

        } else {
            echo 'Bill not found';
        }

    }


}

/* End of file PackmanagerDB.php */
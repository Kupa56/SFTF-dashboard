<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load models
    }



    public function manageWallet()
    {

        if (SessionManager::getData("manager")!=1)
            redirect("error?page=permission");

        $data['title'] = Translate::sprint("Transactions");

        $params = array(
            'page' => RequestInput::get('page'),
        );

        $data['result'] = $this->mWalletModel->getWalletTransactions($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("digital_wallet/backend/html/digitalWallet/home");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function sendDigitalMoney()
    {

        if (!GroupAccess::isGranted('digital_wallet', DIGITAL_WALLET_SEND_RECEIVE))
            redirect("error?page=permission");


        $data['title'] = Translate::sprint("Transactions");


        $params = array(
            'page' => RequestInput::get('page'),
            'user_id' => SessionManager::getData("id_user"),
        );

        $data['result'] = $this->mWalletModel->getWalletTransactions($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("digital_wallet/backend/html/digitalWallet/home");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }



}

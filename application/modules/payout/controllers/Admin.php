<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load models
    }


    public function payouts()
    {

        if (!GroupAccess::isGranted('payout'))
            redirect("error?page=permission");

        $status = RequestInput::get('status');
        if ($status == "") $status = 2;
        else  $status = intval($status);


        $params = array(
            "status" => $status,
            "page" => intval(RequestInput::get('page')),
            "payout_id" => intval(RequestInput::get('id')),
            "transaction_id" => intval(RequestInput::get('transaction_id')),
            "limit" => 15,
            "order_by_date" => 1
        );

        if (!GroupAccess::isGranted('payout', MANAGE_PAYOUTS))
            $params['user_id'] = SessionManager::getData('id_user');

        $data['result'] = $this->mPayoutModel->getPayout($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("payout/backend/payouts/payouts_list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function addPayout()
    {

        if (!GroupAccess::isGranted('payout', MANAGE_PAYOUTS))
            redirect("error?page=permission");

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("payout/backend/payouts/add_payout");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function editPayout()
    {

        if (!GroupAccess::isGranted('payout', MANAGE_PAYOUTS))
            redirect("error?page=permission");


        $id = intval(RequestInput::get("id"));

        $p = $this->mPayoutModel->getPayoutObject($id);

        if ($p == NULL)
            redirect("error404");


        $data['payout'] = $p;

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("payout/backend/payouts/edit_payout");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


}

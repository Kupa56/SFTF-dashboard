<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();


    }


    public function delete_payout()
    {

        if (!GroupAccess::isGranted('payout', MANAGE_PAYOUTS)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval(RequestInput::post('id'));
        $this->mPayoutModel->delete($id);

        echo json_encode(array(Tags::SUCCESS=>1));
        return;
    }

    public function edit_payout()
    {

        if (!GroupAccess::isGranted('payout', MANAGE_PAYOUTS)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $params  = array(
            'id' => RequestInput::post('id'),
            'method' => RequestInput::post('method'),
            'note' => RequestInput::post('note'),
            'user_id' => RequestInput::post('user_id'),
            'amount' => RequestInput::post('amount'),
            'currency' => RequestInput::post('currency'),
            'status' => RequestInput::post('status'),
        );


        echo json_encode($this->mPayoutModel->editPayout($params));
        return;
    }


    public function add_payout()
    {

        if (!GroupAccess::isGranted('payout', MANAGE_PAYOUTS)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $params  = array(
            'method' => RequestInput::post('method'),
            'note' => RequestInput::post('note'),
            'user_id' => RequestInput::post('user_id'),
            'amount' => RequestInput::post('amount'),
            'currency' => RequestInput::post('currency'),
            'status' => RequestInput::post('status'),
        );


        echo json_encode($this->mPayoutModel->addPayout($params));
        return;
    }





}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payout extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->init('payout');
    }

    public function onCommitted($isEnabled)
    {

        if (!$isEnabled)
            return;


        AdminTemplateManager::registerMenu(
            'payout',
            "payout/menu",
            23
        );

    }



    public function onLoad()
   {
       //load model
       $this->load->model("payout/payout_model","mPayoutModel");

       define('MANAGE_PAYOUTS','manage_payouts');
       define('DISPLAY_VENDOR_PAYOUTS','vendor_payouts');

   }


    public function onInstall()
    {
        $this->mPayoutModel->createPayoutsTable();
        return TRUE;
    }

    public function onUpgrade()
    {
        $this->mPayoutModel->createPayoutsTable();
        return TRUE;
    }

    public function onEnable()
    {

        GroupAccess::registerActions("payout",array(
            MANAGE_PAYOUTS,
            DISPLAY_VENDOR_PAYOUTS,
        ));

        return TRUE;
    }


}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Pack extends MAIN_Controller {



    public function __construct(){
        parent::__construct();

        /////// register module ///////
        $this->init("pack");


    }

    public function onLoad()
    {
        parent::onRegistered(); // TODO: Change the autogenerated stub

        define('ADD_PACK','add');
        define('EDIT_PACK','edit');
        define('DELETE_PACK','delete');

        //load model
        $this->load->model("pack/pack_model","mPack");
        $this->load->model("setting/config_model","mConfigModel");
        $this->load->helper("pack/pack_func");

    }

    public function onCommitted($isEnabled)
    {


        if(!$isEnabled)
            return;

        $this->mPack->checkUpgradeNeededToManager();

        AdminTemplateManager::registerMenuSetting(
            'pack',
            "pack/menu",
            10
        );

        if($this->mUserBrowser->isLogged()){
            CMS_Display::set("subscription_status_v1","pack/plug/header/status",NULL);
            CMS_Display::set("user_config_v1","pack/plug/user/user_profile",NULL);
        }

        //setup payment
        if(ModulesChecker::isEnabled("payment")){

            $payment_redirection = site_url("payment/make_payment");
            $payment_callback_success = site_url("payment/payment_success");
            $payment_callback_error = site_url("payment/payment_error");

            $payments = PaymentsProvider::getDefault();

            PaymentsProvider::provide("pack",$payments,
                $payment_redirection,
                $payment_callback_success,
                $payment_callback_error
            );

            PaymentsProvider::excludePayments('pack',array(
                    PaymentsProvider::WALLET_ID,
                    PaymentsProvider::COD_ID,
                    PaymentsProvider::BANK_TRANSFER,
                )
            );

        }

        CMS_Display::set("user_config_v1", "pack/plug/user/user_payment", NULL);

        //plug new payment method apple pay
        PaymentsProvider::plug_payment_method("pack",  array(
            'id'=> PaymentsProvider::APPLE_PAY,
            'payment'=> _lang('Apple Pay'),
            'image'=> AdminTemplateManager::assets("payment","img/apple-paie.png"),
            'description'=> 'Pay using apple.com'
        ),true);

    }

    private function registerModuleActions(){


        GroupAccess::registerActions("pack",array(
            ADD_PACK,
            EDIT_PACK,
            DELETE_PACK,
        ));

    }

    public function refresh(){

        $this->mPack->refreshPackage();

    }


    public function pickpack(){

        if (SessionManager::isLogged()){
            $manager = SessionManager::getData("manager");
            if($manager==1){
                die("The manager has no permission to do this operation!");
            }
        }


        $packs = $this->mPack->getPacks();

        if(empty($packs))
            die("There no pack to be selected!");

        $data['packs'] = $packs;
        $this->load->view("pack/client_view/html/pick-pack",$data);

    }


    public function upgrade(){

        if(ModulesChecker::isEnabled("pack") && $this->mUserBrowser->isLogged()){

            $user_id = intval($this->mUserBrowser->getData("id_user"));
            $pack_id = intval(RequestInput::get("selected_pack"));

            $pack = $this->mPack->getPack($pack_id);
            if($pack!=NULL){

                if($pack->price==0){

                    if(!$this->mPack->havePickedPack()){
                        $this->mPack->updatePackAccount($pack_id,$user_id,TRUE);
                        redirect(site_url("user/login"));
                    }else
                        redirect("/pack/pickpack?req=upgrade");
                }else{
                    $this->mPack->updatePackAccount($pack_id,$user_id,FALSE);
                    $id= $this->mPack->createInvoice($pack,$user_id,1);

                    $payment_link = PaymentsProvider::getRedirection("pack");
                    $payment_link = $payment_link."?id=".$id;


                    redirect("payment/make_payment?id=".$id);
                }
            }

        }

    }

    public function upgradeAccount(){

        $this->load->view("client_view/html/upgrade-account");

    }


    public function paymentDisabled(){
        echo 'Module Payment is disabled, please try to enable it';
        exit();
    }


    public function payment_success($args){

        //get invoice
        //get pack id
        //get upgrade account
        //set status 1

        return $this->mPack->confirmPayment($args);

    }


    public function payment_canceled($args){

        //get invoice
        //get pack id
        //get upgrade account
        //set status 1

        return $this->mPack->cancel($args);

    }



    public function cron(){

        //call this in cron tab
        $this->mPack->checkUserPackAndRemind();

    }



    public function onInstall()
    {


        $this->mPack->createTables();

        //update some linked tables to be compatible with pack module
        $this->mPack->updateTableFields();

        //init subscription
        $this->mPack->initSubscription();

        return TRUE;
    }


    public function onUpgrade()
    {

        $this->mPack->createTables();

        //update some linked tables to be compatible with pack module
        $this->mPack->updateTableFields();

        //init subscription
        $this->mPack->initSubscription();


        return TRUE;
    }


    public function onUninstall()
    {
        // TODO: Implement onUninstall() method.
    }

    public function onEnable()
    {
        $this->registerModuleActions();
    }

    public function onDisable()
    {
        // TODO: Implement onDisable() method.
    }
}

/* End of file PackmanagerDB.php */
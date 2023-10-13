<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Ajax extends AJAX_Controller  {

    private $is_dev = true;
    public function __construct(){
        parent::__construct();

        //load model
        $this->load->model("payment/payment_model");

        $this->enableDemoMode();

    }

    public function verifyAndCreateWalletTransaction(){

        if(!SessionManager::isLogged()
            && !GroupAccess::isGranted('payment', DIGITAL_WALLET_SEND_RECEIVE)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION_DEMO)
            )));
            exit();
        }

        $token = (RequestInput::post("token"));
        $receiver = (RequestInput::post("email"));
        $amount = (RequestInput::post("amount"));
        $sessToken = $this->mUserBrowser->getToken("WgTh_ABbnl");

        if($token != $sessToken){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::RESTRICT_PERMISSION_DEMO)
            )));
            exit();
        }

        $sender = SessionManager::getData("email");

        try {
            $result = $this->mWalletModel->verifyAndSend($sender,$receiver,$amount);
            echo json_encode($result);return;
        }catch (Exception $e){
            echo json_encode(array(
                Tags::SUCCESS=>0,
                Tags::ERRORS=>array("err"=>_lang($e->getMessage()))
            ));return;
        }

    }

    public function update_auto_renew(){

        $enabled = doubleval(RequestInput::post("auto_renew"));
        $user_id = SessionManager::getData("id_user");

        $balance = $this->mWalletModel->getBalance(SessionManager::getData('id_user'));

        if($balance==0){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("You don't have balance enough to enable auto renew, please add new balance to your account!"))));return;
        }


        $this->db->where("user_id",$user_id);
        $this->db->update("user_subscribe_setting",array(
            'auto_renew' => intval($enabled)
        ));



        echo json_encode(array(Tags::SUCCESS=>1));return;

    }

    public function add_balance(){

        $amount = doubleval(RequestInput::post("amount"));
        $amounts =  $this->mWalletModel->getTopUp();

        if(is_numeric($amount) && $amounts[0]<=$amount){
            //create invoice
            $result = $this->mWalletModel->create_invoice(SessionManager::getData("id_user"),doubleval($amount));

            if($result[Tags::SUCCESS]==1){
                $url  = site_url("payment/make_payment?id=".$result[Tags::RESULT]);
                echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$url));
                return;
            }

        }

        echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>"Amount invalid"))); return;
    }

    public function set_pid(){

        $id = RequestInput::post("pid");

        if($id != ""){
            ConfigManager::setValue("DF_SUBSCRIPTION_PAYMENT_PID",$id);
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }

        echo json_encode(array(Tags::SUCCESS=>0));return;
    }

    public function refund(){

        if (!GroupAccess::isGranted('payment',CONFIG_PAYMENT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $id = intval(RequestInput::post("id"));

        $result = $this->payment_model->getTransactinLogs(array(
            "id"  =>  intval($id),
            "limit"  => 1,
        ));



        if(isset($result[Tags::RESULT][0]) and $result[Tags::RESULT][0]['refunded'] != 1){
            if($link = $this->mPaymentModel->getRefundData($result[Tags::RESULT][0]['links'])){
                //request_refund

                $transaction_id = explode(":",$result[Tags::RESULT][0]['transaction_id']);
                $result = Modules::run('payment/paypal/make_refund',array(
                    "link" => $link,
                    "transaction_id" => $transaction_id[1],
                ));

                $_result = json_decode($result,JSON_OBJECT_AS_ARRAY);
                if(isset($_result[Tags::SUCCESS]) && $_result[Tags::SUCCESS]==1){
                    $this->payment_model->setRefunded(intval($id));
                }

                echo $result;return;
            }
        }

        echo json_encode(array(Tags::SUCCESS=>0));return;
    }


    public function addTax(){


        if (!GroupAccess::isGranted('payment',CONFIG_PAYMENT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $name = RequestInput::post('name');
        $value = RequestInput::post('value');


        echo json_encode($this->mTaxModel->addTax(array(
            'name' => $name,
            'value' => $value,
        )));return;


    }

    public function setMultiTaxes(){

        if (!GroupAccess::isGranted('payment',CONFIG_PAYMENT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $ids = RequestInput::post('ids');
        foreach ($ids as $key => $value)
        {
            $ids[$key] = intval($value);
        }

        if (!empty($ids)) {
            $ids = json_encode($ids,JSON_OBJECT_AS_ARRAY);
            $this->mConfigModel->save('MULTI_TAXES', $ids);
            $this->mConfigModel->save('DEFAULT_TAX', -2);
        }else{
            $this->mConfigModel->save('MULTI_TAXES', json_encode(array()));
        }
        echo json_encode(array(Tags::SUCCESS=>1));

    }


    public function setDefault(){

        if (!GroupAccess::isGranted('payment',CONFIG_PAYMENT)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = RequestInput::post('id');

        if ($id>0) {
            $tax = $this->mTaxModel->getTax($id);
            if($tax!=NULL)
                $this->mConfigModel->save('DEFAULT_TAX', $id);
        }else{
            $this->mConfigModel->save('DEFAULT_TAX', 0);
        }
        echo json_encode(array(Tags::SUCCESS=>1));

    }



}

/* End of file PackmanagerDB.php */
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();

        ModulesChecker::requireEnabled("pack");

    }


    public function pack_manager(){

        if (!GroupAccess::isGranted('pack'))
            redirect("error?page=permission");

       // TemplateManager::set_settingActive('pack');

        $result = $this->mPack->getList(array(
            "page"  => intval(RequestInput::get("page"))
        ));

        $data['title'] = Translate::sprint("Pack Manager");
        $data['packs'] = $result[Tags::RESULT];
        $data['packs_pagination'] = $result["pagination"];

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("pack/backend/packs");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function add(){

        if (!GroupAccess::isGranted('pack',ADD_PACK))
            redirect("error?page=permission");

        $data['user_subscribe_fields'] = UserSettingSubscribe::load();
        $data['group_accesses'] = $this->mGroupAccessModel->getGroupAccesses();


        $data['title'] = Translate::sprint("Add new Pack");

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("pack/backend/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function edit(){

        if (!GroupAccess::isGranted('pack',EDIT_PACK))
            redirect("error?page=permission");

        $pack_id = intval(RequestInput::get("id"));

        $data['pack'] = $this->mPack->getPack($pack_id);
        $data['user_subscribe_fields'] = UserSettingSubscribe::load();
        $data['group_accesses'] = $this->mGroupAccessModel->getGroupAccesses();

        $data['title'] = Translate::sprint("Pack Manager");

        if( $data['pack']!=NULL){
            $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
            $this->load->view("pack/backend/edit");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");
        }else
            redirect(admin_url("error404"));


    }

    public function select_pack(){


    }

    public function renew(){

        $this->load->model("pack/pack_model");

        $o_pack = $this->pack_model->getPack(SessionManager::getData('pack_id'));

        if($o_pack->price == 0){
            redirect("pack/pickpack?req=upgrade");
        }

        if($pack = $this->pack_model->checkRenewalNeeded()){

            $user_id = intval($this->mUserBrowser->getData('id_user'));

            $latest_inv = $this->pack_model->getLastInvoice($pack->id);
            $latest_inv = json_decode($latest_inv->items,JSON_OBJECT_AS_ARRAY);
            $qty = 1;

            if(isset($latest_inv[0]['qty']))
                $qty = $latest_inv[0]['qty'];

            $id = $this->pack_model->createInvoice($pack, $user_id, $qty, Translate::sprint("Renew Pack"));
            $payment_link = PaymentsProvider::getRedirection("pack");
            $payment_link = $payment_link."?id=".$id;
            redirect($payment_link);

        }else{
            redirect(admin_url("payment/billing"));
        }

    }


    public function invoice(){

       /* $this->load->model("pack/pack_model");
        $this->load->model("payment/payment_model");

        $invoice_id = intval(RequestInput::get('id'));
        $user_id = intval(RequestInput::get('user_id'));

        $invoice = $this->mPack->getInvoice($invoice_id,$user_id);


        $this->payment_model->invoice();
       */

    }


}

/* End of file PackmanagerDB.php */
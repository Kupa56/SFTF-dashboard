<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Offer extends MAIN_Controller {

    public function __construct(){
        parent::__construct();

        $this->init("offer");
    }

    public function onLoad()
    {

        define('MAX_OFFER_IMAGES',10);
        define('KS_NBR_OFFERS_MONTHLY','nbr_offers_monthly');

        define('ADD_OFFER','add');
        define('EDIT_OFFER','edit');
        define('DELETE_OFFER','delete');
        define('MANAGE_OFFERS','manage_offers');

        //load model
        $this->load->model("offer/offer_model","mOfferModel");
        //load helper
        $this->load->helper('offer/offer');
    }

    public function onCommitted($isEnabled)
    {
        parent::onCommitted($isEnabled); // TODO: Change the autogenerated stub

        if(!$isEnabled)
            return;

        $this->load->model("user/group_access_model","mGroupAccessModel");
        $this->load->helper("user/group_access");
        $this->load->helper("user/user");


        AdminTemplateManager::registerMenu(
            'offer',
            "offer/menu",
            2
        );


        //Setup User Config
        UserSettingSubscribe::set('offer',array(
            'field_name' => KS_NBR_OFFERS_MONTHLY,
            'field_type' => UserSettingSubscribeTypes::INT,
            'field_default_value' => -1,
            'config_key' => 'NBR_OFFERS_MONTHLY',
            'field_label' => 'Offers allowed monthly',
            'field_sub_label' => '( -1 Unlimited )',
            'field_comment' => '',
        ));


        if($this->mUserBrowser->isLogged() && GroupAccess::isGranted('offer')){
            $this->load->helper('cms/charts');
            SimpleChart::add('offer','chart_v1_home',function ($months){

                if(GroupAccess::isGranted('offer',MANAGE_OFFERS)){
                    return $this->mOfferModel->getOffersAnalytics($months);
                }else{
                    return $this->mOfferModel->getOffersAnalytics($months,$this->mUserBrowser->getData('id_user'));
                }

            });
        }

        StoreManager::subscribe('offer','store_id');


        //User action listener
        ActionsManager::register('user','user_switch_to',function ($args){
            $this->mOfferModel->switchTo($args['from'], $args['to']);
        });


        //register event to campaign program
        CampaignManager::register(array(
            'module' => $this,
            'api'    => site_url('ajax/offer/getOffersAjax'),
            'callback_input' => function($args){
               return $this->mOfferModel->campaign_input($args);
            },
            'callback_output' => function($args){
                return $this->mOfferModel->campaign_output($args);
            },

            'custom_parameters' => array(
                'html' => $this->load->view('store/backend/campaign/html',array('module'=>'offer'),TRUE),
                'script' => $this->load->view('store/backend/campaign/script',array('module'=>'offer'),TRUE),
                'var' => "offer_custom_parameters",
            )
        ));

        //store
        if(ModulesChecker::isEnabled("bookmarks"))
        NSModuleLinkers::newInstance('offer','getData',function ($args){

            $params = array(
                "offer_id" => $args['id'],
                "limit" => 1,
            );

            $items =  $this->mOfferModel->getOffers($params);

            if(isset($items[Tags::RESULT][0])){

                return array(
                    'currency' => $items[Tags::RESULT][0]['currency'],
                    'label' => $items[Tags::RESULT][0]['name'],
                    'label_description' => $items[Tags::RESULT][0]['description'],
                    'image' => $items[Tags::RESULT][0]['images'],
                );
            }

            return NULL;
        });


        //handle store deleted action
        ActionsManager::register("store","onDelete",function ($args){
            if(isset($args['id'])){
                $this->db->where("store_id",$args['id']);
                $this->db->delete("offer");
            }
        });

        //register upload clear folder
        $this->onClearUploadFolder();


        //check expiored offers
        $this->mOfferModel->checkExpiredOffers();
    }


    private function onClearUploadFolder()
    {
        ActionsManager::register("uploader","onClearFolder",function(){
            //get all active images
            return $this->mOfferModel->getAllActiveImages();
        });
    }

    private function registerModuleActions(){

        GroupAccess::registerActions("offer",array(
            ADD_OFFER,
            EDIT_OFFER,
            DELETE_OFFER,
            MANAGE_OFFERS
        ));

    }

	public function index()
	{

	}

    public function dp()
    {
        redirect(site_url(""));
    }


    public function id(){
        $this->load->library('user_agent');

        $id = intval($this->uri->segment(3));

        if($id==0)
            redirect("?err=1");

        $platform =  $this->agent->platform();

        if(/*Checker::user_agent_exist($user_agent,"ios")*/ strtolower($platform)=="ios"){

            $link = site_url("offer/id/$id");
            $link = str_replace('www.', '', $link);
            $link = str_replace('http://', 'nsapp://', $link);
            $link = str_replace('https://', 'nsapp://', $link);

            $this->session->set_userdata(array(
                "redirect_to" =>  $link
            ));

            redirect("");
        }

        redirect("");

    }


    public function onUpgrade()
    {
        parent::onUpgrade(); // TODO: Change the autogenerated stub

        $this->mOfferModel->updateFields();
        $this->mOfferModel->addFields16();

        $this->registerModuleActions();

        ConfigManager::setValue("OFFERS_IN_DATE",TRUE);

        return TRUE;
    }

    public function onInstall()
    {
        parent::onInstall(); // TODO: Change the autogenerated stub

        $this->mOfferModel->updateFields();
        $this->mOfferModel->addFields16();


        return TRUE;
    }

    public function cron(){

        //load model
        $this->load->model("offer/Offer_model","mOfferModel");
        //$this->mOfferModel->hiddenOfferOutOfDate();
    }

    public function onEnable()
    {
        $this->registerModuleActions();

    }



}

/* End of file OfferDB.php */
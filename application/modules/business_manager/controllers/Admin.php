<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::requireEnabled("business_manager");

    }




    public function businesses()
    {

        if (!GroupAccess::isGranted('store', ADD_STORE))
            redirect(business_manager_url("error?page=permission"));


       $data = array();

       $data['views'] = array('store','offer','event','booking');

       if(RequestInput::get('active')!="" && in_array(RequestInput::get('active'),$data['views'])){
           $data['active_tab'] = RequestInput::get('active');
       }else{
           $data['active_tab'] = 'store';
       }


        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/global");
        $this->load->view("business_manager/footer");

    }


    public function booking()
    {

        if (!GroupAccess::isGranted('booking'))
            redirect(business_manager_url("error?page=permission"));

        $id = intval(RequestInput::get('id'));

        $params = array(
            "limit"   =>1,
            "page"    =>1,
            "id"    =>$id,
            "user_id" => intval(SessionManager::getData("id_user")),
        );


        $d = $this->mBookingModel->getBookings($params);
        if(isset($d[Tags::RESULT][0])){
            $data['reservation'] = $d[Tags::RESULT][0];
        }else{
            redirect(business_manager_url_admin("error404"));
        }

        $data['title'] = Translate::sprintf("Detail - #%s",array(  str_pad($data['reservation']['id'], 6, 0, STR_PAD_LEFT)));

        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/booking/detail");
        $this->load->view("business_manager/footer");

    }


    public function store()
    {

        if (!GroupAccess::isGranted('store', EDIT_STORE))
            redirect(business_manager_url("error?page=permission"));

        $params = array(
            "limit" => 1,
            "store_id" => intval(RequestInput::get('id')),
            "user_id" => intval(SessionManager::getData("id_user")),
        );

        $data['dataStores'] = $this->mStoreModel->getStores($params);
        $data['categories'] = $this->mCategoryModel->getCategories();


        if ($data['dataStores'][Tags::COUNT]==0) {
            redirect(business_manager_url_admin("error404"));
        }

        if (GroupAccess::isGranted('gallery')
            && ModulesChecker::isRegistred("gallery") && isset($data['dataStores'][Tags::RESULT][0]))
            $data['gallery'] = $this->mGalleryModel->getGallery(array(
                "limit" => $this->mGalleryModel->maxfiles,
                "module" => "store",
                "module_id" => $data['dataStores'][Tags::RESULT][0]['id_store']
            ));

        $data['title'] = Translate::sprintf("Edit - %s",array($data['dataStores'][Tags::RESULT][0]['name']));

        $libcssdp = AdminTemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        AdminTemplateManager::addCssLibs($libcssdp);


        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/store/edit");
        $this->load->view("business_manager/footer");

    }

    public function event()
    {

        if (!GroupAccess::isGranted('event', EDIT_EVENT))
            redirect(business_manager_url("error?page=permission"));

        $css = adminAssets("plugins/datepicker/datepicker3.css");
        AdminTemplateManager::addCssLibs($css);


        $event_id = RequestInput::get("id");
        $data['dataEvents'] = $this->mEventModel->getEvents(array(
            "limit"     => 1,
            "event_id"   => $event_id,
        ));

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $data['title'] = Translate::sprintf("Edit - %s",array($data['dataEvents'][Tags::RESULT][0]['name']));

        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/event/edit");
        $this->load->view("business_manager/footer");

    }


    public function offer()
    {

        if (!GroupAccess::isGranted('event', EDIT_EVENT))
            redirect(business_manager_url("error?page=permission"));

        $css = adminAssets("plugins/datepicker/datepicker3.css");
        AdminTemplateManager::addCssLibs($css);


        $params = array(
            "offer_id"  => intval(RequestInput::get("id")),
            "limit"     => 1,
            "user_id" => SessionManager::getData('id_user')
        );

        $data['offer'] = $this->mOfferModel->getOffers($params);
        $data['title'] = Translate::sprintf("Edit - %s",array($data['offer'][Tags::RESULT][0]['name']));

        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/offer/edit");
        $this->load->view("business_manager/footer");

    }

    public function create_offer()
    {

        if (!GroupAccess::isGranted('offer', ADD_OFFER))
            redirect(business_manager_url("error?page=permission"));


        $css = adminAssets("plugins/datepicker/datepicker3.css");
        AdminTemplateManager::addCssLibs($css);


        $data = array();
        $data['title'] = _lang("Create Offer");

        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/offer/create");
        $this->load->view("business_manager/footer");

    }

    public function create_event()
    {

        if (!GroupAccess::isGranted('event', ADD_EVENT))
            redirect(business_manager_url("error?page=permission"));


        $css = adminAssets("plugins/datepicker/datepicker3.css");
        AdminTemplateManager::addCssLibs($css);


        $data = array();
        $data['title'] = _lang("Create Event");

        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/event/create");
        $this->load->view("business_manager/footer");

    }


    public function create_business()
    {

        if (!GroupAccess::isGranted('store', ADD_STORE))
            redirect(business_manager_url("error?page=permission"));


        $libcssdp = AdminTemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        AdminTemplateManager::addCssLibs($libcssdp);


        $data = array();
        $data['title'] = _lang("Create Business");

        $this->load->view("business_manager/header", $data);
        $this->load->view("business_manager/store/create");
        $this->load->view("business_manager/footer");

    }

    public function logout(){

        $def_lang = Translate::getDefaultLangCode();

        $this->mUserBrowser->LogOut();

        redirect("business_manager/index?lang=".$def_lang);

    }

    public function check(){


        if(GroupAccess::isGranted("store",ADD_STORE)){
            redirect(admin_url("business_manager/businesses"));
        }else{
            //upgrade account
            redirect("pack/pickpack");
        }

    }


    public function error404(){



    }



}

/* End of file CampaignDB.php */
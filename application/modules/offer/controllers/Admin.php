<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();

        ModulesChecker::requireEnabled("offer");

        $this->load->helper('url');
    }

	public function index()
	{

	}



    public function view(){

        if (!GroupAccess::isGranted('offer',MANAGE_OFFERS))
            redirect("error?page=permission");

        $data = array();


        $params = array(
            "offer_id"  => intval(RequestInput::get("id")),
            "limit"     => 1
        );

        $data['offer'] = $this->mOfferModel->getOffers($params);

        if (isset($data['offer'][Tags::RESULT]) and count($data['offer'][Tags::RESULT]) == 1) {
            $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
            $this->load->view("offer/backend/html/edit");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");
        }


    }

    public function edit(){

        if (!GroupAccess::isGranted('offer',EDIT_OFFER))
            redirect("error?page=permission");

        $data = array();

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id" => $this->mUserBrowser->getData("id_user")
        ));

        $params = array(
            "offer_id"  => intval(RequestInput::get("id")),
            "limit"     => 1,
            "user_id" => SessionManager::getData('id_user')
        );

        $data['offer'] = $this->mOfferModel->getOffers($params);


        if (isset($data['offer'][Tags::RESULT]) and count($data['offer'][Tags::RESULT]) == 1) {
            $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
            $this->load->view("offer/backend/html/edit");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");
        }


    }

    public function all_offers(){


        if (!GroupAccess::isGranted('offer',MANAGE_OFFERS)  )
            redirect("error?page=permission");


        $data = array();

        $params =array(
            "offer_id" => RequestInput::get("offer_id"),
            "store_id" => RequestInput::get("store_id"),
            "date_end" => RequestInput::get("date_end"),
            "page" => RequestInput::get("page"),
            "search" => RequestInput::get("search"),
            "limit"     => NO_OF_ITEMS_PER_PAGE,
            "is_super"     => TRUE,
            "status"     => RequestInput::get("status"), // filter offer by status
            "filterBy"     => RequestInput::get("filterBy"),
        );

        $data['offers'] = $this->mOfferModel->getOffers($params);
        $data['list_title'] = "All offers";

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("offer/backend/html/offers");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function my_offers(){


        if (!GroupAccess::isGranted('offer')  )
            redirect("error?page=permission");

        $data = array();

        $params =array(
            "offer_id" => RequestInput::get("offer_id"),
            "store_id" => RequestInput::get("store_id"),
            "date_end" => RequestInput::get("date_end"),
            "page"      => RequestInput::get("page"),
            "search" => RequestInput::get("search"),
            "limit"     => NO_OF_ITEMS_PER_PAGE,
            "user_id"     => SessionManager::getData('id_user'),
            "status"     => RequestInput::get("status"), // filter offer by status
            "filterBy"     => RequestInput::get("filterBy"),
        );

        $data['offers'] = $this->mOfferModel->getOffers($params);
        $data['list_title'] = "My offers";

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("offer/backend/html/offers");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function add(){

        if (!GroupAccess::isGranted('offer',ADD_OFFER))
            redirect("error?page=permission");

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));



        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("offer/backend/html/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function hiddenOfferOutOfDate()
    {
        $this->load->model("offer/offer_model","mOfferModel");
        $this->mOfferModel->hiddenOfferOutOfDate();
    }


    public function verify()
    {

        $status = RequestInput::get('status');


        if ($this->mUserBrowser->isLogged()) {

            if (!GroupAccess::isGranted('offer',MANAGE_OFFERS))
                redirect("error?page=permission");


            $id = intval(RequestInput::get('id'));
            $accept = intval(RequestInput::get('accept'));

            $this->db->where('id_offer',$id);
            $this->db->update('offer',array(
                'verified' => 1,
                'status'   => $accept,
            ));


        }
        /*($status == 1) ? redirect(admin_url("offer/my_offers")) : redirect(admin_url("offer/all_offers"));*/
        echo json_encode(array(Tags::SUCCESS => 1));
        return;
    }


}

/* End of file OfferDB.php */
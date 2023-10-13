<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();
        //load model

        ModulesChecker::checkRequirements('event');
        ModulesChecker::requireEnabled('event');

        // hide the offer if the date

        if(defined('ENABLE_AUTO_HIDDEN_EVENTS')){
            if(ENABLE_AUTO_HIDDEN_EVENTS)
                $this->hiddenEventsOutOfDate();
        }else{
            $this->mConfigModel->save('ENABLE_AUTO_HIDDEN_EVENTS',FALSE);
        }

    }

    public function participants(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS_PARTICIPANTS)  )
            redirect("error?page=permission");



        $id = intval(RequestInput::get('event_id'));
        $page = intval(RequestInput::get('page'));
        $limit = intval(RequestInput::get('limit'));

        if(!GroupAccess::isGranted('event',MANAGE_EVENTS) )
            if(!$this->mEventModel->isOwn(SessionManager::getData('id_user'),$id)){
                redirect("error?page=permission&r=ep01");
            }

        $params = array(
            "limit"   =>100,
            "page"    =>$page,
            "event_id" =>$id,
        );

        if($limit>0){
            $params["limit"] = $limit;
        }

        $data['data'] = $this->mEventModel->getParticipants($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("event/backend/html/participants");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function all_events(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS)  )
            redirect("error?page=permission");

        $status = intval(RequestInput::get("status")) ;
        $typeAuth = intval(RequestInput::get("typeAuth")) ;
        $page = intval(RequestInput::get("page"));
        $search = RequestInput::get("search");
        $limit = NO_OF_STORE_ITEMS_PER_PAGE;
        $store_id  = intval(RequestInput::get("store_id"));

        $params = array(
            "limit"   =>$limit,
            "page"    =>$page,
            "store_id" =>$store_id,
            "search"  => $search,
            "status"  => -1
        );

        $data['data'] = $this->mEventModel->getEvents($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("event/backend/html/events");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function my_events(){

        if (!GroupAccess::isGranted('event'))
            redirect("error?page=permission");

        $status = intval(RequestInput::get("status")) ;
        $typeAuth = intval(RequestInput::get("typeAuth")) ;
        $page = intval(RequestInput::get("page"));
        $search = RequestInput::get("search");
        $limit = NO_OF_STORE_ITEMS_PER_PAGE;
        $store_id  = intval(RequestInput::get("store_id"));


        $params = array(
            "limit"   =>$limit,
            "page"    =>$page,
            "store_id" =>$store_id,
            "search"  => $search,
            "status"  => -1
        );

        $params['user_id'] = $this->mUserBrowser->getData("id_user");

        $data['data'] = $this->mEventModel->getEvents($params);

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("event/backend/html/events");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    
    public function create(){

        $data = array();

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $this->load->view(AdminPanel::TemplatePath."/include/header");
        $this->load->view("event/backend/html/create",$data);
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function view(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS))
            redirect("error?page=permission");

        $event_id = RequestInput::get("id");

        $data['dataEvents'] = $this->mEventModel->getEvents(array(
            "limit"     => 1,
            "event_id"   => $event_id,
        ));

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("event/backend/html/edit");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function push_event_cg(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS_PARTICIPANTS))
            redirect("error?page=permission");

        $event_id = RequestInput::get('event_id');
        $guests = RequestInput::get('guests');

        if(preg_match("#,#",$guests))
            $guests = explode(",",$guests);
        else
            $guests = array($guests);

        $guests = $this->mEventModel->reminder_validate_guest(
            SessionManager::getData('id_user'),
            $event_id,
            $guests
        );



        $key = md5(time());
        $data = array(
            "campaign"=> array(
                $key=>array(
                    "guests"    =>  $guests,
                    "event_id"    =>  $event_id,
                )
            ),
        );

        $this->session->set_userdata($data);

        redirect(admin_url("campaign/create/?event_cg=".$key));

    }

    public function reminder(){

        if (!GroupAccess::isGranted('event',MANAGE_EVENTS_PARTICIPANTS))
            redirect("error?page=permission");

        $event_id = RequestInput::get('event_id');
        $users = RequestInput::get('users');

        if(preg_match("#,#",$users))
            $users = explode(",",$users);
        else
            $users = array($users);

        $contacts = $this->mEventModel->reminder_validate_user(
            SessionManager::getData('id_user'),
            $event_id,
            $users
        );

        $result = $this->mEventModel->send_reminders($event_id,$contacts);

        $data['title'] = _lang("Event Reminded");
        $data['contacts'] = $contacts;

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("event/backend/html/mail_sent");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function edit(){

        if (!GroupAccess::isGranted('event',EDIT_EVENT))
            redirect("error?page=permission");

        $event_id = RequestInput::get("id");
        $data['dataEvents'] = $this->mEventModel->getEvents(array(
            "limit"     => 1,
            "event_id"   => $event_id,
        ));

        $data["myStores"] = $this->mStoreModel->getMyAllStores(array(
            "user_id"   => $this->mUserBrowser->getData("id_user")
        ));

        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("event/backend/html/edit");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function hiddenEventsOutOfDate()
    {
        $this->mEventModel->hiddenEventsOutOfDate();
    }


    public function verify()
    {

        if ($this->mUserBrowser->isLogged()) {

            if (!GroupAccess::isGranted('event',MANAGE_EVENTS))
                redirect("error?page=permission");


            $id = intval(RequestInput::get('id'));
            $accept = intval(RequestInput::get('accept'));


            $this->db->where('id_event',$id);
            $this->db->update('event',array(
                'verified' => 1,
                'status'   => $accept,
            ));


        }

        //redirect(admin_url('event/all_events'));

        echo json_encode(array(Tags::SUCCESS => 1));
        return;
    }




}

/* End of file EventDB.php */
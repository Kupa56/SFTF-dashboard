<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Admin extends ADMIN_Controller {

    public function __construct(){
        parent::__construct();

        ModulesChecker::requireEnabled("messenger");

    }

	public function index()
	{

	}

    public function messages(){


        if (!GroupAccess::isGranted('messenger'))
            redirect("error?page=permission");

        $username = RequestInput::get("u");
        $userData = $this->mUserModel->getUserDataHashId($username);

        if($userData == NULL && Text::checkUsernameValidate($username)){
            $userData['username'] = $username;
        }

        $data['userData'] = $userData;

        $list = Modules::run("messenger/ajax/getMessages",array(
            "username"      => !empty($userData)?$userData['username']:"",
            "page"          => 1,
            "lastMessageId" => 0
        ));

        //parse to message view
        if(isset($list[Tags::SUCCESS]) AND $list[Tags::SUCCESS]==1 && count($list[Tags::RESULT])>0){
            $data['messages_views']         = Modules::run("messenger/ajax/getMessagesViews",$list[Tags::RESULT]);
            $data['messages_pagination']    = $list["pagination"];
            $data['lastMessageId']          = $list["lastMessageId"];
            $data['messengerData']          = $list[Tags::RESULT];

        }else{
            $data['messages_views'] = "";
        }


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("messenger/backend/html/discussions");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


}

/* End of file MessengerDB.php */
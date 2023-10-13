<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mUserModel
 *
 * @author idriss
 */
class Ajax extends AJAX_Controller
{


    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("user/user_browser", "mUserBrowser");


    }

    public function add_group_access(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('user',MANAGE_GROUP_ACCESS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $name = RequestInput::post('name');
        $grp_access = RequestInput::post('grp_access');

        $result = $this->mGroupAccessModel->add_group_access(array(
            'name' => $name,
            'grp_access' => $grp_access,
        ));

        echo json_encode($result,JSON_FORCE_OBJECT);return;

    }


    public function edit_group_access(){

        $this->enableDemoMode();

        if(!GroupAccess::isGranted('user',MANAGE_GROUP_ACCESS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = RequestInput::post('id');
        $name = RequestInput::post('name');
        $grp_access = RequestInput::post('grp_access');

        $result = $this->mGroupAccessModel->edit_group_access(array(
            'id_grp'        => $id,
            'name'          => $name,
            'grp_access'    => $grp_access,
        ));

        echo json_encode($result,JSON_FORCE_OBJECT);return;

    }


    public function refreshPackage($uid = 0)
    {

        $this->load->model("User/mUserModel");
        $this->mUserModel->refreshPackage($uid);

    }

    public function signUp()
    {


        if(reCAPTCHA==TRUE){
            $response =  MyCurl::run("https://www.google.com/recaptcha/api/siteverify",array(
                'secret'    => '6Ld6s4QUAAAAAKKWRIkFKdFU946U3uHOdNhxiG3n',
                'remoteip'  => $this->input->ip_address(),
                'response'  => RequestInput::post('recaptcha_response')
            ));

            $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

            if(isset($response['success']) and $response['success']==false){
                echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                    "error"=>"reCAPTCHA invalid! ".json_encode($response),
                )));
                return;
            }
        }


        $authType =   RequestInput::post("authType");

        $params = array(
            'name' => RequestInput::post("name"),
            'username' => RequestInput::post("username"),
            'password' => RequestInput::post("password"),
            'email' => RequestInput::post("email"),
            "typeAuth" => DEFAULT_USER_GRPAC
        );


        //Switch to the select language
        $lang =  RequestInput::post("lang") ;

        if(isset($lang) )
        {
            if(intval($lang) and $lang == -1)
            {
                $default_language = Translate::getDefaultLang();
                $params['user_language'] = $default_language;
            }else
            {
                Translate::changeSessionLang($lang);
                $params['user_language'] = $lang;
            }

        }

        $default_timezone = TimeZoneManager::getTimeZone();

        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $timezone = RequestInput::post("timezone");

        if(in_array($timezone,$timezones)){
            $default_timezone = $timezone;
        }


        $params['user_timezone'] = $default_timezone;

        $data = $this->mUserModel->signUp($params, array(
            "name",
            "username",
            "password",
            "email",
            "typeAuth"
        ));


        if ($data[Tags::SUCCESS] == 1) {

            $this->mUserBrowser->cleanToken("S0XsOi");
            $this->mUserBrowser->setID($data[Tags::RESULT][0]['id_user']);
            $this->mUserBrowser->setUserData($data[Tags::RESULT][0]);

            $this->session->set_userdata(array(
                "savesession"=>array()
            ));

            //send message welcome
            if (MESSAGE_WELCOME != "") {

                $this->load->model("messenger/messenger_model");

                $this->db->select("id_user");
                $this->db->order_by("id_user", "ASC");
                $user = $this->db->get("user", 1);
                $user = $user->result();

                $result = $this->messenger_model->sendMessage(array(
                    "sender_id" => $user[0]->id_user,
                    "receiver_id" => $data[Tags::RESULT][0]['id_user'],
                    "discussion_id" => 0,
                    "content" => Text::input(MESSAGE_WELCOME)
                ));

            }

        }


        $callback_user_login_redirection
            = $this->session->userdata('callback_user_login_redirection');

        if($callback_user_login_redirection!="")
            $data['url'] = $callback_user_login_redirection;


        if(ModulesChecker::isEnabled("pack") && $data[Tags::SUCCESS]==1 && isset($data[Tags::RESULT][0])){
            $data['url'] = site_url("pack/pickpack");
        }

        echo json_encode($data);
        return;
    }

    public function resetpassword()
    {

        $token = RequestInput::post("stoken");
        $password = RequestInput::post("password");
        $confirm = RequestInput::post("confirm");


        $this->load->model("User/mUserModel");

        $data = $this->mUserModel->resetPassword(array(
            "token" => $token,
            "password" => $password,
            "confirm" => $confirm
        ));

        echo json_encode($data);

    }

    public function forgetpassword()
    {

        $login = RequestInput::post("login");
        $token = RequestInput::post("token");

        $this->load->model("User/mUserModel");

        $data = $this->mUserModel->sendNewPassword(array(
            "login" => $login
        ));

        echo json_encode($data);
    }


    public function signIn()
    {

        if(reCAPTCHA==TRUE){
            $response =  MyCurl::run("https://www.google.com/recaptcha/api/siteverify",array(
                'secret'    => '6Ld6s4QUAAAAAKKWRIkFKdFU946U3uHOdNhxiG3n',
                'remoteip'  => $this->input->ip_address(),
                'response'  => RequestInput::post('recaptcha_response')
            ));

            $response = json_decode($response,JSON_OBJECT_AS_ARRAY);

            if(isset($response['success']) and $response['success']==false){
                echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                    "error"=>"reCAPTCHA invalid! ".json_encode($response),
                )));
                return;
            }
        }

        $errors = array();

        $login = Security::decrypt(RequestInput::post("login"));
        $password = Security::decrypt(RequestInput::post("password"));
        $token = Security::decrypt(RequestInput::post("token"));
        $authType = Security::decrypt(RequestInput::post("authType"));

        $params = array(
            "login" => trim($login),
            "password" => $password,
            "user_language" => Translate::getDefaultLang()
        );

        $data = $this->mUserModel->signIn($params);


        if(isset($data[Tags::SUCCESS]) && $data[Tags::SUCCESS]==1){

            if (isset($data[Tags::RESULT][0])){

                $user = $data[Tags::RESULT][0];

                $callback_user_login_redirection
                    = $this->session->userdata('callback_user_login_redirection');

                if($callback_user_login_redirection!="")
                    $data['url'] = $callback_user_login_redirection;


                //save the session
                if (isset($data[Tags::RESULT][0])){
                    $this->mUserBrowser->setUserData($data[Tags::RESULT][0]);
                    $this->session->set_userdata(array(
                        "savesession"=>array()
                    ));
                }
            }
        }


        echo json_encode(
            $data
        );
        return;

    }

    public function profileEdit()
    {

        //check if user have permission
        $this->enableDemoMode();

        if($this->mUserBrowser->isLogged()){
            $errors = array();

            $id_user = intval($this->mUserBrowser->getData("id_user"));

            $password = RequestInput::post("password");
            $confirm = RequestInput::post("confirm");

            $name = RequestInput::post("name");
            $username = RequestInput::post("username");
            $email = RequestInput::post("email");
            $phone = RequestInput::post("phone");

            $token = RequestInput::post("token");

            $tokenSession = $this->mUserBrowser->getToken("S0XsNOiA");
            if ($token != $tokenSession) {
                echo json_encode(array(Tags::SUCCESS => 0));return;
            }


            $image = RequestInput::post("image");

            $params = array(
                "id_user"               =>$id_user,
                "password"              =>$password,
                "confirm"               =>$confirm,
                "name"                  =>$name,
                "username"              =>$username,
                "email"                 =>$email,
                "phone"                 =>$phone,
                "image"                 =>$image,
                "self_edit"             =>TRUE
            );




            $data = $this->mUserModel->edit($params);

            if(isset($data[Tags::RESULT][0])){
                $this->mUserBrowser->refreshData(  $data[Tags::RESULT][0]['id_user']  );
            }

            if(isset($data[Tags::SUCCESS]) && intval($data[Tags::SUCCESS]) && $data[Tags::SUCCESS] == 1){
                if($data[Tags::RESULT][0]['email'] != $this->mUserBrowser->getData("email")){
                    $this->mUserModel->userMailConfirmation($data[Tags::RESULT][0]);
                    $this->mUserBrowser->setUserData($data[Tags::RESULT][0]);
                }

            }

            echo json_encode($data);return;
        }


        echo json_encode(array(Tags::SUCCESS=>0));return;
    }

    public function edit()
    {

        if(!GroupAccess::isGranted('user',MANAGE_USERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $errors = array();

        $id_user = intval(RequestInput::post("id"));
        $password = RequestInput::post("password");
        $confirm = RequestInput::post("confirm");
        $name = RequestInput::post("name");
        $username = RequestInput::post("username");
        $email = RequestInput::post("email");
        $typeAuth = RequestInput::post("typeAuth");
        $phone = RequestInput::post("phone");

        $token = RequestInput::post("token");
        $tokenSession = $this->mUserBrowser->getToken("S0XsNOi");
        if ($token != $tokenSession) {
            return array(Tags::SUCCESS => 0);
        }

        $user_settings = RequestInput::post("user_settings");

        $image = RequestInput::post("image");

        $params = array(
            "id_user"               =>$id_user,
            "password"              =>$password,
            "confirm"               =>$confirm,
            "name"                  =>$name,
            "username"              =>$username,
            "email"                 =>$email,
            "typeAuth"              =>$typeAuth,
            "user_settings"         =>$user_settings,
            "image"                 =>$image,
            "phone"                 =>$phone,
        );


        $data = $this->mUserModel->edit($params);
        echo json_encode($data);return;

    }

    public function getOwners()
    {

        if(!GroupAccess::isGranted('user')){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $user_id = $this->mUserBrowser->getData("id_user");

       $json = $this->mUserModel->getOwners(array(
           "user_id"   => $user_id,
       ));

        echo json_encode($json);
    }


    public function checkAdminData($id = 0)
    {

        $this->db->select("user.*,setting.*");
        $this->db->where("user.id_user", $id);
        $this->db->join("setting", "setting.user_id=user.id_user", "INNER");
        $this->db->from("user");

        $admin = $this->db->get();
        $admin = $admin->result_array();

        if (count($admin) > 0)
            return $admin[0];
        else
            return null;

    }

    public function create()
    {

        if(!GroupAccess::isGranted('user',ADD_USERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $name = RequestInput::post("name");
        $username = RequestInput::post("username");
        $password = RequestInput::post("password");
        $confirm = RequestInput::post("confirm");
        $email = RequestInput::post("email");
        $typeAuth = RequestInput::post("typeAuth");
        $tel = RequestInput::post("tel");
        $image = RequestInput::post("image");

        $user_settings = RequestInput::post("user_settings");


        if ($this->mUserBrowser->isLogged()) {
            // $data["manager"]= $this->mUserBrowser->getAdmin("id_user");
        } else {
            $errors['login'] = Translate::sprint(Messages::USER_MISS_AUTHENTIFICATION);
            echo json_encode(array(Tags::SUCCESS => 0, "errors" => $errors));
            return;
        }

        $params = array(
            "image"                => $image,
            "name"                  => $name,
            "username"              => $username,
            "password"              => $password,
            "confirm"               => $confirm,
            "email"                  => $email,
            "tel"                   => $tel,
            "typeAuth"              => $typeAuth,
            "user_settings"         => $user_settings,
        );


        $data = $this->mUserModel->create($params);

        echo json_encode($data);return;

    }

    public function getUser($params = array())
    {

        $this->load->model("User/mUserModel");
        return $this->mUserModel->getUsers($params);

    }

    public function detailUser()
    {

        $id = intval(RequestInput::get("id"));

        if (isset($id) AND $id > 0) {
            $this->db->where("id_user", $id);
        }
        $myUsers = $this->db->get("user");
        $myUsers = $myUsers->result();
        return array("success" => 1, "user" => $myUsers);

    }

    public function delete()
    {

        if(!GroupAccess::isGranted('user',DELETE_USERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $id_user = intval(RequestInput::post("id"));
        $switch_to = intval(RequestInput::post("switch_to"));

        $result = $this->mUserModel->delete($id_user);

        //assign all to another owner
        if ($switch_to > 0 && $result) {
            ActionsManager::add_action('user','user_switch_to',array(
                'from' => $id_user,
                'to'   => $switch_to
            ));
        }

        if($result){
            echo json_encode(array("success" => 1));return;
        }

        echo json_encode(array("success" => 0, "errors" => array("err"=>_lang("Couldn't remove this user"))));return;

    }

    public function confirm()
    {
        $id = intval(RequestInput::get("id"));

        if (GroupAccess::isGranted('user',MANAGE_USERS)  && $id > 0) {

            $this->db->where("id_user", $id);
            $this->db->update('user', array(
                "confirmed" => 1
            ));

        }

        echo json_encode(array(Tags::SUCCESS => 1));
        return;
    }

    public function access()
    {

        $this->enableDemoMode();

        $id = intval(RequestInput::get("id"));

        echo json_encode(
            $this->mUserModel->access($id)
        );

    }


}

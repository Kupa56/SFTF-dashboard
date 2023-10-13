<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model("user/user_model", "mUserModel");
        $this->load->model("messenger/messenger_model", "mMessengerModel");
        $this->load->library('session');

    }
    public function otpSendCode()
    {

        $result = $this->OtpModel->send(
            RequestInput::post('userId'),
            RequestInput::post('telephone'),
        );

        echo json_encode($result, JSON_FORCE_OBJECT);
    }

    public function otpVerifyCode()
    {
        $result = $this->OtpModel->verify(
            RequestInput::post('userId'),
            RequestInput::post('telephone'),
            RequestInput::post('otpCode'),
        );
        echo json_encode($result, JSON_FORCE_OBJECT);
    }

    public function findUserByToken()
    {

        $token = RequestInput::post("token");
        $type = RequestInput::post("type");

        if($type==""){$type = "BusinessLogged";}

        $result = $this->mUserModel->findUserByToken($token,$type);

        echo json_encode($result, JSON_FORCE_OBJECT);
    }

    public function generateUniqueQRCode()
    {

        $this->requireAuth();


        $user_id = RequestInput::post("user_id");
        $result = $this->mUserModel->generateUniqueQRCode($user_id);
        echo json_encode($result, JSON_FORCE_OBJECT);
    }


    public function disableAccount()
    {

        $this->requireAuth();

        $user_id = RequestInput::post("user_id");
        $user_token = RequestInput::post("user_token");

        $data = TokenSetting::get_by_token($user_token,"logged");

        if(!empty($data) and isset($data->uid) && intval($data->uid) == intval($user_id)){

            $result = $this->mUserModel->delete(intval($data->uid));

            if($result == TRUE){
                echo json_encode(array(Tags::SUCCESS=>1), JSON_FORCE_OBJECT);return;
            }
        }

        echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>"can't disable this account")), JSON_FORCE_OBJECT);return;
    }

    public function registerToken()
    {

        $fcm_id = RequestInput::post("fcm_id");
        $sender_id = RequestInput::post("sender_id");
        $lat = RequestInput::post("lat");
        $lng = RequestInput::post("lng");
        $platform = RequestInput::post("platform");

        $result = $this->mUserModel->createNewGuest(array(
            "fcm_id" => $fcm_id,
            "sender_id" => $sender_id,
            "lat" => $lat,
            "lng" => $lng,
            "platform" => $platform,
        ));

        echo json_encode($result, JSON_FORCE_OBJECT);
    }


    public function refreshPosition()
    {

        $guest_id = intval(RequestInput::post("guest_id"));
        $lat = RequestInput::post("lat");
        $lng = RequestInput::post("lng");
        $userId = RequestInput::post("userId");

        $result = $this->mUserModel->refreshPosition(array(
            "guest_id" => $guest_id,
            "lat" => $lat,
            "lng" => $lng,
            "userId" => $userId,
        ));

        echo json_encode($result, JSON_FORCE_OBJECT);
    }

    public function checkTokenIsValide($params = array())
    {

        $errors = array();
        $data = array();

        //extract — Importe les variables dans la table des symboles
        extract($params);

        if (
            (isset($mac_adr) and Security::checkMacAddress($mac_adr))
            AND
            (isset($token) and Security::checkToken($token))

        ) {


            $this->db->where("_id", $token);
            $this->db->where("_device_id", $mac_adr);

            $c = $this->db->count_all_results('token');

            if ($c == 1) {
                return TRUE;
            }

        }


        return FALSE;
    }


    public function generateToken()
    {



        $ip_adr = Security::decrypt(RequestInput::post("ip_adr"));
        $mac_adr = Security::decrypt(RequestInput::post("mac_adr"));

        $params = array(
            "ip_adr" => $ip_adr,
            "mac_adr" => $mac_adr
        );

        $data = $this->mUserModel->generateToken($params);

        echo json_encode($data);

    }


    public function checkUser()
    {


        $user_id = intval(RequestInput::post("user_id"));

        $params = array(
            "user_id" => $user_id
        );

        $data = $this->mUserModel->checkUser($params);

        echo json_encode($data);

    }


    public function uploadImage()
    {

        $this->load->model("upload_v1");
        /*
         * CHECK SECURITY
         */


        echo json_encode($this->upload_v1->uploadImage(@$_FILES['image']));
    }


    public function signIn()
    {


        /*///////////////////////////////////////////////////////////////
         * //////////////////////////////////////////////////////////////
         * encrytation data developped by amine
         *//////////////////////////////////////////////////////////////
        ///////////////////////////////////////////////////////////////

        $login = Security::decrypt(RequestInput::post("login"));
        $mac_address = Security::decrypt(RequestInput::post("mac_address"));
        $password = Security::decrypt(RequestInput::post("password"));
        $lat = Security::decrypt(RequestInput::post("lat"));
        $lng = Security::decrypt(RequestInput::post("lng"));


        $guest_id = Security::decrypt(RequestInput::post("guest_id"));

        $params = array(
            "login" => $login,
            "password" => $password,
            "lat" => $lat,
            "lng" => $lng,
            "guest_id" => $guest_id,
            "mac_address" => $mac_address
        );


        $data = $this->mUserModel->signIn($params);

        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array());
        } else {
            echo json_encode($data);
        }

    }


    public function changeUserStatus()
    {

        $user_id = intval(RequestInput::post("user_id"));
        $status = intval(Security::decrypt(RequestInput::post("status")));
        $lat = doubleval(RequestInput::post("lat"));
        $lng = doubleval(RequestInput::post("lng"));

        $params = array(
            'user_id' => $user_id,
            'status' => $status,
            'lat' => $lat,
            'lng' => $lng,
        );

        $data = $this->mUserModel->changeUserStatus($params);


        echo json_encode($data);
    }

    public function checkUserConnection()
    {


       $email = /*Security::decrypt*/
            (RequestInput::post("email"));
        $userid = Security::decrypt(RequestInput::post("userid"));
        $username = /*Security::decrypt*/
            (RequestInput::post("username"));
        $senderId = Security::decrypt(RequestInput::post("senderid"));

        $params = array(
            "email" => $email,
            "userid" => $userid,
            "username" => $username,
            "senderid" => $senderId
        );

        $data = $this->mUserModel->checkUserConnection($params);

        if ($data[Tags::SUCCESS] == 1 AND count($data[Tags::RESULT]) > 0) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => 1));
        } else {
            echo json_encode($data);
        }


    }


    public function getUsers()
    {


        $lat = trim(RequestInput::post("lat"));
        $lng = Security::decrypt(RequestInput::post("lng"));
        $page = Security::decrypt(RequestInput::post("page"));
        $limit = Security::decrypt(RequestInput::post("limit"));
        $user_id = Security::decrypt(RequestInput::post("user_id"));
        $uid = Security::decrypt(RequestInput::post("uid"));

        $params = array(
            'lat' => $lat,
            'lng' => $lng,
            'limit' => $limit,
            'page' => $page,
            'user_id' => $user_id,
            'uid' => $uid, //<=== requested user
        );

        $data = $this->mUserModel->getUsers($params);

        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array(Tags::COUNT => $data[Tags::COUNT]));
        } else {
            echo json_encode($data);
        }


    }


    public function signUp()
    {


       $username = RequestInput::post("username");
        $password = Security::decrypt(RequestInput::post("password"));

        $first_name = RequestInput::post("first_name");
        $last_name = RequestInput::post("last_name");
        $email = RequestInput::post("email");
        $phone = Text::input(RequestInput::post("phone"));
        $name = /*Text::input*/
            (RequestInput::post("name"));
        $mac_addr = Security::decrypt(RequestInput::post("mac_address"));
        $token = (RequestInput::post("token"));
        $auth_type = (RequestInput::post("auth_type"));

        $lat = Security::decrypt(RequestInput::post("lat"));
        $lng = Security::decrypt(RequestInput::post("lng"));
        $image = Security::decrypt(RequestInput::post("image"));

        $guest_id = Security::decrypt(RequestInput::post("guest_id"));


        $params = array(
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'phone' => $phone,
            'name' => $name,
            'lat' => $lat,
            'lng' => $lng,
            'image' => $image,
            'mac_address' => $mac_addr,
            'token' => $token,
            "auth_type" => $auth_type,
            "guest_id" => $guest_id,
            "typeAuth" => DEFAULT_USER_MOBILE_GRPAC
        );

        $data = $this->mUserModel->signUp($params, array(
            "name"
        ));


        //send message welcome
        if (MESSAGE_WELCOME != "" && isset($data[Tags::RESULT])) {

            $this->load->model("Messenger/MessengerModel", "mMessengerModel");
            $this->db->select("id_user");
            $this->db->order_by("id_user", "ASC");
            $user = $this->db->get("user", 1);
            $user = $user->result();

            $result = $this->mMessengerModel->sendMessage(array(
                "sender_id" => $user[0]->id_user,
                "receiver_id" => $data[Tags::RESULT][0]['id_user'],
                "discussion_id" => 0,
                "content" => Text::input(MESSAGE_WELCOME)
            ));

        }


        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, $data);
        } else {
            echo json_encode($data);
        }


    }

    public function userAuth()
    {

        $username = RequestInput::post("username");
        $password = Security::decrypt(RequestInput::post("password"));
        $email = RequestInput::post("email");
        $phone = Text::input(RequestInput::post("phone"));
        $name = (RequestInput::post("name"));
        $token = (RequestInput::post("token"));
        $image = Security::decrypt(RequestInput::post("image"));
        $guest_id = Security::decrypt(RequestInput::post("guest_id"));

        $auth_type = Security::decrypt(RequestInput::post("auth_type"));
        $auth_id = Security::decrypt(RequestInput::post("auth_id"));
        $avatar_url = Security::decrypt(RequestInput::post("avatar_url"));

        $params = array(
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'phone' => $phone,
            'name' => $name,
            'image' => $image,
            'token' => $token,
            "guest_id" => $guest_id,
            "auth_type" => $auth_type,
            "auth_id" => $auth_id,
            "avatar_url" => $avatar_url,
            "typeAuth" => DEFAULT_USER_MOBILE_GRPAC
        );


        $data = $this->mUserAuth->checkUserAuth($params);

        //send message welcome
        if (MESSAGE_WELCOME != "" && isset($data[Tags::RESULT])
            && ModulesChecker::isEnabled("messenger") && (isset($data["newAuth"]))) {

            $this->load->model("Messenger/MessengerModel", "mMessengerModel");
            $this->db->select("id_user");
            $this->db->order_by("id_user", "ASC");
            $user = $this->db->get("user", 1);
            $user = $user->result();

            $result = $this->mMessengerModel->sendMessage(array(
                "sender_id" => $user[0]->id_user,
                "receiver_id" => $data[Tags::RESULT][0]['id_user'],
                "discussion_id" => 0,
                "content" => Text::input(MESSAGE_WELCOME)
            ));

        }


        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, $data);
        } else {
            echo json_encode($data);
        }


    }


    public function updateAccountPassword()
    {

       $user_id = Security::decrypt(RequestInput::post("user_id"));
        $username = trim(RequestInput::post("username"));
        $current_password = trim(RequestInput::post("current_password"));
        $new_password = trim(RequestInput::post("new_password"));
        $confirm_password = trim(RequestInput::post("confirm_password"));


        $params = array(
            'user_id' => $user_id,
            'username' => $username,
            'current_password' => $current_password,
            'new_password' => $new_password,
            'confirm_password' => $confirm_password
        );

        $data = $this->mUserModel->updateAccountPassword($params);

        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array());
        } else {
            echo json_encode($data);
        }


    }


    public function updateAccount()
    {

        $this->requireAuth();

        $user_id = Security::decrypt(RequestInput::post("user_id"));
        $username = trim(RequestInput::post("username"));
        $password = Security::decrypt(RequestInput::post("password"));
        $email = RequestInput::post("email");
        $name = RequestInput::post("name");
        $first_name = RequestInput::post("first_name");
        $last_name = RequestInput::post("last_name");
        $phone = RequestInput::post("phone");
        $city = RequestInput::post("city_id");
        $mac_addr = Security::decrypt(RequestInput::post("mac_address"));
        $token = (RequestInput::post("token"));

        $oldUsername = (RequestInput::post("oldUsername"));
        $user_id = (RequestInput::post("user_id"));
        $job = (RequestInput::post("job"));


        $params = array(
            'user_id' => $user_id,
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'name' => $name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'oldUsername' => $oldUsername,
            'phone' => $phone,
            'city' => $city,
            'job' => $job,
            'mac_address' => $mac_addr,
            'token' => $token
        );

        $data = $this->mUserModel->updateAccount($params);

        if ($data[Tags::SUCCESS] == 1) {
            echo Json::convertToJson($data[Tags::RESULT], Tags::RESULT, TRUE, array());
        } else {
            echo json_encode($data);
        }


    }

    public function updatePhone()
    {

        $user_id = Security::decrypt(RequestInput::post("user_id"));
        $phone = RequestInput::post("phone");

        $params = array(
            'user_id' => $user_id,
            'phone' => $phone,
        );

        $data = $this->mUserModel->updatePhone($params);

        echo json_encode($data);

    }

    public function blockUser()
    {

        $state = RequestInput::post('state');
        if ($state == "true") {
            $state = TRUE;
        } else {
            $state = FALSE;
        }

        $params = array(
            "user_id" => intval(RequestInput::post('user_id')),
            "blocked_id" => intval(RequestInput::post('blocked_id')),
            "state" => $state
        );

        $data = $this->mUserModel->blockUser($params);

        echo json_encode($data, JSON_FORCE_OBJECT);
        return;

    }


}

<?php

class User_auth extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
    }


    private $auth_types = array("facebook","apple","google","twitter");

    public function newUserAuth($params = array())
    {

        $auth_data = array();
        $errors = array();

        if(isset($params["auth_type"]) && in_array($params["auth_type"],$this->auth_types)){
            $auth_data['auth_type'] = $params["auth_type"];
        }else{
            $errors["auth_type"] = _lang("Invalid input (Auth type)");
        }

        if(isset($params["auth_id"]) && $params["auth_id"] != ""){
            $auth_data['auth_id'] = $params["auth_id"];
        }else{
            $errors["auth_id"] = _lang("Invalid input (Auth ID)");
        }

        if(!empty($errors)){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        }

        if (isset($params['password']) && trim($params['password']) == "" OR !isset($params['password'])) {
            $params['password'] =  $auth_data['auth_id'];
        }

        //add missed field
        if (isset($params['name']) && $params['name'] != "") {
            $params['username'] = $this->generateUsername($params['name']);
        }

        if (isset($params['email']) && trim($params['email']) == "") {
            $params['email'] = $this->generateEmail($params['username']);
        }


        $result = $this->mUserModel->signUp($params);

        //update fields
        if($result[Tags::SUCCESS]==1){

            //update avatar from url
            if(isset($params['avatar_url']) && $params['avatar_url']!=""){

                $imageData = url_get_content($params['avatar_url']);
                $image64 = base64_encode($imageData);
                $uploadResult = $this->uploader_model->uploadImage64($image64);

                //update profile fields
                if(isset($uploadResult['result']['image'])){
                    $this->mUserModel->updatePhotosProfile(array(
                        "image"  => $uploadResult['result']['image'],
                        "user_id"   => $result[Tags::RESULT][0]['id_user'])
                    );
                }

            }

            //update tokens
            $token = md5(time() . rand(0, 999));

            $this->db->insert('token', array(
                "id" => $token,
                "uid" => $result[Tags::RESULT][0]['id_user'],
                "type" => "socialMediaAuth",
                "content" => $auth_data['auth_id'],
                "method" => $auth_data['auth_type'],
                "created_at" => date("Y-m-d H:i:s", time())
            ));

            return $this->mUserModel->forceSignInById($result[Tags::RESULT][0]['id_user']);

        }

        return $result;

    }


    public function checkUserAuth($params = array())
    {

        $auth_data = array();
        $errors = array();

        if(isset($params["auth_type"]) && in_array($params["auth_type"],$this->auth_types)){
            $auth_data['auth_type'] = $params["auth_type"];
        }else{
            $errors["auth_type"] = _lang("Invalid input (Auth type)");
        }

        if(isset($params["auth_id"]) && $params["auth_id"] != ""){
            $auth_data['auth_id'] = $params["auth_id"];
        }else{
            $errors["auth_id"] = _lang("Invalid input (Auth ID)");
        }

        if(!empty($errors)){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        }

        //find tokens
        $this->db->where("token.type","socialMediaAuth");
        $this->db->where("token.method",$auth_data['auth_type']);
        $this->db->like("token.content",$auth_data['auth_id']);
        $this->db->where("user.hidden",0);
        $this->db->join("user","user.id_user=token.uid");

        $token = $this->db->get('token',1);
        $token = $token->result_array();


        if(count($token)==0){
            $result =  $this->newUserAuth($params);
            if($result[Tags::SUCCESS]==1){
                $result["newAuth"] = TRUE;
            }
            return $result;
        }

        $result = $this->mUserModel->forceSignInById(intval($token[0]['uid']));

        if(isset($result[Tags::RESULT][0])){
             $this->mUserModel->manageGuest($params['guest_id'],intval($token[0]['uid']));
        }


        return $result;
    }


    public function generateUsername($string)
    {

        $pattern = " ";
        $firstPart = strstr(strtolower($string), $pattern, true);
        $secondPart = substr(strstr(strtolower($string), $pattern, false), 0, 3);
        $nrRand = rand(0, 100);
        $username = trim($firstPart) . trim($secondPart) . trim($nrRand);

        $this->db->where('username',$username);
        $count = $this->db->count_all_results('user');

        if($count>0){
            $username = $this->generateUsername($string);
        }

        return $username;
    }

    public function generateEmail($username)
    {

        $domain = "domain.com";

        if (filter_var(DEFAULT_EMAIL, FILTER_VALIDATE_EMAIL)) {
            // split on @ and return last value of array (the domain)
            $array = explode('@', DEFAULT_EMAIL);
            $domain = array_pop($array);
        }

        return $username . "-auth@" . $domain;
    }


    public function checkRequestFacebookDataDeletion($user_id) {

        $this->db->where("id_user",$user_id);
        $user = $this->db->get('user',1);
        $user = $user->result();

        if(count($user)>0){
            echo "User found: <span class='color:red'>Request</span>";
        }else{
            echo "User not found: <span class='color:green'>Removed</span>";
        }

    }

}


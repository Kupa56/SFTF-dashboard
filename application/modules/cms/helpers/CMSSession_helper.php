<?php


class ClientSession{

    public static function getUser(){
        $ctx = &get_instance();
        return $ctx->candidat_model->getUser(ClientSession::getData("id_user"));
    }

    public static function isLogged(){

        $ctx = &get_instance();

        if(!isset($ctx->session->FCSession))
            return FALSE;

        if(isset($ctx->session->FCSession) && $ctx->session->FCSession == NULL)
            return FALSE;

        $data = $ctx->session->FCSession;
        if( !empty($data) && isset($data['id_user']) && $data['id_user']>0){
            return TRUE;
        }

        return FALSE;
    }


    public static function getData($index=""){

        $ctx = &get_instance();
        if(isset($ctx->session->FCSession[$index]) AND !empty($ctx->session->FCSession[$index])){
            return $ctx->session->FCSession[$index];
        }else{
            return ;
        }

    }

    public static function getFirstName(){

        $ctx = &get_instance();
        if(isset($ctx->session->FCSession["name"]) AND !empty($ctx->session->FCSession["name"])){
            $full_name =  $ctx->session->FCSession["name"];
            $full_name = explode(" ",$full_name);
            return $full_name[0];
        }else{
            return "";
        }

    }

    public static function logOut(){

        $ctx = &get_instance();

        if(self::isLogged()){
            $ctx->session->set_userdata(array(
                "FCSession" => NULL
            ));
        }


        return TRUE;
    }

    public static function getFirstImage($placeholder=""){

        $ctx = &get_instance();
        if(isset($ctx->session->FCSession["images"]) AND !empty($ctx->session->FCSession["images"])){
            $images = ImageManagerUtils::getValidImages($ctx->session->FCSession["images"]);
            $pic = ImageManagerUtils::getFirstImage($images,ImageManagerUtils::IMAGE_SIZE_200);
            if($pic!="")
                return $pic;
        }
        return $placeholder;
    }

    public static function setValue($key,$value){

        $ctx = &get_instance();

        $ctx->session->set_userdata(array(
            "value_".$key => $value
        ));

    }

    public static function getValue($key,$default=null){

        $ctx = &get_instance();
        $key = "value_".$key;
        return $ctx->session->userdata($key);

    }

    public static function createSession($user_id){
        $ctx = &get_instance();
        $user = $ctx->mUserModel->getUserData($user_id);
        $ctx->session->set_userdata(array(
            "FCSession" => $user
        ));
    }

    public static function removeSession($user_id){
        $ctx = &get_instance();
        $ctx->session->set_userdata(array(
            "FCSession" => NULL
        ));
    }


    public static function connectDashboard(){

        $ctx = &get_instance();
        $ctx->mUserBrowser->refreshData( ClientSession::getData("id_user") );
        $ctx->mUserBrowser->setID( ClientSession::getData("id_user") );

        return TRUE;
    }

    public static function disconnectDashboard(){

        $ctx = &get_instance();
        $ctx->mUserBrowser->setUserData( array() );
        $ctx->mUserBrowser->setID( 0 );

        return TRUE;

    }

}

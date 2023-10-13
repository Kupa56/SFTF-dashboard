<?php


class TokenSessManager{

    public static function getSessInstanceToken($val){

        $isGeneratedKey = SessionManager::getValue("dashboardV3TokenGeneratedAjax","");
        if($isGeneratedKey == ""){
            ClientSession::setValue("webappV1TokenGeneratedApi",$val);
            return self::generateSessToken($val);
        }

        return self::getSessToken($isGeneratedKey);
    }

    public static function generateSessToken($val){
        $ctx = &get_instance();
        return $ctx->mUserBrowser->setToken($val);
    }

    public static function getSessToken($val){
        $ctx = &get_instance();
        return $ctx->mUserBrowser->getToken($val);
    }

    public static function clearSessToken($val){
        $ctx = &get_instance();
        return $ctx->mUserBrowser->cleanToken($val);
    }

}
class TokenUserManager{


    public static function isValidTokenAndUser($token,$user_id){

        $context = &get_instance();
        $context->db->where("id",$token);
        $context->db->where("uid",$user_id);
        $context->db->where("type","tokenUserAuth");
        $get = $context->db->get('token',1);
        $get = $get->result_array();

        if(isset($get[0]))
            return $get[0];

        return NULL;
    }


    public static function isValid($token){

        $context = &get_instance();
        $context->db->where("id",$token);
        $context->db->where("type","tokenUserAuth");
        $get = $context->db->get('token',1);
        $get = $get->result_array();

        if(isset($get[0]))
            return $get[0];

        return NULL;
    }

    public static function generate($user_id){

        $result = self::getValidation($user_id);

        if($result != NULL){
            TokenSetting::clear();
        }


        return TokenSetting::createToken($user_id,"tokenUserAuth");

    }


    public static function getValidation($user_id){

        $context = &get_instance();
        $context->db->where("uid",$user_id);
        $context->db->where("type","tokenUserAuth");
        $get = $context->db->get('token',1);
        $get = $get->result_array();

        if(isset($get[0]))
            return $get[0];

        return NULL;
    }


}

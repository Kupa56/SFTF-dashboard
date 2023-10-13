<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 1/15/19
 * Time: 12:32
 */

class SessionManager{

    public static function isLogged(){
        $context = &get_instance();
        if($context->mUserBrowser->isLogged())
            return TRUE;

        return FALSE;
    }


    public static function getData($key){
        $context = &get_instance();
        return $context->mUserBrowser->getData($key);
    }

    public static function refresh($user_id){
        $context = &get_instance();
        return $context->mUserBrowser->refreshData($user_id);
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

    public static function getUserImage(){


        //prepare image data
        $userImage = self::getData("images");

        if (is_string($userImage) && $userImage != "") {
            $userImage = json_decode($userImage, JSON_OBJECT_AS_ARRAY);
        }


        $dc = $userImage;
        if (!is_array($userImage) and $dc != "") {
            $userImage = array();
            $userImage[] = $dc;
        }

        //get image url
        $imageUrl = adminAssets("images/place-holder-160.png");

        if (!empty($userImage) && isset($userImage[0]) && is_string($userImage[0])) {

            $userImage = ImageManagerUtils::getImage($userImage[0]);

            if (!empty($userImage))
                $imageUrl = $userImage;

        } elseif (isset($userImage[0]) && is_array($userImage[0])) {

            if (!empty($userImage[0]))
                $imageUrl = $userImage[0]['200_200']['url'];

        }

        return $imageUrl;

    }

}

function loadPhoneCodes(){
    return json_decode(file_get_contents(APPPATH.'/modules/user/views/assets/json/CountryCodes.json'),JSON_OBJECT_AS_ARRAY);
}

class UserSettingSubscribe{

    private static $user_settings = array();

    public static function setGroup($module,$data=array()){

        foreach ($data as $value){
            self::set($module,$value);
        }

    }

    /*
     * $this->db->where("user_id",$user_id);
                    $this->db->update("setting",array(
                        "nbr_stores" => ($nbr_store-1)
                    ));
     */

    //get user subscribe setting from current session
    public static function getUSSetting($key){

        $context = &get_instance();
        return $context->mUserBrowser->getData($key);

    }

    //get user subscribe setting from databse session
    public static function getUDBSetting($user_id,$key){

        $context = &get_instance();
        $user = $context->mUserModel->getUserData($user_id);

        if(isset($user[$key])){
            return $user[$key];
        }

        return NULL;
    }



    //get user subscribe setting from databse session
    public static function refreshUSetting($user_id,$key,$value=0){

        $context = &get_instance();

        $context->db->where("user_id",$user_id);
        $context->db->update("user_subscribe_setting",array(
            $key => $value
        ));

        return NULL;
    }


    /**
     * @param $module
     * @param $key
     */
    public static function unsetSetting($module, $key){

        $context = &get_instance();

        if ($context->db->field_exists($key, 'user_subscribe_setting')){

            if(isset(self::$user_settings[$module][$key])){
                unset(self::$user_settings[$module][$key]);
            }
            //remove it from database
            $context->dbforge->drop_column('user_subscribe_setting', $key);
        }

    }

    public static function set($module='',$data=array()){

        if(!ModulesChecker::isRegistred($module)) {
            echo "The module that you provide doesn't exists \"".$module."\"";
            exit();
        }elseif(!isset($data['field_name']) OR !isset($data['field_type'])) {
            echo "You've set invalid user Setting";
            echo "<pre>";
            print_r($data);
            exit();
        }else if(!preg_match("#^([a-zA-Z0-9\_]+)$#i",$data['field_name'])){
            echo "You've set invalid field name \"".$data['field_name']."\"";
            exit();
        }else if(!preg_match("#^([A-Z0-9\_]+)$#i",$data['config_key'])){
            echo "You've set invalid config key \"".$data['config_key']."\", it should to be like \"KEY_CONFIG\"";
            exit();
        }else{

            $types = array(
                UserSettingSubscribeTypes::INT,
                UserSettingSubscribeTypes::BOOLEAN,
                UserSettingSubscribeTypes::DOUBLE,
                UserSettingSubscribeTypes::TEXT,
                UserSettingSubscribeTypes::VARCHAR
            );

            if(!in_array($data['field_type'],$types)){
                echo "You've select invalid field type name for \"".$data['field_name']."\"";
                exit();
            }
        }

        //validate default value
        if(isset($data['field_default_value'])){
            if($data['field_type']==UserSettingSubscribeTypes::VARCHAR)
                $data['field_default_value'] = (string)$data['field_default_value'];
            else if($data['field_type']==UserSettingSubscribeTypes::INT)
                $data['field_default_value'] = intval($data['field_default_value']);
            else if($data['field_type']==UserSettingSubscribeTypes::DOUBLE)
                $data['field_default_value'] = doubleval($data['field_default_value']);
            else if($data['field_type']==UserSettingSubscribeTypes::DOUBLE){
                if($data['field_default_value'])
                    $data['field_default_value'] = true;
                else
                    $data['field_default_value'] = true;
            }
        }

        //init default value (if needed)
        if(!isset($data['field_default_value'])){
            if($data['field_type']==UserSettingSubscribeTypes::VARCHAR)
                $data['field_default_value'] = "";
            else if($data['field_type']==UserSettingSubscribeTypes::INT)
                $data['field_default_value'] = 0;
            else if($data['field_type']==UserSettingSubscribeTypes::DOUBLE)
                $data['field_default_value'] = 0;
            else if($data['field_type']==UserSettingSubscribeTypes::BOOLEAN)
                $data['field_default_value'] = false;
        }


        if(!isset($data['_display'])){
            $data['_display'] = TRUE;
        }

        if(!isset($data['field_label'])){
            $data['field_label'] = $data['field_name'];
        }

        if(!isset($data['field_sub_label'])){
            $data['field_sub_label'] = "";
        }

        if(!isset($data['field_placeholder'])){
            $data['field_placeholder'] = "";
        }

        if(!isset($data['field_comment'])){
            $data['field_comment'] = "";
        }

        //check if the field already exists in the database the add it
        if(!isset(self::$user_settings[$module][$data['field_name']])){

            $context = &get_instance();
            if (!$context->db->field_exists($data['field_name'], 'user_subscribe_setting'))
            {
                $fields = array(
                    $data['field_name']  => array('type' => $data['field_type'], 'after' => 'user_id','default' => $data['field_default_value']),
                );
                // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
                $context->dbforge->add_column('user_subscribe_setting', $fields);

            }

            $context->load->module('setting');
            if(!defined($data['config_key'])){
                $context->mConfigModel->save($data['config_key'],$data['field_default_value']);
            }

            //save into variable to use
            $data['module'] = $module;
            self::$user_settings[$module][$data['field_name']] = $data;

        }


    }

    public static function loadModules(){

        return self::$user_settings;

    }

    public static function load(){

        $data = array();

        foreach (self::$user_settings as $moduleBlock){
            foreach ($moduleBlock as $key => $setting){
                $data[$key] = $setting;
            }
        }

        return $data;

    }

    public static function getFields(){

        $data = array();

        foreach (self::$user_settings as $moduleBlock){
            foreach ($moduleBlock as $key => $setting){
                if($setting['_display']==1)
                    $data[] = $setting;
            }
        }

        return $data;

    }

    public static function parseToType($value,$type){
        if($type==UserSettingSubscribeTypes::VARCHAR)
            $value = (string)$value;
        else if($type==UserSettingSubscribeTypes::INT)
            $value = intval($value);
        else if($type==UserSettingSubscribeTypes::DOUBLE)
            $value = doubleval($value);
        else if($type==UserSettingSubscribeTypes::BOOLEAN){
            if($value){
                $value = 1;
            }else{
                $value = 0;
            }
        }

        return $value;
    }


}


class UserSettingSubscribeTypes{

    const INT = "INT";
    const VARCHAR = "VARCHAR(11)";
    const DOUBLE = "DOUBLE";
    const BOOLEAN = "BOOLEAN";
    const TEXT = "TEXT";

}
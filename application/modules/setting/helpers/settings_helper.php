<?php

class TimeZoneManager{

    public static function getTimeZone(){

        $context = &get_instance();
        $u_time_zone = $context->mConfigModel->get("TIME_ZONE");

        if(SessionManager::isLogged()){
            $u_time_zone = SessionManager::getData('user_timezone');
        }

        return $u_time_zone;
    }
}


class ApiUpdater{

    public static function retrieveApiApps(){
        $apps = json_decode(ConfigManager::getValue("API_MAP"),JSON_OBJECT_AS_ARRAY);

        foreach ($apps as $key => $value){
            $item = explode("@",$value);
            $apps[$key] = array(
                "itemId" => isset($item[0])?$item[0]:$value,
                "itemLabel" => (isset($item[1]) && $item[1] != "")?$item[1]:"DefaultApp#1",
                "itemPID" => ConfigManager::getValue("EVT_PID_".md5($key)),
                "itemAPI_Value" => ConfigManager::getValue("EVT_API_".md5($key)),
            );
        }


        return $apps;
    }

    private static function load($pid){

        $api_endpoint = "https://apiv2.webox360.win/api/api3/retrieveAppLicenses";
        $post_data = array(
            "pid" => $pid,
            "item" => INSTALL_PROJECT_ID.".".APP_VERSION,
        );
        $response = MyCurl::run($api_endpoint, $post_data);
        $response = json_decode($response, JSON_OBJECT_AS_ARRAY);
        return $response;
    }

}


class ConfigManager{


    public static function getValue($key=NULL){
        $context = &get_instance();
        return $context->mConfigModel->get($key);
    }

    public static function defined($key=NULL){
        $context = &get_instance();
        return $context->mConfigModel->defined($key);
    }

    public static function setValue($key=NULL,$value=NULL,$init=FALSE){
        $context = &get_instance();

        if($init == TRUE && defined($key))
            return TRUE;

        return $context->mConfigModel->save($key,$value);
    }


    public static function isIos(){
        if(ConfigManager::defined("API_c67fe8c691a125e045fe86b74772ffb5"))
            return TRUE;
        return FALSE;
    }

    public static function isAndroid(){
        if(ConfigManager::defined("API_3f5dcd88d4ef86006d322ec4a0450229"))
            return TRUE;
        return FALSE;
    }

    public static function isWeb(){
        if(ConfigManager::defined("API_7af70ffbe05df578304f4358d84b1cad"))
            return TRUE;
        return FALSE;
    }


    public static function isAndriodNiOS(){
        if(ConfigManager::defined("API_c67fe8c691a125e045fe86b74772ffb5")
            &&
            ConfigManager::defined("API_3f5dcd88d4ef86006d322ec4a0450229"))
            return TRUE;
        return FALSE;
    }


}


class SettingViewer{

    private static $component = array();
    private static $module_order = array();



    public static function register($module,$path="",$data=array())
    {

        if(!isset(self::$component[$module])){
            self::$component[$module] = array();
            self::$component[$module][] = array(
                'path' => $path,
                'config' => $data,
            );
        }else{
            self::$component[$module][] = array(
                'path' => $path,
                'config' => $data,
            );
        }

    }

    public static function loadComponent()
    {


        $component = array();

        /*
         * Start
         */


        foreach (self::$component as $key => $v2){
            if($key=="setting"){

                $component[] = array(
                    'module' => $key,
                    'blocks' => self::$component[$key],
                );

                unset(self::$component[$key]);
                break;
            }
        }


        //re-order block depend on its saved order
        //
        $ordered_modules = FModuleLoader::getModules();

        foreach ($ordered_modules as $k => $value){

            if(!isset($component[$value['module_name']])
                && isset(self::$component[$value['module_name']])){

                $component[$k] = array(
                    'module' => $value['module_name'],
                    'blocks' => self::$component[$value['module_name']],
                );

            }

        }

        foreach (self::$component as $key => $v1){

            $m_exist = FALSE;

            foreach ($component as $v2){

                if($v2['module'] == $key){
                    $m_exist = TRUE;
                    break;
                }
            }

            if($m_exist == FALSE){

                $last_order = key(array_slice($component, -1, 1, true));
                $last_order++;

                $component[$last_order] = array(
                    'module' => $key,
                    'blocks' => self::$component[$key],
                );
            }

        }



        return $component;

    }

    public static function getRealPath($module,$path){



    }


}


class TokenSetting{

    const GLOBAL_USE_TOKEN = -2;

    public static function generateToken($type="unspecified",$content=""){
        return self::createToken(self::GLOBAL_USE_TOKEN,$type,$content);
    }

    public static function createToken($uid=0,$type="unspecified",$content=""){

        $context = &get_instance();
        $token = md5(time() . rand(0, 999));

        //create new one
        $context->db->insert('token', array(
            "id" => $token,
            "uid" => $uid,
            "type" => $type,
            "content" => $content,
            "created_at" => date("Y-m-d H:i:s", time())
        ));

        return $token;
    }

    public static function re_create_token($uid=0,$type="unspecified"){

        $context = &get_instance();
        $token = md5(time() . rand(0, 999));

        $context->db->insert('token', array(
            "id" => $token,
            "uid" => $uid,
            "type" => $type,
            "created_at" => date("Y-m-d", time())
        ));

        return $token;
    }


    public static function getValid($uid=0,$type="unspecified",$token=""){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $context->db->where("id",$token);
        $get = $context->db->get('token',1);
        $get = $get->result();
        if(isset($get[0]))
            return $get[0];

        return NULL;
    }

    public static function get_by_uid($uid=0,$type="unspecified"){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $get = $context->db->get('token',1);
        $get = $get->result();
        if(isset($get[0]))
            return $get[0];

        return NULL;
    }

    public static function getTokensByUserID($uid=0,$type="unspecified"){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $get = $context->db->get('token',1);
        $get = $get->result();

        return $get;
    }


    public static function get_by_token($token="",$type="unspecified"){

        $context = &get_instance();
        $context->db->where("id",$token);

        if($type != NULL && $type != ""){
            $context->db->where("type",$type);
        }

        $get = $context->db->get('token',1);
        $get = $get->result();

        if(isset($get[0]))
            return $get[0];

        return NULL;
    }


    public static function isValid($uid=0,$type="unspecified",$token=""){

        $context = &get_instance();

        $context->db->where("uid",$uid);
        $context->db->where("type",$type);
        $context->db->where("id",$token);
        $count = $context->db->count_all_results('token');
        if($count==1)
            return TRUE;

        return FALSE;
    }


    public static function clear($token=""){

        $context = &get_instance();
        $context->db->where("id",$token);
        $context->db->delete('token');

    }

    public static function clearAll_byUserID($uid){

        $context = &get_instance();
        $context->db->where("uid",$uid);
        $context->db->delete('token');

    }


}



if( !function_exists("input_get") ){
    function input_get($key){
        $ctx = &get_instance();
        return $ctx->input->get($key);
    }
}



function move_unzip($downloaded_file, $destination)
{
    if (!file_exists($downloaded_file))
        return;

    $zipArchive = new ZipArchive();
    if ($zipArchive->open($downloaded_file) !== TRUE) {
        die ("An error occurred creating your ZIP file $downloaded_file.");
    }

    $zipArchive->extractTo($destination);
    $zipArchive->close();
    return TRUE;
}

function is_dir_empty($dir) {
    if (!is_readable($dir)) return null;
    return (count(scandir($dir)) == 2);
}

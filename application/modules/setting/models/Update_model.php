<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Update_model extends CI_Model
{


    public $loadedFiles = "";


    public function __construct()
    {
        parent::__construct();
    }

    public function saveSettingDB($params=array()){

        foreach ($params as $key => $value){

            $this->db->where('_key',$key);
            $c = $this->db->count_all_results('app_config');

            $type = 'N/A';

            if(is_array($value)){
                $value = json_encode($value,JSON_FORCE_OBJECT);
                $type = 'json';
            }else if(is_numeric($value)){

                $value = Text::strToNumber($value);

                if(is_float($value)){
                    $value = floatval($value);
                    $type = 'float';
                }else if(is_integer($value)){
                    $value = intval($value);
                    $type = 'int';
                }else if(is_double('double')){
                    $value = doubleval($value);
                    $type = 'double';
                }

            }else if(is_string($value)){
                $type = 'string';
            }

            if($c==0){

                $this->db->insert('app_config',array(
                    '_key' => $key,
                    'value' => $value,
                    '_type' => $type,
                    'is_verified' => 0,
                    '_version' => APP_VERSION
                ));
            }

        }


    }


    public function checkVersion($version,$code,$type=""){

        for($i=1;$i<=10;$i++){
            if($version==$code.".".$i.$type){
                return TRUE;
            }
        }

        return FALSE;
    }

    public function checkAndPutPID(){

        if(file_exists("config/".PARAMS_FILE.".json")){
            $path = "config/".PARAMS_FILE.".json";
            $params = url_get_content(Path::getPath(array($path)));
        }else{
            $path = "config/params.json";
            $params = url_get_content(Path::getPath(array($path)));
        }

        $params = json_decode($params,JSON_OBJECT_AS_ARRAY);

        if(isset($params['_APP_VERSION'])){ //check version of config files
            $app_v_json = ($params['_APP_VERSION']);
            $app_v_php = (APP_VERSION);

            if($app_v_json==$app_v_php)
                return array(Tags::SUCCESS=>1);
        }

        $id = trim(RequestInput::get("spid"));

        if($id=="")
            $id = trim(RequestInput::get("pid"));
        $id = base64_decode($id);
        if($id!=""){
            $params[PIDINDEX] = $id;
            //update file config
            @file_put_contents(Path::getPath(array($path)),json_encode($params,JSON_FORCE_OBJECT));
        }

    }


    public function getPid(){

        $pid = RequestInput::post("pid");

        return $pid;
    }


    public function verifyPurchaseId(){

        $pid = RequestInput::post("pid");
        $key = RequestInput::post("key");
        $item = RequestInput::post("item");

        $result = MyCurl::run("https://apiv2.webox360.win/api/api3/lvalidate",array(
            "item"           => $item."@".APP_VERSION,
            "pid"            => $pid,
            "email"          => ConfigManager::getValue("DEFAULT_EMAIL"),
            "uri"            => base_url(),
            "reqfile"        => 1
        ));


        $data = json_decode($result,JSON_OBJECT_AS_ARRAY);

        if(isset($data[Tags::SUCCESS]) and $data[Tags::SUCCESS]==1){

            ConfigManager::setValue("API_".md5($item),$data["api"]);
            ConfigManager::setValue("EVT_PID_".md5($item),$pid);

            if(isset($data['download_file_url'])){
                $this->downloadFile($data['download_file_url']);
            }

        }

        return $result;
    }

    private function downloadFile($url){

        //download files if needed
        $files = json_decode($url, JSON_OBJECT_AS_ARRAY);

        foreach ($files as $file) {
            $download_url = $file['download_url'];
            $extract_path = $file['install_path'];
            $filename = $file['file_name'];
            //download file
            if($extract_path==""
                OR (is_dir_empty($extract_path) OR !is_dir($extract_path))){
                $fileContent = file_get_contents($download_url);
                file_put_contents("uploads/".$filename, $fileContent);
                move_unzip("uploads/".$filename,$extract_path);
            }else if(!is_dir($extract_path."/".$filename)){
                $fileContent = file_get_contents($download_url);
                file_put_contents("uploads/".$filename, $fileContent);
                move_unzip("uploads/".$filename,$extract_path);
            }
        }

    }

    function parse($content = "", $args = array())
    {

        foreach ($args as $key => $value) {
            $content = preg_replace("#\{" . $key . "\}#", $value, $content);
        }
        return $content;

    }



}
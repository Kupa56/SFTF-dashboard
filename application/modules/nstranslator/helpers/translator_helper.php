<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 11/15/2017
 * Time: 23:00
 */

class Translate {

    /*
     * DEBUGGING MODE
     */
    public static $in_debugging_mode = FALSE;


    public static $_translate_data_array = array();

    /*
     *  $_translate_data_array = array(
     *      code => data list
     *  )
     */



    public static function init(){

        /*
         * JSON PATH
         */

        $default_language_path_json = Path::getPath(array('uploads','translates'));

        //create a default folder if needed
        if(!is_dir($default_language_path_json)){
            if(!is_dir(Path::getPath(array('uploads'))))
                @mkdir(Path::getPath(array('uploads')));

            @mkdir($default_language_path_json);
        }

        //get saved json data
        $default_language_path_json = Path::getPath(array('uploads','translates',self::getDefaultLang().'.json'));

        //check if the file already exists to avoid problems, we can regenerated
        // from yaml or getting the yaml data
        if(!file_exists($default_language_path_json)){

            //regenerate from yaml files
            $data = self::loadLanguageFromYml();
            $data_json = json_encode($data,JSON_FORCE_OBJECT);
            @file_put_contents($default_language_path_json,$data_json);

            self::$_translate_data_array = $data;

        }else{

            $language_cached = self::loadLanguageFromCache(self::getDefaultLangCode());
            $language_uncached = self::loadLanguageFromYml(self::getDefaultLangCode());
            self::$_translate_data_array = self::merge($language_uncached,$language_cached);

        }


    }

    public static function save($code,$data){

        $default_language_path_json = Path::getPath(array('uploads','translates',$code.'.json'));
        $data_json = json_encode($data,JSON_FORCE_OBJECT);
        @file_put_contents($default_language_path_json,$data_json);

    }


    public static function getData($code){

        $default_language_path_json = Path::getPath(array('uploads','translates',$code.'.json'));
        $default_language_path_yaml = Path::getPath(array('languages',$code.'.yml'));

        return array(
            'cache' => url_get_content($default_language_path_json),
            'origin' => url_get_content($default_language_path_yaml),
        );

    }


    public static function changeSessionLang($lang="en"){
        $context =& get_instance();
        $context->load->library('session');
        $context->session->set_userdata("lang",$lang);
        self::getDefaultLangData();
    }

    public static function getLangsCodes(){
        $json = self::loadJSONs();
        $yml = self::loadYmls();

        foreach ($yml as $key => $value){
            if(isset($json[$key]))
                $yml[$key] = $json[$key];
        }

        return $yml;
    }

    public static function test(){
        if(empty(self::$_translate_data_array)){
            self::getDefaultLangData();
        }

        foreach (self::$_translate_data_array as $key => $value){
            if(is_string($value))
                echo $key.": ".$value.'<br>';
        }
    }

    public static function getDir(){

        $data = self::getDefaultLangData();

        if(isset($data['config'])
            AND isset($data['config']['dir'])){
            $data['config']['dir'] = strtolower($data['config']['dir']);
            return $data['config']['dir'];
        }

        return "ltr";
    }

    public static function sprint($msgId="",$default=""){


        $msgId = textClear($msgId);

        if($default!="")
            $default = textClear($default);

        if(empty(self::$_translate_data_array)){
            self::getDefaultLangData();
        }

        $data = self::$_translate_data_array;

        if(isset($data[$msgId])){

            if(empty(self::$_translate_data_array)){
                self::getDefaultLangData();
            }

            if($data[$msgId]=="" && $default!="")
                return $default;
            else if($data[$msgId]=="" && $default=="")
                return $msgId;
            else
                return $data[$msgId];

        }else{

            /*
            * SAVE UNTRANSLATED WORDS
            */
            self::registerNewWord($msgId,$default);
            //END

            if($default!="")
                return $default;
            else
                return ($msgId);

        }


    }

    public static function sprintf($msgId="",$args=array(),$default=""){

        if(empty(self::$_translate_data_array)){
            self::getDefaultLangData();
        }

        $data = self::$_translate_data_array;

        if(isset($data[$msgId])){
            if(empty($args))
                return $data[$msgId];
            else{

                /*
                 * SAVE UNTRANSLATED WORDS
                 */
                self::registerNewWord($msgId,$default);
                //END

                return vsprintf($data[$msgId],$args);
            }

        }

        else
            return vsprintf($msgId,$args);




    }

    public static function registerInAll($key, $value,$code){

        //add it in all
        $language_data = self::loadLanguageFromCache($code);

        if(!isset($language_data[$key]) OR $language_data[$key] == ""){
            $language_data[$key] = $value;
        }

        self::save($code,$language_data);
        unset($language_data);

        //register to the other languages
        $codes = Translate::getLangsCodes();
        foreach ($codes as $ncode => $ldata){
            if($ncode != $code){
                $language_data = self::loadLanguageFromCache($ncode);

                if(!isset($language_data[$key]) OR $language_data[$key] == "") {
                    $language_data[$key] = $value;
                }

                self::save($ncode,$language_data);
                unset($language_data);
            }
        }

    }

    public static function registerNewWord($key, $value){


        //load translate from cache
        if(empty(self::$_translate_data_array)){
            self::getDefaultLangData();
        }

        if(!isset(self::$_translate_data_array[$key])){

            //add it in current language
            if($value != "")
                self::$_translate_data_array[$key] = $value;
            else
                self::$_translate_data_array[$key] = $key;

            if(!isset(self::$_translate_data_array['config']) OR empty(self::$_translate_data_array['config'])){
                $data = self::loadLanguageFromYml(self::getDefaultLangCode());
                self::$_translate_data_array['config'] = array(
                    'name' => $data['config']['name'],
                    'version' => $data['config']['version'],
                    'dir' => (!isset($data['config']['dir']))?"ltr":$data['config']['dir'],
                );
            }

            self::save(self::getDefaultLangCode(),self::$_translate_data_array);

            //add it in all
            $codes = Translate::getLangsCodes();
            foreach ($codes as $code => $ldata){
                $language_data = self::loadLanguageFromCache($code);
                if(isset($language_data[$key]))
                    continue;
                $language_data[$key] = $value;
                self::save($code,$language_data);
                unset($language_data);
            }
        }

    }

    public static function getDefaultLang(){

        $context =& get_instance();
        $lngFromSession = $context->session->userdata('lang');
        if($lngFromSession!=""){
            return $lngFromSession;
        }else{
            return ConfigManager::getValue("DEFAULT_LANG");
        }

    }

    public static function getDefaultLangCode(){

        $context =& get_instance();
        $context->load->library("nstranslator/yaml");


        $lngFromSession = $context->session->userdata('lang');

        $default_language_code = "en";

        if($lngFromSession!=""){
            $default_language_code = $lngFromSession;
        }else if(defined("DEFAULT_LANG")){
            $default_language_code = DEFAULT_LANG;
        }


        return $default_language_code;

    }

    public static function getDefaultLangData(){
        $language_cached = self::loadLanguageFromCache(self::getDefaultLangCode());

        self::$_translate_data_array = self::clearLangData($language_cached);
        return self::$_translate_data_array;
    }

    private static function clearLangData($data=array()){

        foreach ($data as $key => $value){
            if(isset($data[$key]) && $key=="")
                unset($data[$key]);
        }

        return $data;
    }


    public static function loadLanguageFromYml($def=""){

        $context =& get_instance();
        $context->load->library("nstranslator/yaml");
        $lngFromSession = $context->session->userdata('lang');

        if($def!="" && preg_match("#[a-zA-Z]{2}#",$def)){
            $fileYaml = Path::getPath(array("languages",textClear($def).".yml"));
        }else if($lngFromSession!=""){
            $fileYaml = Path::getPath(array("languages",textClear($lngFromSession).".yml"));
        }else{
            $fileYaml = Path::getPath(array("languages",ConfigManager::getValue("DEFAULT_LANG").".yml"));
        }

        if(!file_exists($fileYaml)){
            $fileYaml = Path::getPath(array("languages",ConfigManager::getValue("DEFAULT_LANG").".yml"));
        }

        if(file_exists($fileYaml)){

            $context =& get_instance();
            //load yanl file
            $data = $context->yaml->load($fileYaml);

            return $data;
        }

        return  array();

    }

    public static function remove($code=""){

        $path_yml =  Path::getPath(array('languages',$code.'.yml'));
        $path_json =  Path::getPath(array('uploads','translates',$code.'.json'));

        @unlink($path_yml);
        @unlink($path_json);
    }

    public static function loadLanguageFromCache($def=""){

        $context =& get_instance();
        $context->load->library('nstranslator/yaml');
        $lngFromSession = $context->session->userdata('lang');

        if($def!="" && preg_match("#[a-zA-Z]{2}#",$def)){
            $fileJSON = Path::getPath(array("uploads","translates",textClear($def).".json"));
        }else if($lngFromSession!=""){
            $fileJSON = Path::getPath(array("uploads","translates",textClear($lngFromSession).".json"));
        }else{
            $fileJSON = Path::getPath(array("uploads","translates",ConfigManager::getValue("DEFAULT_LANG").".json"));
        }




        if(!file_exists($fileJSON)){
            //$fileJSON = Path::getPath(array("uploads","translates",DEFAULT_LANG.".json"));
            return array();
        }

        if(file_exists($fileJSON)){

            $json = url_get_content($fileJSON);
            return json_decode($json,JSON_OBJECT_AS_ARRAY);
        }

        return  array();

    }

    public static function regenerateJson($code,$data,$config=array()){

        $values = array();

        $cached = self::loadLanguageFromCache($code);
        $uncached = self::loadLanguageFromYml($code);

        if(isset($cached['config'])){
            unset($cached['config']);
        }

        if(isset($uncached['config'])){
            unset($uncached['config']);
        }


        $values = Translate::merge($uncached,$cached);

        if(is_string($data))
            $data = json_decode($data,JSON_OBJECT_AS_ARRAY);


        if(!empty($data))
            foreach ($data as $val){
                $values[$val['key']] = $val['value'];
            }

        $values['config'] = $config;
        $values['config']['last_update'] = date('Y-m-d H:i:s',time());


        self::save($code,$values);

        return TRUE;

    }


    public static function merge($array_new_keys, $array_new_values){

        $merged = array();


        //add new keys
        foreach ($array_new_keys as $key => $value){
            if(!isset($array_new_values[$key])){
                if(is_string($value) && $key!="")
                    $merged[$key] = $value;
            }
        }

        //fetch old values
        foreach ($array_new_values as $key => $value){
            if(is_string($value) && $key!="")
                $merged[$key] = $value;
        }


        return $merged;
    }

    private static function loadYmls(){

        //get instance of object
        $context =& get_instance();
        $context->load->library("nstranslator/yaml");

        $data_to_json = array();
        $path = Path::getPath(array("languages"));

        if(!is_dir($path)){
            mkdir($path);
        }

        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." ) {

                    //prepare path of files
                    $fileYaml = Path::addPath($path, array($entry));

                    //load yanl file
                    $data = $context->yaml->load($fileYaml);

                    //prepare config data for lang

                    if(isset($data['config'])
                        AND isset($data['config']['name'])
                        AND isset($data['config']['version'])){



                        $lng = preg_replace("#.yml#", "", $entry);

                        $data_to_json[$lng] = array(
                            "name"  => $data['config']['name'],
                            "version"  => $data['config']['version'],
                            "lang"     => $lng,
                            "dir"       => "ltr"
                        );


                        if(isset($data['config']['dir'])){
                            $data_to_json[$lng]['dir'] = $data['config']["dir"];
                        }



                        unset($data);

                    }


                }
            }


            closedir($handle);
        }

        return $data_to_json;
    }


    public static function loadJSONs(){

        //get instance of object
        $context =& get_instance();

        $data_to_json = array();
        $path = Path::getPath(array("uploads","translates"));

        if(!is_dir($path)){
            mkdir($path);
        }

        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." ) {

                    //prepare path of files
                    $fileJson = Path::addPath($path, array($entry));
                    //load yanl file
                    $data = @url_get_content($fileJson);
                    $data = json_decode($data,JSON_OBJECT_AS_ARRAY);

                    //prepare config data for lang

                    if(isset($data['config'])
                        AND isset($data['config']['name'])
                        AND isset($data['config']['version'])){

                        $lng = preg_replace("#.json#", "", $entry);


                        $data_to_json[$lng] = array(
                            "name"  => $data['config']['name'],
                            "version"  => $data['config']['version'],
                            "lang"     => $lng,
                            "dir"       => "ltr"
                        );


                        if(isset($data['config']['dir'])){
                            $data_to_json[$lng]['dir'] = $data['config']["dir"];
                        }



                        unset($data);

                    }


                }
            }


            closedir($handle);
        }

        return $data_to_json;
    }


    public static function parse($code,$fileYaml){

        //get instance of object
        $context =& get_instance();
         $context->load->library("nstranslator/yaml");
       


        $data_to_json = array();
        $path = Path::getPath(array("languages"));

        if(!is_dir($path)){
            mkdir($path);
        }


        //load yanl file
        $data = $context->yaml->load($fileYaml);

        //prepare config data for lang

        if(isset($data['config'])
            AND isset($data['config']['name'])
            AND isset($data['config']['version'])){

            $data_to_json = $data;

            if(isset($data['config']['dir'])){
                $data_to_json[$code]['dir'] = $data['config']["dir"];
            }


            unset($data);

        }

        return $data_to_json;
    }


    public static function updateLanguages($module){

        //path
        $path =  Path::getPath(array("application","modules",$module,"languages"));

        //get instance of object
        $context =& get_instance();
        $context->load->library("nstranslator/yaml");

        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." ) {

                    //prepare path of files
                    $fileYaml = Path::addPath($path, array($entry));

                    //load yanl file
                    $data = $context->yaml->load($fileYaml);
                    $code = preg_replace("#.yml#", "", $entry);

                    //register all words
                    foreach ($data as $k => $v){
                        self::registerInAll($k,$v,$code);
                    }

                }
            }


            closedir($handle);
        }

    }


}

if(!function_exists('_lang')){
    function _lang($str,$default=''){
        return Translate::sprint($str,$default);
    }
}

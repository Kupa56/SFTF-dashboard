<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of event_webservice
 *
 * @author idriss
 */
class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();



    }


    public function save(){

        //check if user have permission
        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $values = RequestInput::post('values');
        $code = RequestInput::post('code');


        $config_version = RequestInput::post('config_version');
        $config_direction = RequestInput::post('config_direction');
        $config_name = RequestInput::post('config_name');

        $config = array(
            'name' => $config_name,
            'version' => $config_version,
            'dir' => $config_direction,
        );


        if( Translate::regenerateJson($code,$values,$config)  ){
            echo json_encode(array(Tags::SUCCESS=>1));return;
        }


        echo json_encode(array(Tags::SUCCESS=>0));return;
    }


    public function add_new_language(){

        //check if user have permission
        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $name = trim(RequestInput::post('name'));
        $code = RequestInput::post('code');
        $direction = RequestInput::post('direction');


        if($name == "" OR $code == "" OR $direction == ""){
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }

        $config = array(
                'name' => $name,
                'version' => "1.0",
                'dir' => $direction
            );

        if( file_exists(Path::getPath(array('languages',$code.'.yml'))) ){

            $code = strtolower($code);

            //get default value
            $en_data = Translate::parse($code,Path::getPath(array('languages',$code.'.yml')));
            $en_data['config'] = $config;

        }else if(file_exists(   Path::getPath(array('languages','en.yml'))   )){

            $code = strtolower($code);
            //get default value
            $en_data = Translate::parse("en",Path::getPath(array('languages','en.yml')));
            $en_data['config'] = $config;

        }

            //create new language path yml
        $yaml = new Yaml();
        $yaml = $yaml->dump($en_data);

        $path_yml =  Path::getPath(array('languages',$code.'.yml'));
        @file_put_contents($path_yml,$yaml);

        //convert to json
        $en_data = json_encode($en_data,JSON_FORCE_OBJECT);

        //create new language path json
        $path_json =  Path::getPath(array('uploads','translates',$code.'.json'));
        @file_put_contents($path_json,$en_data);

        echo json_encode(array(Tags::SUCCESS=>1));return;
    }

    public function add_new_key(){

        //check if user have permission
        $this->enableDemoMode();

        if(!GroupAccess::isGranted('nstranslator',TRANSLATOR_MANAGE)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }


        $key =  trim(RequestInput::post('key'));
        $value = trim(RequestInput::post('value'));
        $code = RequestInput::post('code');


        if($key == "" OR $value == "" OR $code == ""){
            echo json_encode(array(Tags::SUCCESS=>0));return;
        }


        $path_json =  Path::getPath(array('uploads','translates',$code.'.json'));

        if(file_exists($path_json)){

            $data_lang = url_get_content($path_json);
            $data_lang = json_decode($data_lang,JSON_OBJECT_AS_ARRAY);

            $data_lang[$key] = $value;

            $data_lang = json_encode($data_lang,JSON_FORCE_OBJECT);
            file_put_contents($path_json,$data_lang);

            echo json_encode(array(Tags::SUCCESS=>1));return;

        }

        echo json_encode(array(Tags::SUCCESS=>0));return;
    }


}

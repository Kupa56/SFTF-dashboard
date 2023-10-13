<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getLanguages(){
        $result = array();
        $languages = Translate::getLangsCodes();
        foreach ($languages as $key => $lng) {
            $result[] = $key.":".$lng['name'];
        }

        echo json_encode(array(Tags::SUCCESS=>1, Tags::RESULT=> implode(",",$result)) );
    }

}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getLocations(){

        $country = trim(RequestInput::post('country'));
        $country = urlencode($country);

        $q = trim(RequestInput::post('q'));
        $q = urlencode($q);

        if(ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==1){
            $result = $this->location_picker_model->getLocalitiesHEREMaps($country,$q,"en");
        }else if(ConfigManager::getValue("LOCATION_PICKER_OP_PICKER")==2){
            $result = $this->location_picker_model->getLocalityAutocomplete($country,$q,"en");
        }else{
            $result = array();
        }

        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$result));return;

    }


}
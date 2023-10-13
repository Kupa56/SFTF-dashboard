<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    //load cities and locations
    public function loadLocations(){

        //load cities and countries
        $this->location_picker_model->setupCitiesListDB();
        echo json_encode(array(Tags::SUCCESS=>1));

    }

    public function getCitiesListAjax()
    {

        if(!SessionManager::isLogged())
            return array();

        $data = $this->location_picker_model->getCitiesList(RequestInput::get("q"));
        $result = array();

        foreach ($data as $object) {

            $o = array(
                'text' => $object['name'] . ", " . $object['country_name'],
                'id' => $object['id_city'],
                'longitude' => Text::output($object['longitude']),
                'latitude' => Text::output($object['latitude']),
                'city' => Text::output($object['name']),
                'country' => Text::output($object['country_name']),
            );
            $result['results'][] = $o;
        }

        echo json_encode($result, JSON_OBJECT_AS_ARRAY);
        return;
    }


    public function saveConfig()
    {

        $this->enableDemoMode();

        if (!GroupAccess::isGranted('setting', CHANGE_APP_SETTING)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $LOCATION_PICKER_HERE_MAPS_APP_ID = RequestInput::post("LOCATION_PICKER_HERE_MAPS_APP_ID");
        $LOCATION_PICKER_HERE_MAPS_APP_CODE = RequestInput::post("LOCATION_PICKER_HERE_MAPS_APP_CODE");
        $LOCATION_PICKER_OP_PICKER = RequestInput::post("LOCATION_PICKER_OP_PICKER");
        $MAPS_API_KEY = RequestInput::post("MAPS_API_KEY");
        $GOOGLE_PLACES_API_KEY = RequestInput::post("GOOGLE_PLACES_API_KEY");

        ConfigManager::setValue("LOCATION_PICKER_HERE_MAPS_APP_ID", $LOCATION_PICKER_HERE_MAPS_APP_ID);
        ConfigManager::setValue("LOCATION_PICKER_HERE_MAPS_APP_CODE", $LOCATION_PICKER_HERE_MAPS_APP_CODE);
        ConfigManager::setValue("LOCATION_PICKER_OP_PICKER", $LOCATION_PICKER_OP_PICKER);
        ConfigManager::setValue("MAPS_API_KEY", $MAPS_API_KEY);
        ConfigManager::setValue("GOOGLE_PLACES_API_KEY", $GOOGLE_PLACES_API_KEY);

        echo json_encode(array(Tags::SUCCESS => 1));
        return;

    }


    public function query()
    {

        $country = trim(RequestInput::get('country'));
        $country = urlencode($country);

        $q = trim(RequestInput::get('q'));
        $q = urlencode($q);

        if (ConfigManager::getValue("LOCATION_PICKER_OP_PICKER") == 1) {
            $result = $this->location_picker_model->getLocalitiesHEREMaps($country, $q, Translate::getDefaultLangCode());
        } else if (ConfigManager::getValue("LOCATION_PICKER_OP_PICKER") == 2) {
            $result = $this->location_picker_model->getGooglPlacesLocality($country, $q, Translate::getDefaultLangCode());
        } else {
            $result = array();
        }

        echo json_encode(array(Tags::SUCCESS => 1, Tags::RESULT => $result));
        return;

    }


    public function getAddresses()
    {

        $country = trim(RequestInput::get('country'));
        $country = urlencode($country);

        $q = trim(RequestInput::get('q'));
        $q = urlencode($q);

        if (ConfigManager::getValue("LOCATION_PICKER_OP_PICKER") == 1) {
            $result = $this->location_picker_model->getLocalitiesHEREMaps($country, $q, "en");
        } else if (ConfigManager::getValue("LOCATION_PICKER_OP_PICKER") == 2) {
            // $result = $this->location_picker_model->getGooglPlacesLocality($country,$q,"en");
            $result = $this->location_picker_model->getLocalityAutocomplete($country, $q, "en");
        } else {
            $result = array();
        }

        echo json_encode($result);
        return;

    }


    public function getAddressDetail()
    {

        $latitude = RequestInput::get('latitude');
        $longitude = RequestInput::get('longitude');

        $result = $this->location_picker_model->getAddressDetail($latitude, $longitude);
        echo json_encode($result);
        return;

    }


    private function echoError()
    {

    }

}
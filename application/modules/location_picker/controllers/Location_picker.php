<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Location_picker extends MAIN_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->init("location_picker");
    }

    public function onLoad()
    {
        define('WEB_MAP_PICKER','gmap');
        $this->load->helper("location_picker/location_picker");
        $this->load->model("location_picker/location_picker_model","location_picker_model");



    }

    public function onCommitted($isEnabled)
    {
        if(!$isEnabled)
            return;

        AdminTemplateManager::registerMenuSetting(
            'location_picker',
            "location_picker/menu_setting",
            10
        );

        AdminTemplateManager::addCssLibs(
            adminAssets("plugins/easyautocomplete/easy-autocomplete.min.css")
        );

        //check and alert
        if(false)
            $this->location_picker_model->checkLocations();

    }

    public function onInstall()
    {

        //update database field
        $this->location_picker_model->updateDBFields();

        return TRUE;
    }

    public function onUpgrade()
    {

        //update database field
        $this->location_picker_model->updateDBFields();

        return TRUE;
    }

    public function onEnable()
    {

        ConfigManager::setValue("LOCATION_PICKER_HERE_MAPS_APP_ID","",TRUE);
        ConfigManager::setValue("LOCATION_PICKER_HERE_MAPS_APP_CODE","",TRUE);
        ConfigManager::setValue("LOCATION_PICKER_OP_PICKER",2,TRUE);
        ConfigManager::setValue("MAPS_API_KEY","",TRUE);
        ConfigManager::setValue("GOOGLE_PLACES_API_KEY","",TRUE);

        return TRUE;
    }

    public function onUninstall()
    {
        return TRUE;
    }


}
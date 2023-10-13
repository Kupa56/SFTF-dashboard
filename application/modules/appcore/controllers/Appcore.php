<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */
class Appcore extends MX_Controller
{

    const AdminTemplate = "admin-v2";

    public function getRegistredModules()
    {
        return FModuleLoader::getRegistredModules();
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();

    }

    public function load_all_dependencies(){

        $this->load->helper("fmoduleloader");
        //load model
        $this->load->model("AppcoreModel", "mAppCore");

        //helpers
        $this->load->helper("appcore/moduleauthorizations");
        $this->load->helper("appcore/moduleschecker");
        $this->load->helper("appcore/modulesettings");
        $this->load->helper("appcore/modulemanager");
        $this->load->helper("appcore/actionsmanager");
        $this->load->module("utils");
        $this->utils->load_dependencies();

        FModuleLoader::init();
    }


    public function load_all_config(){

        if (!$this->db->table_exists('app_config')){
            return;
        }

        $config = $this->db->get('app_config');
        $config = $config->result_array();

        foreach ($config as $value) {

            if (!defined($value['_key'])) {
                if ($value['_type'] == 'int') {
                    define($value['_key'], intval($value['value']));
                } else if ($value['_type'] == 'float') {
                    define($value['_key'], floatval($value['value']));
                } else if ($value['_type'] == 'double') {
                    define($value['_key'], doubleval($value['value']));
                } else if ($value['_type'] == 'boolean') {
                    if ($value['value'] == 1) {
                        define($value['_key'], TRUE);
                    } else {
                        define($value['_key'], FALSE);
                    }
                } else {
                    define($value['_key'], $value['value']);
                }
            }

        }

    }


    public function getSettingsMenu()
    {
        //sort items
        $views = "";

        foreach (FModuleLoader::getRegistredModules() as $key => $item) {
            if (isset($item['setting_menu']) and $item['setting_menu'] != NULL) {
                $views .= $this->load->view($item['module_name'] . '/' . $item['setting_menu'], NULL, FALSE);
            }

        }

        return $views;
    }


    public function getMenusWithView()
    {
        //sort items
        $views = "";
        //loop
        foreach (FModuleLoader::getRegistredModules() as $key => $item) {
            if (isset($item['menu']) and $item['menu'] != NULL)
                $views .= $this->load->view($item['module_name'] . '/' . $item['menu'], NULL, FALSE);
        }

        return $views;
    }



    public function getModules()
    {
        return $this->mAppCore->getModules();
    }

    public function enableModule($moduleName, $app_id, $bol)
    {
        return $this->mAppCore->enableModule($moduleName, $app_id, $bol);
    }


    //enabled for all apps by SaaS
    public function isEnabled($moduleName)
    {
        return $this->mAppCore->isEnabled($moduleName);
    }


    /*
     * CALL BACK REGISTER
     */
}

/* End of file AppcoreDB.php */
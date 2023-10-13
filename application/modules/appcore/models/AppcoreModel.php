<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class AppcoreModel extends CI_Model
{

    private static $saas_modules;
    private static $app_modules;



    public function __construct()
    {
        parent::__construct();
        $this->createTable();
    }


    public function refreshModules($module){

        $moduleName = $module['module_name'];

        $this->db->where("module_name",$moduleName);
        $modules = $this->db->get("modules");
        $modules = $modules->result();

        if(count($modules)==0){

            if(intval($module['version_code']) == 0 ){
                throw new Exception('Module "'.$module['module_name'].'" is not valid or config is not valid');
            }

            $m['version_code'] =$module['version_code'];
            $m['version_name'] =$module['version_name'];
            $m['module_name'] =$module['module_name'];
            $m['_order']        =$module['order'];
            $m['updated_at'] = date("Y-m-d",time());
            $m['created_at'] = date("Y-m-d",time());
            $m['_enabled'] = 0;


            $this->db->insert("modules",$m);

        }

        FModuleLoader::onLoad($moduleName);
        FModuleLoader::onRegistered($moduleName);
        FModuleLoader::onCommitted($moduleName);

    }


    public function getModules(){

        $modules = array();

        $result = $this->db->get("modules");
        $result = $result->result();

        foreach ($result as $value){
            $modules[$value->module_name]['root_enabled'] = boolval ($value->_enabled);
            $modules[$value->module_name]['app_enabled'] = boolval ($value->_enabled);
        }

        return $modules;
    }


    public function startupModules(){

        $this->db->update("modules",array(
            "_enabled"  =>1
        ));

    }


    //db forge
    public function createTable()
    {


        if ($this->db->table_exists('modules') )
            return;

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'module_name' => array(
                'type' => 'VARCHAR(60)'
            ),
            'version_code' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'version_name' => array(
                'type' => 'VARCHAR(60)',
                'default' => NULL
            ),
            '_enabled' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            '_order' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('module_name', TRUE);
        $this->dbforge->create_table('modules', TRUE, $attributes);

    }




}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Modules_manager_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();

    }

    public function install_internal_path($path,$destination,$dir=""){

        if(file_exists($path) && file_exists($destination)){

            $zipArchive = new ZipArchive();
            $result = $zipArchive->open($path);


            if ($result === TRUE) {

                if($zipArchive->numFiles>0){
                    $name =  $zipArchive->getNameIndex(0);
                    if(preg_match("#\/#",$name)){
                        $name = rtrim($name,'/');


                        /*if(file_exists("application/modules/".$name)){
                            return array(Tags::SUCCESS=>0,Tags::ERRORS => array(
                                "err"=> Translate::sprint("Already exist with the same name!")
                            ));
                        }*/

                    }
                }

                $zipArchive ->extractTo($destination);
                $zipArchive ->close();

                if($dir != "")
                    FileManager::_removeDir($dir);

                //check validate of module
                if(isset($name)){

                    $path = "application/modules/".$name.'/config/'.$name.'.json';
                    $path_main_controller = "application/modules/".$name.'/controllers/'.ucfirst($name).'.php';


                    if(file_exists($path) and file_exists($path_main_controller)){

                        $config_file_json = url_get_content($path);
                        $config_file_json = json_decode($config_file_json,JSON_OBJECT_AS_ARRAY);

                        if(!isset($config_file_json['version_code'])
                            or !isset($config_file_json['version_name'])){
                            return array(Tags::SUCCESS=>0,Tags::ERRORS => array(
                                "err"=> Translate::sprint("Module config is not valid! (#019)")
                            ));
                        }

                    }else{
                        return array(Tags::SUCCESS=>0,Tags::ERRORS => array(
                            "err"=> Translate::sprint("Module content is not valid! (#018)")
                        ));
                    }


                }


                return array(Tags::SUCCESS=>1);
                // Do something else on success
            } else {
                // Do something on error
            }
        }


        return array(Tags::SUCCESS=>0);

    }

    public function install($module_id){

        if(ModulesChecker::isRegistred($module_id)){

            $this->db->where('module_name',$module_id);
            $this->db->where('_installed',0);
            $count = $this->db->count_all_results('modules');

            if($count>0){

                //execute callback
                $install_result = $this->{$module_id}->onInstall();

                if(is_array($install_result) && $install_result[Tags::SUCCESS]<=0)
                    return $install_result;


                if($install_result == FALSE)
                    return array(Tags::SUCCESS=>0);

                $this->db->where('module_name',$module_id);
                $this->db->where('_installed',0);
                $this->db->update('modules',array(
                    '_installed' => 1
                ));

                //action to use in all modules
                ActionsManager::add_action("modules_manager",MODULES_MANAGER_ACTIONS_ON_INSTALL,array(
                    'module_id' => $module_id
                ));


                return array(Tags::SUCCESS=>1);
            }

        }

        return array(Tags::SUCCESS=>2);//skip
    }

    public function upgrade($module_id){

        if(ModulesChecker::isRegistred($module_id)){

            $module_data = FModuleLoader::getModuleDetail($module_id);

            $this->db->where('module_name',$module_id);
            $this->db->where('_installed',1);
            $this->db->where('version_code <',$module_data['version_code']);

            $count = $this->db->count_all_results('modules');

            if($count>0){

                //execute callback
                $upgrade_result = $this->{$module_id}->onUpgrade();

                if(is_array($upgrade_result) && $upgrade_result[Tags::SUCCESS]==0)
                    return $upgrade_result;


                if(is_array($upgrade_result))
                    return $upgrade_result;


                if($upgrade_result == FALSE)
                    return array(Tags::SUCCESS=>0);


                $this->db->where('module_name',$module_id);
                $this->db->where('_installed',1);
                $this->db->where('version_code <',$module_data['version_code']);

                $this->db->update('modules',array(
                    'version_code' => $module_data['version_code']
                ));

                //action to use in all modules
                ActionsManager::add_action("modules_manager",MODULES_MANAGER_ACTIONS_ON_UPGRADE,array(
                    'module_id' => $module_id
                ));


                return array(Tags::SUCCESS=>1);

            }

        }

        return array(Tags::SUCCESS=>0);
    }


    public function enable($module_id){

        if(ModulesChecker::isRegistred($module_id)){

            $module_data = FModuleLoader::getModuleDetail($module_id);




            /*
            * CHECK REQUIMENTS
            */


            if(isset($module_data['requirements']) AND !empty($module_data['requirements'])){
                foreach ($module_data['requirements'] as $req_module){

                    $req_module = explode(":",$req_module);
                    $module_name = $req_module[0];


                    if(!ModulesChecker::isRegistred($module_name)){
                        return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>
                            Translate::sprintf("Please make sure if this module (%s) is installed",array($module_name))
                        ));
                    }


                    if(!ModulesChecker::isEnabled($module_name)){
                        return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>
                            Translate::sprintf("Please make sure if this module (%s) is enabled",array($module_name))
                        ));
                    }

                    $module_data = FModuleLoader::getModuleDetail($module_name);
                    $required_version = $req_module[1];

                    if(!ModuleManager::checkCompatibility($required_version,$module_data['version_name'])){
                        return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>
                            Translate::sprintf("This module is not compatible with (%s)",array($module_name))
                        ));
                    }

                }
            }

            /*
           * END CHECK REQUIMENTS
           */

            $this->db->where('module_name',$module_id);
            $this->db->where('_enabled',0);
            $this->db->where('_installed',1);

            $count = $this->db->count_all_results('modules');

            if($count>0){

                $this->db->where('module_name',$module_id);
                $this->db->where('_enabled',0);

                $this->db->update('modules',array(
                    '_enabled' => 1
                ));

                //execute callback
                $this->{$module_id}->onEnable();

                //grant the permission to the admin


               /* if(ModulesChecker::isEnabled('user')
                    && $this->mUserBrowser->isLogged()){
                    $this->updatePermission($module_id);
                }*/


                //action to use in all modules
                ActionsManager::add_action("modules_manager",MODULES_MANAGER_ACTIONS_ON_ENABLE,array(
                    'module_id' => $module_id
                ));


                return array(Tags::SUCCESS=>1);
            }

        }

        return array(Tags::SUCCESS=>0);
    }

    public function disable($module_id){

        if(ModulesChecker::isRegistred($module_id)){


            $module_data = FModuleLoader::getModuleDetail($module_id);

            if($module_data['required']==1)
                return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>Translate::sprint("This module is required")));


            /*
             * CHECK REQUIMENTS
             */

            $modules = ModuleManager::getLoadedModules();

            //load all existing modules
            foreach ($modules as $value){
                $mdl_name = $value['module_name'];
                $mdl_data = FModuleLoader::getModuleDetail($mdl_name);

                //check if there is any requirement with this module in any other module
                if(isset($mdl_data['requirements']) )
                foreach ($mdl_data['requirements'] as $req_module) {

                    $req_module = explode(":", $req_module);
                    $module_name = $req_module[0];

                    if($module_name == $module_id && ModulesChecker::isEnabled($mdl_name)){
                        return array(Tags::SUCCESS => 0, Tags::ERRORS => array("err" =>
                            Translate::sprintf("Some enabled modules are using \"%s\" module, please make sure this module \"%s\" is disabled then try again ",array($module_id,$mdl_name))
                        ));
                    }

                }

            }

            /*
            * END CHECK REQUIMENTS
            */

            $this->db->where('module_name',$module_id);
            $this->db->where('_enabled',1);
            $this->db->where('_installed',1);

            $count = $this->db->count_all_results('modules');

            if($count>0){

                $this->db->where('module_name',$module_id);
                $this->db->where('_enabled',1);

                $this->db->update('modules',array(
                    '_enabled' => 0
                ));

                //execute callback
                $result = $this->{$module_id}->onDisable();

                //action to use in all modules
                ActionsManager::add_action("modules_manager",MODULES_MANAGER_ACTIONS_ON_DISABLE,array(
                    'module_id' => $module_id
                ));

                return array(Tags::SUCCESS=>1);
            }

        }

        return array(Tags::SUCCESS=>0);
    }


    public function uninstall($module_id){

        if(ModulesChecker::isRegistred($module_id)){

            $module_data = FModuleLoader::getModuleDetail($module_id);

            if($module_data['required']==1)
                return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>Translate::sprint("This module is required")));


            $this->db->where('module_name',$module_id);
            $this->db->where('_installed',1);
            $this->db->where('_enabled',0);

            $count = $this->db->count_all_results('modules');

            if($count>0){

                $this->db->where('module_name',$module_id);
                $this->db->where('_installed',1);

                $this->db->update('modules',array(
                    '_installed' => 0
                ));

                //execute callback
                $this->{$module_id}->onUninstall();

                $module_path = Path::getPath(array('application','modules',$module_id));
                delTree($module_path);


                $this->db->where('module_name',$module_id);
                $this->db->delete('modules');

                return array(Tags::SUCCESS=>1);
            }

        }

        return array(Tags::SUCCESS=>0);
    }

    public function updatePermission($module_name){

        //$actions = GroupAccess::getModuleActions();

        $this->db->where('id',$this->mUserBrowser->getData('grp_access_id'));
        $grp = $this->db->get('group_access');
        $grp = $grp->result_array();

        if(count($grp)>0){
            $permission = $grp[0]['permissions'];
            $permission = json_decode($permission,JSON_OBJECT_AS_ARRAY);

            if(isset($permission[$module_name])){
                $module_actions = $permission[$module_name];
                foreach ($module_actions as $key => $mp){
                    $module_actions[$key] = 1;
                }
                $permission[$module_name] = $module_actions;
            }

            $permission = json_encode($permission,JSON_FORCE_OBJECT);

            $this->db->where('id',$this->mUserBrowser->getData('grp_access_id'));
            $this->db->update('group_access',array(
                'permissions' => $permission
            ));

        }

    }

    public function updateFields(){

        $this->load->dbforge();

        if (!$this->db->field_exists('_installed', 'modules'))
        {
            $this->load->dbforge();
            $fields = array(
                '_installed'  => array('type' => 'INT', 'after' => '_enabled','default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('modules', $fields);
        }

    }



}
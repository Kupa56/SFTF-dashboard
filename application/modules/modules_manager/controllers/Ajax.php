<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */

class Ajax extends AJAX_Controller {

    public function __construct(){
        parent::__construct();

        //load model
        $this->load->model('modules_manager/modules_manager_model');


        //make as preview version
        $this->enableDemoMode();


    }


    public function get_modules(){

        $result = FModuleLoader::loadAllModules();
        echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$result)); return;

    }


    public function install_internal_path(){


        $dir = RequestInput::post("dir");
        $path = RequestInput::post("path");

        $result = $this->modules_manager_model->install_internal_path($path,"application/modules",$dir);

        echo json_encode($result); return;

    }

    public function install(){

        $module_id = RequestInput::post('module_id');
        $result = $this->modules_manager_model->install($module_id);
        echo json_encode($result); return;

    }

    public function bulk_install(){

        $errors = array();

        $modules = RequestInput::post('modules');
        $modules = json_decode($modules,JSON_OBJECT_AS_ARRAY);



        $installed_module = array();
        foreach ($modules as $module){

            $result = $this->modules_manager_model->install($module);

            if(isset($result[Tags::SUCCESS]) && $result[Tags::SUCCESS]==0){
                if(isset($result[Tags::ERRORS]) && !empty($result[Tags::ERRORS])){
                    $errors[] = '"'.$module.'" installing with error: '.json_encode($result[Tags::ERRORS]);
                }else{
                    $errors[] = '"'.$module.'" installing with an unknown error';
                }

            }else if(isset($result[Tags::SUCCESS]) && $result[Tags::SUCCESS]==1){
                $installed_module[] = $module;
            }

        }

        if(empty($errors)){
            echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$installed_module)); return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors)); return;
        }


    }


    public function enable(){

        $module_id = RequestInput::post('module_id');
        $result = $this->modules_manager_model->enable($module_id);
        echo json_encode($result); return;

    }


    public function bulk_enable(){

        $errors = array();
        $modules_enabled = array();

        $modules = RequestInput::post('modules');
        $modules = json_decode($modules,JSON_OBJECT_AS_ARRAY);

        foreach ($modules as $module){

            $result = $this->modules_manager_model->enable($module);

            if(isset($result[Tags::SUCCESS]) && $result[Tags::SUCCESS]==0
                && isset($result[Tags::ERRORS]) && !empty($result[Tags::ERRORS])){
                $errors[] = '"'.$module.'" enabling with error: '.json_encode($result[Tags::ERRORS]);
            }else{
                $modules_enabled[] = $module;
            }

        }

        if(empty($errors)){
            echo json_encode(array(Tags::SUCCESS=>1,"enabled"=>$modules_enabled)); return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors)); return;
        }

    }

    public function bulk_upgrade(){

        $errors = array();
        $modules_enabled = array();

        $modules = RequestInput::post('modules');
        $modules = json_decode($modules,JSON_OBJECT_AS_ARRAY);

        foreach ($modules as $module){

            $result = $this->modules_manager_model->upgrade($module);

            if(isset($result[Tags::SUCCESS]) && $result[Tags::SUCCESS]==0
                && isset($result[Tags::ERRORS]) && !empty($result[Tags::ERRORS])){
                $errors[] = '"'.$module.'" upgrading with error: '.json_encode($result[Tags::ERRORS]);
            }else{
                $modules_enabled[] = $module;
            }

        }

        if(empty($errors)){
            echo json_encode(array(Tags::SUCCESS=>1,"upgraded"=>$modules_enabled)); return;
        }else{
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors)); return;
        }

    }

    public function disable(){

        $module_id = RequestInput::post('module_id');
        $result = $this->modules_manager_model->disable($module_id);
        echo json_encode($result); return;

    }

    public function uninstall(){

        $module_id = RequestInput::post('module_id');
        $result = $this->modules_manager_model->uninstall($module_id);
        echo json_encode($result); return;

    }

    public function upgrade(){

        $module_id = RequestInput::post('module_id');
        $result = $this->modules_manager_model->upgrade($module_id);
        echo json_encode($result); return;

    }

    //////////group

    public function install_group(){

        $list_modules = RequestInput::post('list');

        foreach ($list_modules as $module_id){
            $this->modules_manager_model->install($module_id);
        }

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }


    public function enable_group(){

        $list_modules = RequestInput::post('list');

        foreach ($list_modules as $module_id){
            $this->modules_manager_model->enable($module_id);
        }

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }


    public function disable_group(){

        $list_modules = RequestInput::post('list');

        foreach ($list_modules as $module_id){
            $this->modules_manager_model->disable($module_id);
        }

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }



    public function uninstall_group(){

        $list_modules = RequestInput::post('list');

        foreach ($list_modules as $module_id){
            $this->modules_manager_model->uninstall($module_id);
        }

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }

    public function upgrade_group(){

        $list_modules = RequestInput::post('list');

        foreach ($list_modules as $module_id){
            $this->modules_manager_model->upgrade($module_id);
        }

        echo json_encode(array(Tags::SUCCESS=>1)); return;

    }


    public function index(){

    }



}

/* End of file CmsDB.php */
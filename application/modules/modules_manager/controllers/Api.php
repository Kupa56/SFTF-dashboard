<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends API_Controller
{


    public function __construct()
    {
        parent::__construct();
    }


    public function availableModules()
    {
        $data['modules'] = ModuleManager::fetch();

        $user_id  = intval(RequestInput::post('user_id'));

        $availableMdules = array();
        if (!is_array($data['modules']))
            $data['modules'] = json_decode($data['modules'], JSON_OBJECT_AS_ARRAY);

        if($user_id == 0)
            $user_id = Security::decrypt($this->input->get_request_header('Session-User-Id', 0));

        foreach ($data['modules'] as $module) {
            $availableMdules[] = array(
                "module_name" => $module["module_name"],
                "enabled" => $module["_enabled"],
                "privileges" => $this->mGroupAccessModel->getModulePrivileges($user_id,$module["module_name"]),
                "user" => $user_id
            );
        }

        echo json_encode(array(Tags::SUCCESS => 1, Tags::RESULT => $availableMdules), JSON_FORCE_OBJECT);

    }

}
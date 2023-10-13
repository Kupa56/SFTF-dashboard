<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 1/15/19
 * Time: 12:32
 */


class GroupAccess{

    private static $permissions=NULL;

    public static function getGrpName($id){

        $context = &get_instance();
        $context->db->where('id',$id);
        $grp = $context->db->get("group_access",1);
        $grp = $grp->result();

        if(isset($grp[0])){
            return $grp[0]->name;
        }

       return NULL;
    }

    public static function reloadPermission($module,$grp_id=0){


        if(!is_string($module)){
            $module = strtolower(get_class($module));
        }

        $context = &get_instance();

        $context->db->where('module',$module);
        $count = $context->db->count_all_results("module_actions");

        if($count > 0){

            $context->db->where('module',$module);
            $actions = $context->db->get("module_actions");
            $actions = $actions->result();
            $actions = $actions[0];
            $actions = json_decode($actions->actions,JSON_OBJECT_AS_ARRAY);

            if($grp_id > 0){

                $context->db->where('id',$grp_id);
                $grp = $context->db->get("group_access",1);
                $grp = $grp->result();
                $grp = $grp[0];
                $permissions = $grp->permissions;
                $permissions = json_decode($permissions,JSON_OBJECT_AS_ARRAY);

                $actions_permission = array();
                foreach ($actions as $action){
                    $actions_permission[$action] = 1;
                }

                $permissions[$module] = $actions_permission;

                //save into database
                $context->db->where('id',$grp_id);
                $context->db->update('group_access',array(
                    'permissions'	=> json_encode($permissions,JSON_FORCE_OBJECT)
                ));

            }
        }
    }

    public static function reloadActions($module,$actions=array()){

        $context = &get_instance();

        ///////////// CRATE TABLE IF NEEDED /////////////
        GroupAccess::createTableModuleActions();
        GroupAccess::createTableGroupAccess();
        //////////////////////////////////////////////////


        $context->db->where('module',$module);
        $count = $context->db->count_all_results("module_actions");

        if($count ==0){



            $array = array(
                'module' => trim($module),
                'actions' => json_encode($actions,JSON_OBJECT_AS_ARRAY),
                'created_at' => date("Y-m-d H:i:s",time()),
                'updated_at' => date("Y-m-d H:i:s",time()),
            );
            $context->db->insert("module_actions",$array);

        }else{

            $context->db->where('module',$module);
            $array = array(
                'module' => trim($module),
                'actions' => json_encode($actions,JSON_OBJECT_AS_ARRAY)
            );
            $context->db->update("module_actions",$array);

        }
    }

    public static function registerActions($module, $registred_actions=array()){

        $context = &get_instance();

        ///////////// CRATE TABLE IF NEEDED /////////////
        GroupAccess::createTableModuleActions();
        GroupAccess::createTableGroupAccess();
        //////////////////////////////////////////////////


        $context->db->where('module',$module);
        $count = $context->db->count_all_results("module_actions");

        if($count == 0){

            $array = array(
                'module' => trim($module),
                'actions' => json_encode($registred_actions,JSON_OBJECT_AS_ARRAY),
                'created_at' => date("Y-m-d H:i:s",time()),
                'updated_at' => date("Y-m-d H:i:s",time()),
            );

            $context->db->insert("module_actions",$array);

        }else{

            $context->db->where('module',$module);
            $action = $context->db->get("module_actions",1);
            $action = $action->result();
            $action = $action[0];
            $saved_actions = $action->actions;
            $saved_actions = json_decode($saved_actions,JSON_OBJECT_AS_ARRAY);

            //check if there is new registered action
            foreach ($registred_actions as $value){
                if(!in_array($value,$saved_actions)){
                    $array = array(
                        'module' => trim($module),
                        'actions' => json_encode($registred_actions,JSON_OBJECT_AS_ARRAY)
                    );
                    $context->db->where('id',$action->id);
                    $context->db->update("module_actions",$array);
                    break;
                }
            }

            //check if there is new deleted action
            foreach ($saved_actions as $value){
                if(!in_array($value,$registred_actions)){
                    $array = array(
                        'module' => trim($module),
                        'actions' => json_encode($registred_actions,JSON_OBJECT_AS_ARRAY)
                    );
                    $context->db->where('id',$action->id);
                    $context->db->update("module_actions",$array);
                    break;
                }
            }
        }


        //update admin permission for this module if needed
        if (SessionManager::isLogged()){
            $grp_acc_id = SessionManager::getData('grp_access_id');
            GroupAccess::reloadPermission($module,$grp_acc_id);
        }else{

            $context->db->where('manager',1);
            $users = $context->db->get('user',1);
            $users = $users->result();
            if(isset($users[0])){
                GroupAccess::reloadPermission($module,$users[0]->grp_access_id);
            }

        }

    }

    public static function getModuleActions(){

        $context = &get_instance();
        $actions = $context->db->get("module_actions");
        $actions = $actions->result_array();

        $modules_actions = array();

        foreach ($actions as $value){
            $modules_actions[$value['module']] = json_decode($value['actions'],JSON_OBJECT_AS_ARRAY);
        }

        return $modules_actions;
    }

    public static function validateActions($actions = array()){

        foreach ($actions as $key => $value){
            if(!ModulesChecker::isEnabled($key))
                unset($actions[$key]);
        }

        return $actions;
    }

    public static function validateGrpAcc($grps = array()){

        foreach ($grps as $key => $value){

            $value['permissions'] = json_decode($value['permissions'],JSON_OBJECT_AS_ARRAY);

            $value['permissions'] = self::validateActions( $value['permissions']);

            $grps[$key]['permissions'] = json_encode( $value['permissions'] );
        }

        return $grps;
    }

    public static function initGrant(){

        if(self::$permissions==NULL ) {

            $context = &get_instance();

            if(!$context->db->table_exists("group_access"))
                return;

            $grp_id = $context->mUserBrowser->getData("grp_access_id");

            $context->db->where('id', $grp_id);
            $grp = $context->db->get("group_access", 1);
            $grp = $grp->result_array();

            if (count($grp) > 0) {
                $grp = $grp[0];
                self::$permissions = json_decode($grp['permissions'], JSON_OBJECT_AS_ARRAY);
            }

        }

    }

    public static function isGrantedUser($user_id,$module,$action=""){

        $permissions = array();

        $context = &get_instance();

        $context->db->select('grp_access_id');
        $context->db->where('id_user',$user_id);
        $user = $context->db->get('user',1);
        $user = $user->result();

        if(count($user)==0)
            return FALSE;

        $grp_id = $user[0]->grp_access_id;

        $context->db->where('id', $grp_id);
        $grp = $context->db->get("group_access", 1);
        $grp = $grp->result_array();

        if (count($grp) > 0) {
            $grp = $grp[0];
            $permissions = json_decode($grp['permissions'], JSON_OBJECT_AS_ARRAY);
        }

        return self::checkisGrant($permissions,$module,$action);
    }

    public static function isGranted($module, $action = "")
    {

        if (self::$permissions == NULL) {

            $context = &get_instance();
            $grp_id = $context->mUserBrowser->getData("grp_access_id");

            $context->db->where('id', $grp_id);
            $grp = $context->db->get("group_access", 1);
            $grp = $grp->result_array();

            if (count($grp) > 0) {
                $grp = $grp[0];
                self::$permissions = json_decode($grp['permissions'], JSON_OBJECT_AS_ARRAY);
            }

        }


        return self::checkIsGrant(self::$permissions,$module,$action);

    }

    private static function checkIsGrant($permissions,$module, $action = ""){


        if ($action == "") {

            if (isset($permissions[$module])) {
                foreach ($permissions[$module] as $key1 => $value1) {
                    if ($value1 == 1)
                        return TRUE; //module is granted
                }
            }

        } else {

            if (isset($permissions[$module][$action]) AND
                $permissions[$module][$action] == 1) {
                return TRUE; //module & action is granted
            }

        }


        return FALSE;
    }

    public static function createTableModuleActions(){

        $context = &get_instance();

        if ($context->db->table_exists('module_actions') )
            return;

        $context->load->dbforge();
        $context->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'module' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'actions' => array(
                'type' => 'TEXT',
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
        $context->dbforge->add_key('id', TRUE);
        $context->dbforge->create_table('module_actions', TRUE, $attributes);


    }

    public static function createTableGroupAccess(){

        $context = &get_instance();

        if ($context->db->table_exists('group_access') )
            return;

        $context->load->dbforge();
        $context->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'name' => array(
                'type' => 'VARCHAR(50)',
                'default' => NULL
            ),
            'permissions' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            'editable' => array(
                'type' => 'INT',
                'default' => 1
            ),
            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            )
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $context->dbforge->add_key('id', TRUE);
        $context->dbforge->create_table('group_access', TRUE, $attributes);


    }

}
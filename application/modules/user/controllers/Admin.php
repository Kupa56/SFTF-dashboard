<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Admin extends ADMIN_Controller
{

    public function __construct()
    {
        parent::__construct();


        ModulesChecker::requireEnabled("user");

    }

    public function index()
    {

        $actions = GroupAccess::getModuleActions();
        echo "<pre>";
        print_r($actions);
        die();

    }


    public function resetConsumption(){

        if (!GroupAccess::isGranted('user', MANAGE_GROUP_ACCESS))
            redirect("error?page=permission");

        $userId = RequestInput::get("userId");
        $callback = RequestInput::get("callback");
        $callback = base64_decode($callback);

        $result = $this->mUserModel->resetUserSettingSubscribe($userId);

        if($result){
            redirect($callback);return;
        }

        die("Error");

    }

    public function group_access()
    {

        if (!GroupAccess::isGranted('user', MANAGE_GROUP_ACCESS))
            redirect("error?page=permission");

        $data['actions'] = GroupAccess::getModuleActions();
        $data['actions'] = GroupAccess::validateActions($data['actions']);

        $data['group_accesses'] = $this->mGroupAccessModel->getGroupAccesses();
        $data['group_accesses'] = GroupAccess::validateGrpAcc($data['group_accesses']);



        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view('user/backend/html/grp_access/add_group_access');
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

    public function edit_group_access()
    {

        if (!GroupAccess::isGranted('user', MANAGE_GROUP_ACCESS))
            redirect("error?page=permission");


        $id = RequestInput::get("id");
        $id = intval($id);


        $grp = $this->mGroupAccessModel->getGroupAccess($id);
        if ($grp != NULL)
            if ($grp['editable'] == 0){
                if(ENVIRONMENT!="development"){
                    redirect(admin_url('error404'));
                }
            }

        $data['actions'] = GroupAccess::getModuleActions();
        $data['actions'] = GroupAccess::validateActions($data['actions']);

        $data['group_access'] = $this->mGroupAccessModel->getGroupAccess($id);
        $data['group_access']['permissions'] =  GroupAccess::validateActions(
            json_decode($data['group_access']['permissions'],JSON_OBJECT_AS_ARRAY)
        );

        $data['group_access']['permissions'] = json_encode($data['group_access']['permissions']);


        if ($data['group_access'] != NULL) {

            $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
            $this->load->view('user/backend/html/grp_access/edit_group_access');
            $this->load->view(AdminPanel::TemplatePath."/include/footer");

        } else
            redirect(admin_url('error404'));

    }


    public function delete_group_access()
    {

        if (!GroupAccess::isGranted('user', MANAGE_GROUP_ACCESS))
            redirect("error?page=permission");

        $id = RequestInput::get("id");
        $id = intval($id);

        $grp = $this->mGroupAccessModel->getGroupAccess($id);

        if ($grp != NULL) {

            if ($grp['editable'] == 1) {
                $this->mGroupAccessModel->deleteGrp($grp['id']);
            }

        }

        redirect(admin_url('user/group_access'));

    }

    public function shadowing()
    {

        if (!GroupAccess::isGranted('user'))
            redirect("error?page=permission");

        $id = RequestInput::get("id");

        if (ModulesChecker::isEnabled("demo") && SessionManager::getData('manager')==0) {
            $id = intval(ConfigManager::getValue("default_demo_user"));
        }


        $re = $this->mUserBrowser->shadowing_mode($id);
        if ($re)
            redirect(admin_url());

    }

    public function close_shadowing()
    {

        if ($this->mUserBrowser->isLogged() && $this->mUserBrowser->isShadowing()) {

            $re = $this->mUserBrowser->close_shadowing_mode();
            if ($re)
                redirect(admin_url("user/users"));
        } else {
            redirect(admin_url("error404"));
        }

    }

    public function users()
    {

        if(!GroupAccess::isGranted('user',MANAGE_USERS))
            redirect("error?page=permission");

        $params = array(
            "page" => RequestInput::get("page"),
            "id" => RequestInput::get("id"),
            "search" => RequestInput::get("search"),
            'limit' => NO_OF_ITEMS_PER_PAGE,
            "is_super" => TRUE,
            "user_id" => $this->mUserBrowser->getData("id_user")
        );

        $data['data'] = $this->mUserModel->getUsers($params);


        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("user/backend/html/users");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function edit()
    {

        if (!GroupAccess::isGranted('user', MANAGE_USERS))
            redirect("error?page=permission");

        $id = intval(RequestInput::get("id"));

        if(ModulesChecker::isEnabled("demo")){
            redirect(admin_url("user/edit?id=".ConfigManager::getValue("default_demo_user"))); exit();
        }


        $data['user'] = $this->mUserModel->userDetail($id);
        $data['grp_accesses'] = $this->mGroupAccessModel->getGroupAccesses();
        $data['user_settings'] = UserSettingSubscribe::load();
        $data['config'] = $this->mConfigModel->getParams();

        if(isset( $data['user'][Tags::RESULT][0])){
            $data['user'] = $data['user'][Tags::RESULT][0];
            $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
            $this->load->view("user/backend/html/edit");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");
        }else{
            redirect(admin_url('error404?code=u09'));
        }

    }


    public function profile()
    {

        $id = intval($this->mUserBrowser->getData("id_user"));
        $data['user'] = $this->mUserModel->userDetail($id);
        $data['grp_accesses'] = $this->mGroupAccessModel->getGroupAccesses();
        $data['user_settings'] = UserSettingSubscribe::load();
        $data['config'] = $this->mConfigModel->getParams();

        if(isset( $data['user'][Tags::RESULT][0])){
            $data['user'] = $data['user'][Tags::RESULT][0];
            $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
            $this->load->view("user/backend/html/profile");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");
        }else{
            redirect(admin_url('error404?code=u09'));
        }


    }

    public function add()
    {

        if (!GroupAccess::isGranted('user', ADD_USERS))
            redirect("error?page=permission");

        $data['grp_accesses'] = $this->mGroupAccessModel->getGroupAccesses();
        $data['user_subscribe_fields'] = UserSettingSubscribe::load();
        $data['config'] = $this->mConfigModel->getParams();

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("user/backend/html/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function userSetting()
    {

        if (!GroupAccess::isGranted('user', USER_SETTING))
            redirect("error?page=permission");

        $data['user_subscribe_fields'] = UserSettingSubscribe::load();
        $data['config'] = $this->mConfigModel->getParams();
        $data['grp_accesses'] = $this->mGroupAccessModel->getGroupAccesses();


        AdminTemplateManager::set_settingActive('user');

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("user/backend/html/user_setting");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function resendMail(){


        if(!GroupAccess::isGranted('user',MANAGE_USERS)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $user_id = RequestInput::get('id');
        $user_id = intval($user_id);

        $this->mUserModel->resendMailConfirmation($user_id);
        echo json_encode(array(Tags::SUCCESS=>1));

    }

    public function resendClientMail(){

        $user_id = RequestInput::get('id');
        $user_id = intval($user_id);

        if(SessionManager::getData("id_user") != $user_id){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check number of request
        $request_resend_numbers = SessionManager::getValue("request_resend_numbers",0);
        $request_resend_numbers = intval($request_resend_numbers);

        if($request_resend_numbers>2){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("You've exceeded maximum of request"))));
            return;
        }


        $this->mUserModel->resendMailConfirmation($user_id);

        //increase number of request
        $request_resend_numbers++;
        SessionManager::setValue("request_resend_numbers",$request_resend_numbers);

        //print result
        echo json_encode(array(Tags::SUCCESS=>1));return;

    }

    public function login(){
        if(!$this->mUserBrowser->isLogged())
            redirect(site_url('user/login'));
        else
            redirect(site_url(''));
    }

    public function signup(){
        if(!$this->mUserBrowser->isLogged())
            redirect(site_url('user/signup'));
        else
            redirect(site_url(''));

    }





}

/* End of file UserDB.php */
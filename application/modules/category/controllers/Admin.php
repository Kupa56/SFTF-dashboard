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

        ModulesChecker::requireEnabled("category");
    }


    public function edit()
    {

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('category', EDIT_CATEGORY))
            redirect("error?page=permission");


        $data['data'] = $this->mCategoryModel->getByCategory();
        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);

        $idc = intval(RequestInput::get("id"));
        $data2['category'] = $this->mCategoryModel->getByCategory($idc);
        if (isset($data2['category']['cats'][0])) {
            $data2['category'] = $data2['category']['cats'][0];
            $this->load->view("category/backend/html/edit", $data2);
        } else {
            redirect(admin_url("error404?s"));
        }
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function categories()
    {

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('category'))
            redirect("error?page=permission");


        AdminTemplateManager::set_settingActive('categories');

        $data['data'] = $this->mCategoryModel->getByCategory();
        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("category/backend/html/add");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }

}

/* End of file CategoryDB.php */
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

    }

    public function index()
    {

    }






    public function options(){

        /*
        *  CHECK USER PEMISSIONS
        */

        if (!GroupAccess::isGranted('setting',CHANGE_APP_SETTING))
            redirect("error?page=permission");

        AdminTemplateManager::set_settingActive('application');

        $data['config'] = $this->mConfigModel->getParams();


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("store/backend/html/options");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function cf_categories()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $data['data'] = $this->mCategoryModel->getByCategory();

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/cf_category/html/list");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function cf_categories_edit()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            redirect(admin_url("error404"));
        }

        $data = array();

        $idc = intval(RequestInput::get("id"));
        $data['category'] = $this->mCategoryModel->getByCategory($idc);

        if (isset($data['category']['cats'][0])) {

            $data['category'] = $data['category']['cats'][0];

            $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
            $this->load->view("store/backend/html/cf_category/html/edit");
            $this->load->view(AdminPanel::TemplatePath."/include/footer");

        } else {
            redirect(admin_url("error404"));
        }


    }



    public function reviews()
    {

        if (!GroupAccess::isGranted('store'))
            redirect("error?page=permission");


        $id_store = intval((RequestInput::get("id")));


        $params = array(
            "limit" => 1,
            "store_id" => $id_store,
        );

        if (!GroupAccess::isGranted("store", MANAGE_STORES)){
            $params['user_id'] = $this->mUserBrowser->getData("id_user");
        }

        $data["store"] = $this->mStoreModel->getStores($params);

        if (!isset($data["store"][Tags::RESULT][0]))
            redirect("error?page=permission");


        $page = intval(RequestInput::get("page"));

        $data['data'] = $this->mStoreModel->getReviews(array(
            'id_store' => $id_store,
            'page' => $page,
        ));


        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/reviews");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function view()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES))
            redirect("error?page=permission");


        $params = array(
            "limit" => 1,
            "store_id" => intval(RequestInput::get('id')),
        );

        $data['dataStores'] = $this->mStoreModel->getStores($params);

        if ($data['dataStores'][Tags::SUCCESS] == 0) {
            redirect(admin_url("error404"));
        }

        $data['categories'] = $this->mCategoryModel->getCategories();

        if (GroupAccess::isGranted('gallery')
            && ModulesChecker::isRegistred("gallery"))
            $data['gallery'] = $this->mGalleryModel->getGallery(array(
                "limit" => $this->mGalleryModel->maxfiles,
                "module" => "store",
                "module_id" => $data['dataStores'][Tags::RESULT][0]['id_store']
            ));

        // css
        $libcssdp = AdminTemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        AdminTemplateManager::addCssLibs($libcssdp);

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/edit");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }

    public function edit()
    {

        if (!GroupAccess::isGranted('store', EDIT_STORE))
            redirect("error?page=permission");


        $params = array(
            "limit" => 1,
            "store_id" => intval(RequestInput::get('id')),
            "user_id" => intval($this->mUserBrowser->getData("id_user")),
        );


        $data['dataStores'] = $this->mStoreModel->getStores($params);

        if (!isset($data['dataStores'][Tags::RESULT][0])) {
            redirect(admin_url("error404"));
        }

        $data['categories'] = $this->mCategoryModel->getCategories();


        if (GroupAccess::isGranted('gallery')
            && ModulesChecker::isRegistred("gallery"))
            $data['gallery'] = $this->mGalleryModel->getGallery(array(
                "limit" => $this->mGalleryModel->maxfiles,
                "module" => "store",
                "module_id" => $data['dataStores'][Tags::RESULT][0]['id_store']
            ));


        // css
        $libcssdp = AdminTemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        AdminTemplateManager::addCssLibs($libcssdp);

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/edit");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function create()
    {

        if (!GroupAccess::isGranted('store', ADD_STORE))
            redirect("error?page=permission");

        // css
        $libcssdp = AdminTemplateManager::assets("store", "plugins/timepicker/jquery.timepicker.css");
        AdminTemplateManager::addCssLibs($libcssdp);

        $data['categories'] = $this->mCategoryModel->getCategories();

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/create");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }


    public function all_stores()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES))
            redirect("error?page=permission");

        $id_store = intval(RequestInput::get("id"));
        $page = intval(RequestInput::get("page"));
        $status = intval(RequestInput::get("status"));
        $search = RequestInput::get("search");
        $category_id = intval(RequestInput::get("category_id"));

        $limit = NO_OF_STORE_ITEMS_PER_PAGE;

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "search" => $search,
            "status" => -1,
            "category_id" => $category_id,
            "order_by" => "recent"
        );

        $owner_id = intval(RequestInput::get("owner_id"));
        $params["owner_id"] = $owner_id;

        $data["data"] = $this->mStoreModel->getStores($params);
        $data["paginate_url"] = admin_url("store/all_stores");
        $data["h1_title"] = Translate::sprint("All Stores");


        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/stores");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function my_stores()
    {

        if (!GroupAccess::isGranted('store'))
            redirect("error?page=permission");

        $id_store = intval(RequestInput::get("id"));
        $page = intval(RequestInput::get("page"));
        $status = intval(RequestInput::get("status"));
        $search = RequestInput::get("search");
        $category_id = intval(RequestInput::get("category_id"));
        $limit = NO_OF_STORE_ITEMS_PER_PAGE;

        $params = array(
            "limit" => $limit,
            "page" => $page,
            "search" => $search,
            "status" => -1,
            "category_id" => $category_id,
            "order_by" => "recent"
        );

        $params['user_id'] = intval($this->mUserBrowser->getData("id_user"));

        $data["data"] = $this->mStoreModel->getStores($params);
        $data["paginate_url"] = admin_url("store/my_stores");
        $data["h1_title"] = Translate::sprint("My Stores");

        $this->load->view(AdminPanel::TemplatePath."/include/header", $data);
        $this->load->view("store/backend/html/stores");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");


    }


    public function verify()
    {

        if ($this->mUserBrowser->isLogged()) {

            if (!GroupAccess::isGranted('store', MANAGE_STORES))
                redirect("error?page=permission");


            $id = intval(RequestInput::get('id'));
            $accept = intval(RequestInput::get('accept'));


            $this->db->where('id_store', $id);
            $this->db->update('store', array(
                'verified' => 1,
                'status' => $accept,
            ));


        }

        //  redirect(admin_url('store/all_stores'));

        echo json_encode(array(Tags::SUCCESS => 1));
        return;
    }

    public function status()
    {

        if (!GroupAccess::isGranted('store', MANAGE_STORES)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        $id = intval(RequestInput::get("id"));
        echo $this->mStoreModel->storeAccess($id);return;
    }

}

/* End of file StoreDB.php */
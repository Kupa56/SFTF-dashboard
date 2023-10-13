<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model("category/category_model", "mCategoryModel");

    }

    public function getCategories(){

        $categories = $this->mCategoryModel->getCategories();
        $categories = $categories[Tags::RESULT];

        $data = array();

        foreach ($categories as $category){
            $data[] = array(
                "id" => $category['id_category'],
                "name" => Text::output($category['name'])
            );
        }

        echo json_encode($data);return;
    }

    public function re_order()
    {

        if (!GroupAccess::isGranted('category', EDIT_CATEGORY)) {
            echo json_encode(array(Tags::SUCCESS => 0, Tags::ERRORS => array(
                "error" => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $list = trim(RequestInput::post("list"));

        echo json_encode($this->mCategoryModel->re_order($list));

        return;

    }

    public function addCategory()
    {

        if(!GroupAccess::isGranted('category',ADD_CATEGORY)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $cat = trim(RequestInput::post("cat"));
        $image = RequestInput::post("image");
        $icon = RequestInput::post("icon");
        $color = RequestInput::post("color");
        $cf_id = RequestInput::post("cf_id");

        echo json_encode($this->mCategoryModel->addCategory(array(
            "cat" => $cat,
            "image" => $image,
            "icon" => $icon,
            "color" => $color,
            "cf_id" => $cf_id,
        )));

        return;

    }


    public function delete()
    {

        if(!GroupAccess::isGranted('category',DELETE_CATEGORY)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $id = intval(RequestInput::post("id"));

        echo json_encode($this->mCategoryModel->delete(
            $id
        ));
    }


    public function editCategory()
    {

        if(!GroupAccess::isGranted('category',EDIT_CATEGORY)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(
                "error"  => Translate::sprint(Messages::PERMISSION_LIMITED)
            )));
            exit();
        }

        //check if user have permission
        $this->enableDemoMode();

        $cat = trim(RequestInput::post("cat"));
        $cat_id = intval(trim(RequestInput::post("id")));
        $image = RequestInput::post("image");
        $icon = RequestInput::post("icon");
        $color = RequestInput::post("color");
        $cf_id = RequestInput::post("cf_id");

        $params =  array(
            "cat" => $cat,
            "cat_id" => $cat_id,
            "image" => $image,
            "icon" => $icon,
            "color" => $color,
            "cf_id" => $cf_id,
        );


        echo json_encode($this->mCategoryModel->editCategory(
            $params
        ));
    }


}

/* End of file CategoryDB.php */
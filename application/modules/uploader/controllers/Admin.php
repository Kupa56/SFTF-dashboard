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


    public function media(){

        if (!GroupAccess::isGranted('user'))
            redirect("error?page=permission");

        AdminTemplateManager::addCssLibs(
            AdminTemplateManager::assets('uploader','css/style.css')
        );

        $data['title'] = _lang("Media");
        $data['media'] = $this->uploader_model->getMedia(
            intval(RequestInput::get("page"))==0?1:intval(RequestInput::get("page"))
        );


        $this->load->view(AdminPanel::TemplatePath."/include/header",$data);
        $this->load->view("uploader/backend/html/media");
        $this->load->view(AdminPanel::TemplatePath."/include/footer");

    }





}

/* End of file StoreDB.php */
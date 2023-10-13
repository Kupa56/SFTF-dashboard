<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Messenger extends MAIN_Controller {

    public function __construct(){
        parent::__construct();
        $this->init("messenger");
    }

    public function onLoad()
    {

        define('SEND_RECEIVE_MESSAGES','send_and_receive');
        define('MANAGE_MESSAGES','manage_messages');

        //load model
        $this->load->model("messenger/messenger_model","mMessengerModel");

    }

    public function onCommitted($isEnabled)
    {
        parent::onCommitted($isEnabled); // TODO: Change the autogenerated stub

        if(!$isEnabled)
            return;

        AdminTemplateManager::registerMenu(
            'messenger',
            "messenger/menu",
            6
        );

        if($this->mUserBrowser->isLogged() && GroupAccess::isGranted('messenger')){

            $this->load->helper('cms/charts');

            SimpleChart::add('messenger','chart_v1_home',function ($months){

                if($this->mUserBrowser->getData("manager") == 1){
                    return $this->mMessengerModel->getMessengerAnalytics($months);
                }else{
                    return $this->mMessengerModel->getMessengerAnalytics($months,$this->mUserBrowser->getData('id_user'));
                }

            });
        }

    }

    public function onEnable()
    {
        $this->registerModuleActions();
    }



    public function onUpgrade()
    {
        $this->registerModuleActions();
        $this->mMessengerModel->updateDatabaseFields();

        return TRUE;
    }

    public function onInstall()
    {

        $this->mMessengerModel->updateDatabaseFields();
        return TRUE;
    }

    private function registerModuleActions(){

        GroupAccess::registerActions("messenger",array(
            SEND_RECEIVE_MESSAGES,
            MANAGE_MESSAGES
        ));

    }



}

/* End of file MessengerDB.php */
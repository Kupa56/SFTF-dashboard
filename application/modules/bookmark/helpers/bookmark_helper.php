<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 12/28/18
 * Time: 22:28
 */

class BookmarkManager{


    static public function add($params=array()){

        $context = &get_instance();

        $context->load->module('bookmark');

        return $context->mBookmarkModel->add($params);

    }


    static public function remove($params=array()){

        $context = &get_instance();

        $context->load->module('bookmark');

        return $context->mBookmarkModel->remove($params);

    }

    static public function exist($params=array()){


        if(_NOTIFICATION_AGREEMENT_USE==FALSE)
            return TRUE;

        $context = &get_instance();

        $context->load->module('bookmark');

        return $context->mBookmarkModel->exist($params);

    }


}


class NSModuleLinkers{


    private static $blm_data = array();

    public $module;
    public $request;
    public $callback;


    public static function getInstances(){
        return self::$blm_data;
    }

    public static function newInstance($module,$request,$callback=NULL){

        $object = new NSModuleLinkers();
        $object->module = $module;
        $object->request = $request;
        $object->callback = $callback;

        self::$blm_data[$module][$request] = $object;

    }

    public static function find($module,$request){

        if(isset(self::$blm_data[$module][$request]))
            return self::$blm_data[$module][$request]->callback;

        return NULL;
    }


}
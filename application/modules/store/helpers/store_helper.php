<?php


class LocationManager{

    public static function plug_pick_location($data=array(
        "lat"=>"",
        "lng"=>"",
        "address"=>"",
    ),$config=array(
        "lat"=>TRUE,
        "lng"=>TRUE,
        "address"=>TRUE,
    )){

        $ctx = &get_instance();
        $data['config'] = $config;
        $data['var'] = substr(md5(time()),0,10);
        $html = $ctx->load->view('store/plug/'.WEB_MAP_PICKER.'/html',$data,TRUE);
        $script = $ctx->load->view('store/plug/'.WEB_MAP_PICKER.'/js',$data,TRUE);

        return array(
            'html' => $html,
            'script' =>$script,
            'fields_id' => array(
                "lat"       =>"lat_".$data['var'],
                "lng"       =>"lng_".$data['var'],
                "address"   =>"address_".$data['var'],
            )
        );
    }

}

class StoreHelper{

    public static function get($params=array(), $whereArray=array(), $method = NULL){

        $context = &get_instance();
        $result = $context->mStoreModel->getStores($params,$whereArray,$method);
        return $result;
    }

}


class StoreManager{


    private static $store_subscriptions = array();

    public static function subscribe($module,$key){

        $context = &get_instance();

        if(!isset(self::$store_subscriptions[$key])){

            //add field if needed
            $context->mStoreModel->add_fk_field($module,$key);

            self::$store_subscriptions[$module] = array(
                "module" => $module,
                "field" => $key,
            );

        }

    }

    public static function getSubscriptions(){
        return self::$store_subscriptions;
    }


}

if( !function_exists("input_get") ){
    function input_get($key){
        $ctx = &get_instance();
        return $ctx->input->get($key);
    }
}
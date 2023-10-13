<?php


class LocationPickerManager{


    public static function getAddressDetail($latitude,$longitude){
        $url = site_url("location_picker/ajax/getAddressDetail?latitude=".$latitude."&longitude=".$longitude);
        $response = MyCurl::get($url);
        $response = json_decode($response,JSON_OBJECT_AS_ARRAY);
        return $response;
    }

    public static function plug_pick_location($data=array(
        "lat"=>"",
        "lng"=>"",
        "hideMap"=>"",
        "custom_address"=>"",
        "address"=>"",
    ),$config=array(
        "lat"=>TRUE,
        "lng"=>TRUE,
        "hideMap"=>FALSE,
        "custom_address"=>TRUE,
        "address"=>TRUE,
    ),$placeholder=array(
        "custom_address"=>"",
        "address"=>"",
    )){


        $ctx = &get_instance();
        $data['config'] = $config;
        $data['placeholder'] = $placeholder;
        $data['var'] = substr(md5(time()),0,10);
        $html = $ctx->load->view('location_picker/plug/'.WEB_MAP_PICKER.'/html',$data,TRUE);
        $script = $ctx->load->view('location_picker/plug/'.WEB_MAP_PICKER.'/js',$data,TRUE);

        return array(
            'html' => $html,
            'script' =>$script,
            'fields_id' => array(
                "lat"       =>"lat_".$data['var'],
                "lng"       =>"lng_".$data['var'],
                "custom_address"   =>"custom_address_".$data['var'],
                "address"   =>"address_".$data['var'],
                "city"   =>"city_".$data['var'],
                "country"   =>"country_".$data['var'],
            )
        );
    }

}
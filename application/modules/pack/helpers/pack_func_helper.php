<?php

if(!function_exists('whenModuleIsActive')){
    function whenModuleIsActive($module){

        if(!is_string($module)){
            $module = strtolower(get_class($module));
        }

        $cxt = &get_instance();

        $uri0 = $cxt->uri->segment(1);
        $uri1 = $cxt->uri->segment(2);

        if($uri0 == __ADMIN && $uri1==$module){
            return TRUE;
        }else if($uri0 == "ajax" && $uri1==$module){
            return TRUE;
        }else if($uri0 == $module && $uri1=="ajax"){
            return TRUE;
        }else if($uri0 == "api" && $uri1==$module){
            return TRUE;
        }else if($uri0 == $module && $uri1=="api"){
            return TRUE;
        }else if($uri0==$module){
            return TRUE;
        }

        return FALSE;
    }
}



class PackHelper{


    public static function getValidDur($date){
        $days = MyDateUtils::getDays($date);

        if($days>=30){
            return intval($days/30)." Month(s)";
        }else{
            return intval($days)." Day(s)";
        }

    }



}




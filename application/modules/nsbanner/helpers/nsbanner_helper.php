<?php

class NSBannerManager{

    public static function add($params=array()){

        $context = &get_instance();
        $context->load->model('nsbanner/nsbanner_model');

        $result = $context->nsbanner_model->add($params);
        return $result;

    }

}
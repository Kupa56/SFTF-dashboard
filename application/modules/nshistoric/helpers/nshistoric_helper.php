<?php

class NSHistoricManager{

    public static function add($params=array()){

        $context = &get_instance();
        $context->load->model('nshistoric/notification_history_model');


        $result = $context->notification_history_model->add($params);
        return $result;

    }

    public static function refresh($params=array()){

        $context = &get_instance();
        $context->load->model('nshistoric/notification_history_model');

        $result = $context->notification_history_model->refresh($params);
        return $result;

    }

}
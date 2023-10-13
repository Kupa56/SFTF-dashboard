<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 12/6/2017
 * Time: 17:14
 */


class ActionsManager{


    private static $registered_callback_actions = array(

    );

    public static function add_action($module,$key,$args=NULL){

        if(isset(self::$registered_callback_actions[$module][$key])){

            foreach (self::$registered_callback_actions[$module][$key] as $action){
                $callback = $action;
                if ($args != NULL)
                    call_user_func($callback, $args);
                else
                    call_user_func($callback, NULL);
            }

        }

    }

    public static function return_action($module,$key,$args=NULL){

        $object = NULL;
        if(isset(self::$registered_callback_actions[$module][$key])){
            foreach (self::$registered_callback_actions[$module][$key] as $callback){

                if($object == NULL)
                    $object = $args;

                $object = call_user_func($callback, $object);
            }

        }

        return $object;
    }

    public static function register($tag, $key, $callback){

        self::$registered_callback_actions[$tag][$key][] =  $callback;

    }
}

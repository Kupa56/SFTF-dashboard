<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 4/14/19
 * Time: 17:10
 */


class SimpleChart{


    private static $chart_list = array();


    private static $months = array();
    private static $weeks = array();
    private static $years = array();
    private static $days = array();

    public static function getMonths(){
        return self::$months;
    }

    public static function init($id){

        $months = getLast12Months();
        //fill months
        foreach ($months as $m) {
            $index = date("Y-m", strtotime($m ));
            self::$months[$index] = date("F", strtotime($m ));
        }

        if(!isset(self::$chart_list[$id]))
            self::$chart_list[$id] = array();

    }


    public static function add($module,$id,$callback){
        $result = call_user_func($callback,self::$months);
        self::$chart_list[$id][$module] = $result;
    }

    public static function get($id){
        if(isset(self::$chart_list[$id]))
            return self::$chart_list[$id];
        else
            return NULL;
    }

}

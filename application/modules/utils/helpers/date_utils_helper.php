<?php
/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 3/3/2018
 * Time: 13:27
 */

class MyDateUtils{

    const DISPLAY_FORMAT_DATE_ONLY = "d-m-Y";
    const DISPLAY_FORMAT_DATE_TIME_24H = "d-m-Y H:i";
    const DISPLAY_FORMAT_DATE_TIME_12H = "d-m-Y h:i A";

    public static function formatted($date=""){

        if($date == null)
            return;

        $current = date("Y-m-d",time());
        $inputDate = date("Y-m-d",strtotime($date));

        if($current==$inputDate){
            return date("H:i",strtotime($date));
        }

        return date("Y-m-d H:i",strtotime($date));
    }

    public static function getDays($date){

        if($date == null)
            return -1;

        $date = MyDateUtils::convert($date,"UTC",TimeZoneManager::getTimeZone(),"Y-m-d H:i:s");
        $now = time(); // or your date as well
        $your_date = strtotime($date);
        $datediff =  $your_date - $now;

        return round($datediff / (60 * 60 * 24));
    }

    public static function display($date,$format=MyDateUtils::DISPLAY_FORMAT_DATE_ONLY){
       return MyDateUtils::convert($date, "UTC",TimeZoneManager::getTimeZone(), $format);
    }

    public static function convert($time, $from_defaultTZ = "UTC", $to_newTimeTZ = "UTC", $schema="Y-m-d H:i:s"){

        if($time == null)
            return -1;

        if($from_defaultTZ==$to_newTimeTZ){
            $changetime = new DateTime($time, new DateTimeZone($from_defaultTZ));
            return $changetime->format($schema);
        }

        try {

            $changetime = new DateTime($time, new DateTimeZone($from_defaultTZ));
            if(trim($to_newTimeTZ)!="")
                $changetime->setTimezone(new DateTimeZone($to_newTimeTZ));
            return $changetime->format($schema);
        } catch(Exception $e) {
            //echo $e->getMessage() . '<br />';
        }

        return $time;

    }



    public static function getDate($schema="Y-m-d H:i:s",$time = NULL){
        return date($schema,$time == NULL?time():$time);
    }


    public static function format_interval(DateInterval $interval) {
        $result = "";
        if ($interval->y) { $result .= $interval->format("%y")." ".Translate::sprint("years")." "; }
        if ($interval->m) { $result .= $interval->format("%m")." ".Translate::sprint("months")." "; }
        if ($interval->d) { $result .= $interval->format("%d ")." ".Translate::sprint("days")." "; }
        if ($interval->h) { $result .= $interval->format("%h")." ".Translate::sprint("hours")." "; }
        if ($interval->i) { $result .= $interval->format("%i")." ".Translate::sprint("minutes")." "; }
        if ($interval->s) { $result .= $interval->format("%s")." ".Translate::sprint("seconds")." "; }

        return $result;
    }


    public static function diff_days(DateInterval $interval) {


    }


    public static function convert_months(DateInterval $interval) {
        $result = "";
        if ($interval->m) { $result .= $interval->format("%m")." ".Translate::sprint("months")." "; }
        return $result;
    }


    public static function convert_days(DateInterval $interval) {
        $result = "";
        if ($interval->d) { $result .= $interval->format("%d ")." ".Translate::sprint("days")." "; }
        return $result;
    }



}
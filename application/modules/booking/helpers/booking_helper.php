<?php


class BookingHelper{

    public static function optionsBuilderString($options){

        $string = "";

        if(!is_array($options))
            $options = json_decode($options);

        if(!empty($options))
            $string = "<br />";

        foreach ($options as $grp_label => $option){
            $string .= '&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;'.$grp_label.'<br/>';
        }

        return $string;
    }

}
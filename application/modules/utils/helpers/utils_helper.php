<?php


class KeysManager{

    private static $index = 0;
    private static $keys = array();

    /**
     * KeysManager constructor.
     */

    public function __construct()
    {

    }

    public function getKey($value){
        if(isset(self::$keys[$value])){
            return self::$keys[$value];
        }
        return "";
    }

    public function setKey($value){
        if(!in_array($value,self::$keys)){
            self::$index++;
            self::$keys[$value] = self::$index;
        }
    }

}
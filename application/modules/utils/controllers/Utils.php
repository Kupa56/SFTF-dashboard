<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Amine Maagoul
 * Date: {date}
 * Time: {time}
 */
class Utils extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function load_dependencies(){

        /*
         * load helper files
         */

        $files = $this->loadHelpers();

        foreach ($files as $file){
            $this->load->helper($file);
        }

        /*
        * load labraries files
        */

        $files = $this->loadLibraries();

        foreach ($files as $file){
            $this->load->library($file);
        }

    }

    private function loadHelpers(){

        $files = array();
        $path = APPPATH."modules/utils/helpers";

        if(!is_dir($path)){
            die("Please check you utils(helper) folder path!");
        }

        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if(preg_match("#_helper#",$entry)){
                        $file = explode("_helper",$entry);
                        $files[] = $file[0];
                    }
                }
            }
        }

        return $files;
    }

    private function loadLibraries(){

        $files = array();
        $path = APPPATH."modules/utils/libraries";


        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $filename = strtolower(preg_replace('#(\.php)?$#i', '', $entry));
                    $files[] = $filename;
                }
            }
        }

        return $files;
    }

}

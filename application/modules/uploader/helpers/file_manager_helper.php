<?php

/**
 * Created by PhpStorm.
 * User: Amine
 * Date: 3/13/2017
 * Time: 22:25
 */
class FileManager
{


    public static function generateQRCode($text){
        $ctx = &get_instance();
        header("Content-Type: image/png");

        $params['level'] = 'H';
        $params['size'] = 10;

        $params['data'] = $text;
        $ctx->ciqrcode->generate($params);
    }

    public static function getAllFilesFromFolder($path=""){
        $data = array();

        if(!is_dir($path))
            return array();

        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $data[] = Path::addPath($path,array($entry));
                }
            }
        }
        return $data;

    }

    public static function _openDir($dir="",$title="",$specific_folder=""){

        if($specific_folder=="")
            $path  = Path::getPath(array("uploads","files",$dir));
        else
            $path  = Path::getPath(array("uploads","files",$specific_folder,$dir));

        $data = array();


        if(!is_dir($path))
            return array();

        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    $path_2 = Path::addPath($path,array($entry));

                    if(is_file($path_2)){

                        $exp = explode(".", $entry);
                        $end = end($exp);

                        $res  = reset($exp);
                        $res = explode("-",$res);
                        $res = end($res);

                        $res = explode("x",$res);
                        $res = end($res);


                        $data[] = array(
                            "name"  =>   $entry,
                            "path"  => $path_2,
                            "url"   => FILES_BASE_URL.$dir."/$entry",
                            "ext"   =>   $end
                        ) ;

                    }

                }
            }
            closedir($handle);
        }

        if(!empty($data)){
            $data["name"] = $dir;
        }

        return $data;

    }


    public static function _removeDir($dir='',$specific_folder=""){

        if($specific_folder=="" or $specific_folder == NULL)
            $path  = Path::getPath(array("uploads","files",$dir));
        else
            $path  = Path::getPath(array("uploads","files",$specific_folder,$dir));

        $data = self::_openDir($dir);


        if ($handle = opendir($path) AND $path!="" AND is_dir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    $path_2 = Path::addPath($path,array($entry));

                    if(is_dir($path_2)){
                        unlink(Path::addPath($path_2,array($entry)));
                    }else if(is_file($path_2) && file_exists($path_2)){
                        unlink($path_2);
                    }

                }
            }
            closedir($handle);
            rmdir($path);
        }


        return $data;

    }

    public static function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
        $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster`enter code here`
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            return false; //Invalid hex color code
        }
        return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;die;
    }


}





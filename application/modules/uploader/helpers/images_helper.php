<?php


function loadAllImages()
{

    $path = Path::getPath(array("uploads", "images"));

    $data = array();

    if (!is_dir($path))
        return array();

    if ($handle = opendir($path) AND $path != "" AND is_dir($path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {

                $data[] = $entry;

            }
        }
        closedir($handle);
    }


    return $data;

}


function _openDir($dir = "")
{

    if(textVerify($dir) == "")
        return array();

    if(!preg_match("#^[0-9]+$#",textVerify($dir)) && textVerify($dir) != ""){
        $dir_ = json_decode($dir,JSON_OBJECT_AS_ARRAY);
        if(is_array($dir_))
            foreach ($dir_ as $d){
                $dir = $d;
            }
    }


    $path = Path::getPath(array("uploads", "images", $dir));
    $data = array();

    if (!is_dir($path)){
        return Modules::run('uploader/externalStorage/check',$dir);
    }

    if ($handle = opendir($path) AND $path != "" AND is_dir($path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {

                $path_2 = Path::addPath($path, array($entry));

                if (is_file($path_2)) {

                    $ar = explode(".", $entry);
                    $index = reset($ar);
                    $ext = end($ar);


                    $data[$index] = array(
                        "name" => $entry,
                        "path" => $path_2,
                        "url" => IMAGES_BASE_URL . $dir . "/$entry",
                        "ext" => $ext
                    );


                }

            }
        }
        closedir($handle);
    }

    if (!empty($data)) {
        $data["name"] = $dir;
    }

    return $data;

}


function _getAllSizes($id = "")
{

    if ($id != "") {
        $userDir = _openDir($id);
        return $userDir;

    }


}

function _removeDir($dir = '',$local=true)
{


    $path = Path::getPath(array("uploads", "images", $dir));
    $data = _openDir($dir);

    if ($handle = opendir($path) AND $path != "" AND is_dir($path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {

                $path_2 = Path::addPath($path, array($entry));
                if (is_file($path_2)) {
                    @unlink($path_2);
                }

            }
        }
        closedir($handle);
        rmdir($path);
    }


    return $data;

}


function getAllImgFolder()
{
    $path = Path::getPath(array("uploads", "images"));

    $data = array();

    if ($handle = opendir($path) AND $path != "" AND is_dir($path)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {

                $data[] = $entry;

            }
        }
        closedir($handle);
    }

    return $data;

}


function hex2RGB($hexStr, $returnAsString = false, $seperator = ',')
{
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
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray;
    die;
}

function parse_file_name($str, $delimiter = '-'){
    $slug = Text::removeSymbols($str);
    $slug = preg_replace("`[^\w]+`", "-", $slug);
    $slug = str_replace(" ", "-", $slug);
    return trim($slug, '-');
}

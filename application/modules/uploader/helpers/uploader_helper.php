<?php
/**
 * Created by PhpStorm.
 * User: amine
 * Date: 12/28/18
 * Time: 22:28
 */




class UploaderHelper
{

    private $files;
    private $ext;
    private $size;
    private $errors;
    private $namedir;
    private $download;

    private $isExternalStorageEnabled = true;


    public function __construct($data = NULL, $type = '')
    {

        if(ConfigManager::getValue('APP_STORAGE')=='local'){
            $this->isExternalStorageEnabled = false;
        }else{
            $this->isExternalStorageEnabled = true;
        }

        $this->files = $data;
        $this->ext = array("image/jpg", "image/jpeg", "image/png", "image/gif");
        $this->size = (1048576 * MAX_IMAGE_UPLOAD);
        $this->errors = array();

        if(is_array($this->files)){
            $filename = explode(".", $this->files['name']);
            $this->namedir = parse_file_name($filename[0]) .'-'. rand(000, 99999);
        }else{
            $this->namedir = time() . rand(000, 99999);
        }

        $this->download = FALSE;
        $this->createDir();

    }


    public function start64()
    {

        $ext = "jpeg";
        $filePath = Path::getPath(array("uploads", "images", $this->namedir, "full.jpeg"));

        file_put_contents($filePath, base64_decode($this->files));

        if (!file_exists($filePath)) {
            return array();
        }

        $result =  $this->uploadToLocalStorage($filePath);

        if($this->isExternalStorageEnabled){
            $result = $this->uploadToExternalStorage($this->namedir);
        }

        return $result;
    }


    public function start()
    {


        if ($this->files["error"] == UPLOAD_ERR_NO_TMP_DIR) {
            $this->errors['dir'] = "Error (no tmp dir) UPLOAD_ERR_NO_TMP_DIR:6";
        }

        if (isset($this->files['type']) AND !in_array(strtolower($this->files['type']), $this->ext)) {
            $this->errors['type'] = "The type's image isn't valid! " . $this->files['type'];
        }

        if (isset($this->files['size']) AND $this->files['size'] > $this->size) {
            $this->errors['size'] = "Error in size, the max size :" . MAX_IMAGE_UPLOAD." MB";
        }


        if (empty($this->errors)) {


            $ar = explode(".", $this->files['name']);
            $ext = end($ar);
            $filePath = Path::getPath(array("uploads", "images", $this->namedir, "full." . strtolower($ext)));

            if (!move_uploaded_file($this->files['tmp_name'], $filePath)) {
                return array();
            }

            if (!file_exists($filePath)) {
                return array();
            }


            $result =  $this->uploadToLocalStorage($filePath);

            if($this->isExternalStorageEnabled){
                $result = $this->uploadToExternalStorage($this->namedir);
            }

            return $result;

        }

        return array();
    }

    private function uploadToExternalStorage($fileName){

        Modules::run('uploader/ExternalStorage/upload',$fileName);

        //remove image from local storage
        _removeDir($fileName);

        return $this->result(_getAllSizes($fileName));

    }


    private function uploadToLocalStorage($file){

        $ar = explode(".", $this->files['name']);

        $image = new SimpleImage();
        $image->load($file);
        $image->resizeToWidth(560);

        $newpath = Path::getPath(array("uploads", "images",$this->namedir, "560_560." . $image->getExt()));
        $image->save($newpath);

        $image->resizeToWidth(200);
        $newpath = Path::getPath(array("uploads", "images", $this->namedir, "200_200." . $image->getExt()));
        $image->save($newpath);

        $image->resizeToWidth(100);
        $newpath = Path::getPath(array("uploads", "images", $this->namedir, "100_100." . $image->getExt()));
        $image->save($newpath);
        $image->destroy();

        $imageData = _getAllSizes($this->namedir);

        return $this->result($imageData);
    }

    private function result($file){


        if (!empty($file) && isset($file['200_200'])) {

            $imageData['html'] = ""
                . "<div data-id=\"".$file['name']."\" class=\"image-uploaded cursor-draggable item_" . $file['name'] . "\">
                        <i class='index'></i><a  id=\"image-preview\">    
                           <img src='" . $file['200_200']['url'] . "' alt=''/>
                        </a>
                        
                        <div class=\"clear\"></div>
                        <a href=\"#\"  data=\"".$file['name']."\" id=\"delete\"><i class=\"fa fa-trash\"></i>&nbsp;&nbsp;"._lang("Delete")."</a></div>"
                . "<input id=\"image-data\" type=\"hidden\" value=\"" . md5($file['name']) . "\">";

            $imageData['type'] = "image/" . strtolower($file['200_200']['ext']);
            $imageData['image_data'] = md5($file['name']);
            $imageData['image'] = $file['name'];

            return $imageData;
        }
    }


    public function getErrors()
    {
        return $this->errors;
    }

    private function createDir()
    {
        @mkdir(Path::getPath(array("uploads", "images", $this->namedir)));
        return;
    }


}

class ImageManagerUtils{


    const IMAGE_SIZE_200 = "200_200";
    const IMAGE_SIZE_560 = "560_560";
    const IMAGE_SIZE_100 = "100_100";
    const IMAGE_FULL_SIZE = "full";

    public static function getImage($dir,$size=self::IMAGE_SIZE_200){

        $images = _openDir($dir);
        if(isset($images[$size]['url'])){
            return $images[$size]['url'];
        }

        return "";
    }

    public static function getFirstImage($images,$size=self::IMAGE_SIZE_200){

        if(is_string($images))
            $images  = json_decode($images,JSON_OBJECT_AS_ARRAY);

        if(isset($images[0]) && is_string($images[0])){
            $images = _openDir($images[0]);
            if(isset($images[$size]['url'])){
                return $images[$size]['url'];
            }
        }else if(isset($images[0]) && is_array($images[0])){
            $images = $images[0];
            if(isset($images[$size]['url'])){
                return $images[$size]['url'];
            }
        }

        return NULL;
    }


    public static function checkAndClearImages(){

        $context = &get_instance();




    }

    public static function imageHTML($images){
        if (isset($images['100_100']['url'])) {
            return '<img src="' . $images['100_100']['url'] . '"width="50" height="50" alt="Product Image">';
        } else {
            return '<img src="' . adminAssets("images/def_logo.png") . '"width="50" height="50" alt="Product Image">';
        }
    }

    public static function getValidImages($userImageStr){

        if($userImageStr=="")
            return array();

        //convert from image ID or json to the array
        if (!is_array($userImageStr) and !preg_match('#^([0-9]+)$#',$userImageStr)) {
            $userImage = json_decode($userImageStr, JSON_OBJECT_AS_ARRAY);
        }else if(!is_array($userImageStr) and preg_match('#^([0-9]+)$#',$userImageStr)) {
            $userImage = array($userImageStr);
        }



        $array = array();

        if(isset($userImage)){
            foreach ($userImage as $dirName){

                $userImage = _openDir($dirName);

                if(!empty($userImage))
                    $array[] = $userImage;

            }
        }else{
            $array = $userImageStr;
        }

        //validate all images

        $new_arrays = array();

        foreach ($array as $key => $img){
            if(empty($img))
                unset($array[$key]);
            else
                $new_arrays[] = $img;
        }

        return $new_arrays;
    }


    public static function unused_images($new,$old){
        $ctx = &get_instance();
        $ctx->uploader_model->images_to_clear($new,$old);
    }

    public static function validate($image,$manager="unknown"){
        $ctx = &get_instance();
        $ctx->uploader_model->validate($image,$manager);
    }

    public static function checkRequestValidity($key){

        $ctx = &get_instance();

        $errors = array();

        if(!preg_match('#^([a-zA-Z]+)$#i',  ($key!=null?$key:"")    )){
            $errors['error'] = Translate::sprint('Invalidate Upload Function');
        }else{
            $limit = $ctx->uploader->getUploaderSession($key);
            $limit_saved = $ctx->uploader->getUploaderSession('saved-'.$key);
            if($limit==$limit_saved){
                $errors[] = Translate::sprint("You have exceeded the maximum number of files");
            }
        }

        return $errors;

    }

    public static function increaseRequest($key,$id){

        $ctx = &get_instance();

        $errors = array();

        if(!preg_match('#^([a-zA-Z]+)$#i',  ($key!=null?$key:"")    )){
            $errors['error'] = Translate::sprint('Invalidate Upload Function');
        }else{
            $limit = $ctx->uploader->getUploaderSession($key);
            $limit_saved = $ctx->uploader->getUploaderSession('saved-'.$key);
            if($limit==$limit_saved){
                $errors[] = Translate::sprint("You have exceeded the maximum number of files");
            }
        }

        if(!empty($errors))
            return $errors;

        $limit_saved++;
        $ctx->uploader->setUploaderSession('saved-'.$key,$limit_saved);

        $list = $ctx->uploader->getUploaderSession('loaded-images'.$key);
        $list[] = $id;

        $ctx->uploader->setUploaderSession('loaded-images'.$key,$list);

        return TRUE;
    }

}




interface UploaderInterface{
    public function onClearFolder();
}

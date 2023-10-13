<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Ajax extends AJAX_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function loadMedia(){

        $page = RequestInput::post('page');

        $result = $this->uploader_model->getMedia($page);

        $html = "";
        foreach ($result[Tags::RESULT] as $media){
            $data['media'] = $media;
            $html .= $this->load->view('uploader/backend/html/image-item-ajax',$data,TRUE);
        }

        echo json_encode(array(Tags::SUCCESS=>1,"html"=>$html,'pagination'=>$result['pagination']));

    }

    public function clearByKey(){

        $key = RequestInput::post('key');
        $list = $this->uploader->getUploaderSession('loaded-images'.$key);


        foreach ($list as $value){
            $this->delete($key,$value);
        }

        echo json_encode(array(Tags::SUCCESS=>1));
    }





    public function delete($key=NULL,$id=NULL){


        $errors = array();

        if($key==NULL)
            $key = RequestInput::post('key');

        if($id==NULL)
            $id = RequestInput::post('id');


        if(!preg_match('#^([a-zA-Z]+)$#i',$key)){
            $errors['error'] = Translate::sprint('Invalidate Upload Function');
        }

        if(!preg_match('#^([0-9]+)$#i',$id)){
            $errors['error'] = Translate::sprint('Invalidate image ID');
        }


        if(empty($errors)){

            //less saved number
            $limit_saved = $this->uploader->getUploaderSession('saved-'.$key);

            $limit_saved--;
            if($limit_saved<0)
                $limit_saved = 0;

            $this->uploader->setUploaderSession('saved-'.$key,$limit_saved);

            //remove image from session
            $list = $this->uploader->getUploaderSession('loaded-images'.$key);
            if(isset($list[$id]))
                unset($list[$id]);
            $this->uploader->setUploaderSession('loaded-images'.$key,$list);

            $s = array(Tags::SUCCESS=>1);
            echo json_encode($s);return;
        }


        $s = array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        echo json_encode($s);return;
    }

    public function uploadImage64($data64=NULL){


        $key = RequestInput::post('key');
        if($key == NULL)
            $key = "";

        $errors = ImageManagerUtils::checkRequestValidity($key);

        if(!empty($errors)){
            return $errors;
        }


        $uploader = new UploaderHelper($data64);
        $result = $uploader->start64();

        if(!empty($uploader->getErrors())){
            return $uploader->getErrors();
        }


        $id = $result['image'];
        $type = $result['type'];

        $user_id = intval($this->mUserBrowser->getData("id_user"));

        if($user_id==0){
            $user_id = 1;
        }

        $this->db->insert('image',array(
            "image"     =>  $id,
            "type"      =>  $type,
            "user_id"      =>  $user_id,
        ));


        ImageManagerUtils::increaseRequest($key,$id);

        return $result;

    }

    public function uploadImage64_admin($data64=NULL){

        $errors = array();

        $uploader = new UploaderHelper($data64);

        $result = $uploader->start64();

        if(!empty($uploader->getErrors())){
            return $uploader->getErrors();
        }

        $id = $result['image'];
        $type = $result['type'];

        $user_id = intval($this->mUserBrowser->getData("id_user"));

        if($user_id==0){
            $user_id = 1;
        }

        $this->db->insert('image',array(
            "image"     =>  $id,
            "type"      =>  $type,
            "user_id"      =>  $user_id,
        ));

        return $result;

    }

    public function uploadImage(){


        $key = RequestInput::post('key');
        if($key == NULL)
            $key = "";

        $errors = ImageManagerUtils::checkRequestValidity($key);

        if(!empty($errors)){
            echo json_encode(array(Tags::SUCCESS=>0,"errors"=>$errors));
            exit();
        }


        $uploader = new UploaderHelper($_FILES['addimage']);
        $result = $uploader->start();

        if(!empty($uploader->getErrors())){
            return $uploader->getErrors();
        }


        $id = $result['image'];
        $type = $result['type'];

        $user_id = intval($this->mUserBrowser->getData("id_user"));

        if($user_id==0){
            $user_id = 1;
        }

        $this->db->insert('image',array(
            "image"     =>  $id,
            "type"      =>  $type,
            "user_id"      =>  $user_id,
        ));

        ImageManagerUtils::increaseRequest($key,$id);

        echo json_encode(array("errors"=>$errors,"results"=>$result));
        exit();

    }

    public function uploadURLs(){

        $urls = RequestInput::post('URLs');

        if(is_string($urls))
            $urls = json_decode($urls,JSON_OBJECT_AS_ARRAY);

        $data = array();

        if(empty($urls)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(_lang("No url provided"))));return;
        }

        foreach ($urls as $url){

            $imageDATA = url_get_content($url);
            $imageDATA64 = base64_encode($imageDATA);
            $result = $this->uploadImage64($imageDATA64);
            $data[] = $result;

        }

        if(!empty($data)){
            echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$data));return;
        }

        echo json_encode(array(Tags::SUCCESS=>0));return;

    }

    public function uploadURLs_admin(){

        $urls = RequestInput::post('URLs');

        if(is_string($urls))
            $urls = json_decode($urls,JSON_OBJECT_AS_ARRAY);

        $data = array();

        if(empty($urls)){
            echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array(_lang("No url provided"))));return;
        }

        foreach ($urls as $url){

            $imageDATA = url_get_content($url);
            $imageDATA64 = base64_encode($imageDATA);
            $result = $this->uploadImage64_admin($imageDATA64);
            $data[] = $result;

        }

        if(!empty($data)){
            echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$data));return;
        }

        echo json_encode(array(Tags::SUCCESS=>0));return;

    }

    public function uploadFiles(){

        $key = RequestInput::post('key');
        if($key == NULL)
            $key = "";

        if(!preg_match('#^([a-zA-Z]+)$#i',$key)){
            $errors['error'] = Translate::sprint('Invalidate Upload Function');
        }else{

            $limit = $this->uploader->getUploaderSession($key);
            $limit_saved = $this->uploader->getUploaderSession('saved-'.$key);

            if($limit==$limit_saved){
                $errors[] = Translate::sprint("You have exceeded the maximum number of files");
            }
        }

        if(!empty($errors)){
            echo json_encode(array("errors"=>$errors,"results"=>array()));
            exit();
        }


        if (!isset($files) AND isset($_FILES['file'])){
            $file = $_FILES['file'];
        }else if(isset($files)) {
            $file =$files;
        }else{

            $errors = array();
            $errors[] = "Upload failed!";
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        }


        $uploader = new FileUploader($this,$file);


        if(isset($file)){
            $types = $this->uploader->getUploaderSession( "file_types_".$key);
            $uploader->ext = $types;
        }

        $result = $uploader->start();

        if(empty($uploader->getErrors())  and isset($result['result'])){

            $object = $result[0];

            $name = $result['name'];
            $dir = $result['result']['dir'];

            $this->db->insert('files',array(
                'user_id' => $this->mUserBrowser->getData("id_user"),
                'dir' => $dir,
                'ext' => $object['ext'],
                'file' => $object['url'],
                'updated_at' => date("Y-m-d H:i:s",time()),
                'created_at' => date("Y-m-d H:i:s",time()),
            ));

            $limit_saved++;
            $this->uploader->setUploaderSession('saved-'.$key,$limit_saved);

            $list = $this->uploader->getUploaderSession('loaded-files'.$key);
            $list[] = $name;

            $this->uploader->setUploaderSession('loaded-files'.$key,$list);



            echo json_encode(array("errors"=>$errors,"results"=>$result['result']));
            exit();
        }


        echo json_encode(array("errors"=>$uploader->getErrors(),"results"=>array()));
        exit();

    }


    public function delete_file($key=NULL,$id=NULL){


        $errors = array();

        if($key==NULL)
            $key = RequestInput::post('key');

        if($id==NULL)
            $id = RequestInput::post('id');


        if(!preg_match('#^([a-zA-Z]+)$#i',$key)){
            $errors['error'] = Translate::sprint('Invalidate Upload Function');
        }

        if(!preg_match('#^([0-9]+)$#i',$id)){
            $errors['error'] = Translate::sprint('Invalidate image ID');
        }


        if(empty($errors)){

            $user_id = $this->mUserBrowser->getData('user_id');

            //remove image from database
            $this->db->where('user_id',$user_id);
            $this->db->where('dir',$id);
            $this->db->delete('files');

            //remove image folder
            @FileManager::_removeDir($id);

            //less saved number
            $limit_saved = $this->uploader->getUploaderSession('saved-'.$key);

            $limit_saved--;
            if($limit_saved<0)
                $limit_saved = 0;

            $this->uploader->setUploaderSession('saved-'.$key,$limit_saved);

            //remove image from session
            $list = $this->uploader->getUploaderSession('loaded-images'.$key);
            if(isset($list[$id]))
                unset($list[$id]);
            $this->uploader->setUploaderSession('loaded-images'.$key,$list);

            $s = array(Tags::SUCCESS=>1);
            echo json_encode($s);return;
        }


        $s = array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        echo json_encode($s);return;
    }




}

/* End of file UploaderDB.php */
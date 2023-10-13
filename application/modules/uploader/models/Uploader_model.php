<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Uploader_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
    }

    public function update_v1(){

        $version = ModulesChecker::getField("uploader","version_name");

        if (!version_compare($version, '1.1.1', '>='))
            return;

        $this->db->where("used",0);
        $this->db->update("image",array(
            'used' => 1
        ));


    }

    public function validate($image,$manager="unknown"){

        $this->db->where('image',$image);
        $exist = $this->db->count_all_results('image');

        if($exist == 0){
            return NULL;
        }

        $this->db->where('image',$image);
        $this->db->update('image',array(
            'used' => 1,
            'managed_by' => $manager,
        ));
    }



    public function images_to_clear($new,$old){

        $new = json_decode($new);
        $old = json_decode($old);

        if(is_string($new))
            $new = array($new);

        if(is_string($old))
            $old = array($old);


        //get all unused files
        $clear = array();
        foreach ($old as $image){
                if(!in_array($image,$new)){
                    $clear[] = $image;
                }
        }

        //update unused field
        foreach ($clear as $image){
            $this->db->where('image',$image);
            $this->db->update('image',array(
                'used' => 0
            ));
        }

    }


    public function getMedia($page=1){

        $limit = 20;

        $this->db->where('user_id',SessionManager::getData('id_user'));
        $this->db->join("user", "user.id_user=image.user_id");
        $count = $this->db->count_all_results("image");

        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();

        if ($count == 0)
            return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => array());

        $this->db->where('user_id',SessionManager::getData('id_user'));
        $this->db->join("user", "user.id_user=image.user_id");

        $this->db->order_by("id_image","desc");
        $this->db->select("image.*");
        $this->db->from("image");

        $this->db->limit($pagination->getPer_page(), $pagination->getFirst_nbr());
        $media = $this->db->get();
        $media = $media->result_array();

        foreach ($media as $key => $value){
            $media[$key]['image'] = _openDir($value['image']);
        }

        return array(Tags::SUCCESS => 1, "pagination" => $pagination, Tags::COUNT => $count, Tags::RESULT => $media);

    }



    public function uploadFiles($files=NULL){

        /*
         * path file
         * resize (true|false)
         * save in database (true|false)
         * base64 (true|false)
         */


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
        //$uploader->setCopyright(Path::getPath(array("template","copyright.png")));
        $result = $uploader->start();

        if(empty($uploader->getErrors()) and isset($result['result'])){

            $type = $this->getTypeFile($files['type']);

            $file = new FileDB();
            $file->directory =$result['result']['dir'];
            $file->filetype=strtolower($type);
            $file->save();
            $insertedId = $file->id;

            return array(Tags::CODE=>Codes::SUCCESS,"errors"=>$uploader->getErrors(),"results"=>$result);


        }

        return array(Tags::CODE=>Codes::FAILED,"errors"=>$uploader->getErrors());

    }


    public function delete($dirID){

        $this->db->where("image",$dirID);
        $this->db->delete("image");

        @_removeDir($dirID);

        return TRUE;
    }

    public function uploadImage64($files=NULL){


        if($files!=NULL and isset($files) AND is_string($files)){

            $Upoader = new UploaderHelper($files);

            $r = $Upoader->start64();

            $errors = array();
            $errors = $Upoader->getErrors();

            if(empty($errors)){

                if(isset($r['image']) AND $r['image']!=""){

                    $imageData = array("type"=>$r['type'],"image"=>$r['image']);
                    $this->db->insert("image",$imageData);
                    $id = $this->db->insert_id();

                    if(isset($imageData['image'])){
                        $imageData['images'] = _openDir($imageData['image']);
                    }


                    return array(Tags::SUCCESS=>1,Tags::RESULT=>$imageData,"image_id"=>$id);

                }else{
                    return array(Tags::SUCCESS=>0,  Tags::ERRORS=>array("add"=>Translate::sprint("Error")));
                }

            }else{


                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors,"results"=>$r);
            }


        }else{

            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("select"=>Translate::sprint("Please Select image")));

        }
    }


    /*public function uploadImage($files=NULL){



        if($files!=NULL and isset($files)){

            //$_FILES['image']['type']="image/jpg";
            $Upoader = new UploaderHelper($files);

            $r = $Upoader->start();
            //echo json_encode(array("errors"=>$Upoader->getErrors(),"results"=>$r));



            $errors = array();
            $errors = $Upoader->getErrors();

            if(empty($errors)){

                if(isset($r['image']) AND $r['image']!=""){

                    $imageData = array("type"=>$r['type'],"image"=>$r['image']);
                    $this->db->insert("image",$imageData);
                    $id = $this->db->insert_id();

                    if(isset($imageData['image'])){
                        $imageData['images'] = _openDir($imageData['image']);
                    }



                    return array(Tags::SUCCESS=>1,"data"=>$imageData,"image_id"=>$id);

                }else{
                    return array(Tags::SUCCESS=>0,  Tags::ERRORS=>array("add"=>"Erreur dans l'ajout votre image de votre marque"));
                }

            }else{


                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors,"results"=>$r);
            }


        }else{

            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("select"=>Translate::sprint("Please Select image")));

        }
    }*/

    public function uploadImage($files=NULL){



        if($files!=NULL and isset($files)){

            //$_FILES['image']['type']="image/jpg";
            $Upoader = new UploaderHelper($files);

            $r = $Upoader->start();
            //echo json_encode(array("errors"=>$Upoader->getErrors(),"results"=>$r));



            $errors = array();
            $errors = $Upoader->getErrors();

            if(empty($errors)){

                if(isset($r['image']) AND $r['image']!=""){

                    $imageData = array("type"=>$r['type'],"image"=>$r['image']);
                    $this->db->insert("image",$imageData);
                    $id = $this->db->insert_id();

                    if(isset($imageData['image'])){
                        $imageData['images'] = _openDir($imageData['image']);
                    }



                    return array(Tags::SUCCESS=>1,"data"=>$imageData,"image_id"=>$id);

                }else{
                    return array(Tags::SUCCESS=>0,  Tags::ERRORS=>array("add"=>"Erreur dans l'ajout votre image de votre marque"));
                }

            }else{


                return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors,"results"=>$r);
            }


        }else{

            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("select"=>Translate::sprint("Please Select image")));

        }
    }


    public function clear(){

        $images_key_from_db = array();

        //Collect active images


        //gallery
        $images = $this->db->query('SELECT image FROM image WHERE id_image IN (SELECT image_id FROM gallery)');
        $images = $images->result();

        foreach ($images as $image){
            $collected_active_images[] = $image->image;
        }


        //logo
        if(!is_array(APP_LOGO))
            $images = json_decode(APP_LOGO,JSON_OBJECT_AS_ARRAY);
        else
            $images = json_decode(APP_LOGO,JSON_OBJECT_AS_ARRAY);

        if(is_array($images) && count($images)>0){
            foreach ($images as $key => $value)
                $collected_active_images[] = $value;
        }else{
            $collected_active_images[] = $images;
        }

        ///////get all images from folder and check to remove
        $folders = getAllImgFolder();

        foreach ($folders as $folder){
            if(!in_array($folder,$images_key_from_db)){
               $this->delete($folder);
            }
        }

        //clear Database from not exist images
        $media = $this->db->get("image");
        $media = $media->result_array();

        foreach ($media as $key => $value){
            $img = _openDir($value['image']);
            if(empty($img)){
               $this->db->where("id_image",$value['id_image']);
               $this->db->delete("image");
            }
        }

    }

    public function clearDatabase(){

        //clear Database from not exist images
        $media = $this->db->get("image");
        $media = $media->result_array();

        foreach ($media as $key => $value){
            $img = _openDir($value['image']);
            if(empty($img)){
                $this->db->where("id_image",$value['id_image']);
                $this->db->delete("image");
            }
        }

    }


    public function updateFields(){


        if (!$this->db->field_exists('managed_by', 'image'))
        {
            $fields = array(
                'managed_by'  => array('type' => 'VARCHAR(100)', 'default' => ""),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('image', $fields);
        }

        if (!$this->db->field_exists('used', 'image'))
        {
            $fields = array(
                'used'  => array('type' => 'INT', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('image', $fields);
        }


        if (!$this->db->field_exists('updated_at', 'image'))
        {
            $fields = array(
                'updated_at'  => array('type' => 'DATETIME', 'after' => 'image','default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('image', $fields);
        }


        if (!$this->db->field_exists('created_at', 'image'))
        {
            $fields = array(
                'created_at'  => array('type' => 'DATETIME', 'after' => 'updated_at','default' => NULL),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('image', $fields);
        }


    }

    public function createTable(){

        if(!$this->db->table_exists('files')){


            $this->load->dbforge();
            $this->dbforge->add_field(array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ),
                'dir' => array(
                    'type' => 'VARCHAR(100)',
                    'default' => NULL
                ),

                'user_id' => array(
                    'type' => 'INT',
                    'default' => NULL
                ),

                'ext' => array(
                    'type' => 'VARCHAR(100)',
                    'default' => NULL
                ),

                'file' => array(
                    'type' => 'VARCHAR(100)',
                    'default' => NULL
                ),


                'updated_at' => array(
                    'type' => 'DATETIME',
                    'default' => NULL
                ),
                'created_at' => array(
                    'type' => 'DATETIME',
                    'default' => NULL
                ),
            ));

            $attributes = array('ENGINE' => 'InnoDB');
            $this->dbforge->add_key('id', TRUE);
            $this->dbforge->create_table('files', TRUE, $attributes);

        }

    }

}
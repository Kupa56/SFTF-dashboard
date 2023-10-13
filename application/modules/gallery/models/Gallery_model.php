<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Gallery_model extends CI_Model
{

    public $maxfiles = 20;

    public function __construct()
    {
        parent::__construct();

    }


    public function setup($tag,$galley=array(),$uid=0){

        $variable = "var_".md5($tag);
        $data["tag"] =  $tag;
        $data["images"] =  $galley;
        $data["uid"] =  $uid;
        $data["gallery"] =  $galley;
        $data["variable"] =  $variable;
        $this->load->view("gallery/html",$data);

        return $variable;
    }

    public function setupV2($tag,$galley=array(),$uid=0){

        $variable = "var_".md5($tag);
        $data["tag"] =  $tag;
        $data["images"] =  $galley;
        $data["uid"] =  $uid;
        $data["gallery"] =  $galley;
        $data["variable"] =  $variable;
        $this->load->view("gallery/html",$data);

        return $data;
    }

    /*public function loadJs($tag,$galley=array(),$uid=0){
        $data["tag"] =  $tag;
        $data["images"] =  $galley;
        $data["uid"] =  $uid;
        $this->load->view("gallery/js",$data);
        return "fileUploaded_".$tag;
    }*/


    public function getGallery($params=array()){

        extract($params);

        $data = array();
        $errors = array();

        if(!isset($page))
            $page = 1;

        if($page==0)
            $page = 1;

        if(!isset($limit))
            $limit = 20;


        if( !isset($module) ){
            $errors[] = Translate::sprint("Module is missing");
        }else if($module!="" && !ModulesChecker::isRegistred($module)){
            $errors[] = Translate::sprint("Module is invalid!");
        }

        if(!isset($module_id)){
            $errors[] = Translate::sprint("The ID is not valid!");
        }


        if(!empty($errors)){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
        }


        $this->db->where("module",$module);
        $this->db->where("module_id",$module_id);

        $count = $this->db->count_all_results("gallery");


        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        $this->db->where("module",$module);
        $this->db->where("module_id",$module_id);

        $this->db->from("gallery");
        $this->db->order_by("_order","asc");
        $this->db->limit($pagination->getPer_page(),$pagination->getFirst_nbr());
        $gallery = $this->db->get();
        $gallery = $gallery->result_array();


        $new_gallery_results = array();
        foreach ($gallery as $key => $image){

            $img_id = $image["image_id"];

            $this->db->select("image");
            $this->db->where("id_image",$img_id);
            $img = $this->db->get("image",1);
            $img = $img->result();


            if(count($img)>0){

                $imgData = _openDir($img[0]->image);

                if(!empty($imgData)){
                    $new_gallery_results[] = $imgData;
                }else{
                    $count--;
                }

            }else{
                $count--;
            }

        }

        if(count($new_gallery_results)==0)
            $count = 0;


        return array(Tags::SUCCESS=>1,"pagination"=>$pagination,  Tags::COUNT=>$count,  Tags::RESULT=>$new_gallery_results);

    }

    public function saveGallery($module,$module_id,$images=array()){

        $errors = array();

        if($module=="store" OR $module=="offer" OR $module=="event"){

            $this->db->where("module_id",intval($module_id));
            $this->db->where("module",$module);
            $nbrfiles = $this->db->count_all_results("gallery");

            if($nbrfiles>=$this->maxfiles){

                $errors[] = Translate::sprintf("You have exceeded the maximum number of images (Max: %s)",array($this->maxfiles));

            }else if($module_id>0){


                $this->db->where("id_".$module,intval($module_id));
                $c = $this->db->count_all_results($module);

                if($c>0){

                    if(!empty($images)){

                        $object = array(
                            "module_id"    => $module_id,
                            "module"      => $module,
                        );

                        $ids = array();
                        $index = 0;
                        foreach ($images as $key => $value){
                            $index++;
                            if($nbrfiles>=$this->maxfiles){
                                break;
                            }

                            //get image object
                            $this->db->select("id_image");
                            $this->db->where("image",$value);
                            $img = $this->db->get("image",1);
                            $img = $img->result();

                            if(count($img)==0){
                                continue;
                            }

                            $this->db->where("image_id",$img[0]->id_image);
                            $this->db->where("module",$module);
                            $this->db->where("module_id",intval($module_id));
                            $i = $this->db->count_all_results("gallery");

                            if($i==0){

                                $object['_order'] = $index;
                                $object['image_id'] = intval($img[0]->id_image);
                                $object['updated_at'] = date("Y-m-d H:i:s",time());
                                $object['created_at'] = date("Y-m-d H:i:s",time());
                                $this->db->insert("gallery",$object);
                                $nbrfiles++;

                            }else{

                                $this->db->where('image_id',$img[0]->id_image);
                                $this->db->update("gallery",array(
                                    '_order' => $index
                                ));
                            }

                            $ids[] = intval($img[0]->id_image);

                        }

                        if(!empty($ids)){
                            $this->db->where_not_in('image_id', $ids);
                            $this->db->where('module', $module);
                            $this->db->where('module_id', $module_id);
                            $this->db->delete("gallery");
                        }else{
                            $this->db->where('module', $module);
                            $this->db->where('module_id', $module_id);
                            $this->db->delete("gallery");
                        }


                        return array(Tags::SUCCESS=>1);

                    }else{
                        $errors[] = Translate::sprint("You should select some images");
                    }

                }else{
                    $errors[] = Translate::sprintf("The %s is not exists!",array($module));
                }

            }else{
                $errors[] = Translate::sprint("The ID is not valid!");
            }

        }



        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    public function createTable(){

        if ($this->db->table_exists('gallery'))
            return;

        // create new table gallery
        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'module' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'module_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'module' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),
            'image_id' => array(
                'type' => 'INT',
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
        $this->dbforge->create_table('gallery', TRUE, $attributes);

    }

    public function updateFields(){

        if (!$this->db->field_exists('_order', 'gallery'))
        {
            $fields = array(
                '_order'  => array('type' => 'INT', 'after' => 'image_id','default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('gallery', $fields);
        }

        if ($this->db->field_exists('int_id', 'gallery')){
            $fields = array(
                'int_id' => array(
                    'name' => 'module_id',
                    'type' => 'INT',
                    'default' => NULL
                ),
            );
            $this->dbforge->modify_column('gallery', $fields);
        }

        if ($this->db->field_exists('type', 'gallery')){
            $fields = array(
                'type' => array(
                    'name' => 'module',
                    'type' => 'VARCHAR(30)',
                    'default' => NULL
                ),
            );
            $this->dbforge->modify_column('gallery', $fields);
        }

    }


}
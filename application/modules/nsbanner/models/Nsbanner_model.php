<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nsbanner_model extends CI_Model {


    public function __construct()
    {
        parent::__construct();
    }

    public function getAllActiveImages(){
        $result = array();
        $this->db->select('image,id');
        $value = $this->db->get('ns_banners');
        $value = $value->result();
        foreach ($value as $obj){
            $images = json_decode($obj->image,JSON_OBJECT_AS_ARRAY);
            if(count($images)>0){
                foreach ($images as $image){
                    $result[] = $image;
                }
            }
        }
        return $result;
    }

    public function getBanner($id)
    {

        $this->db->where('id',$id);
        $banner = $this->db->get('ns_banners',1);
        $banner = $banner->result_array();

        if(count($banner)>0){
            return $banner[0];
        }

        return NULL;
    }


    public function getBanners($params=array())
    {


        extract($params);
        $errors = array();
        $data = array();

        if(!isset($limit) OR (isset($limit) && intval($limit)==0)){
            $limit = 30;
        }


        if(!isset($page)){
            $page = 1;
        }


        if( isset($module) && $module != ""){
            $data ['module'] = $module;
        }


        if( isset($module_id) && $module_id != ""){
            $data ['module_id'] = $module_id;
        }

        if( isset($banner_id) && $banner_id > 0){
            $data ['id'] = intval($banner_id);
        }

        if( isset($status) && $status >= 0){
            $data ['status'] = $status;
        }

        $this->db->where($data);

        $this->db->from("ns_banners");
        $count = $this->db->count_all_results();


        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        $this->db->where($data);

        $this->db->from("ns_banners");
        $this->db->limit($pagination->getPer_page(),$pagination->getFirst_nbr());

        $this->db->order_by("created_at","DESC");

        $banners = $this->db->get();
        $banners = $banners->result_array();

        //prepare image
        foreach ($banners as $key => $banner) {

            if (isset($banner['image'])) {

                $images = (array)json_decode($banner['image']);

                $banners[$key]['image'] = array();
                // $new_stores_results[$key]['image'] = $store['images'];
                foreach ($images AS $k => $v) {
                    $banners[$key]['image'][] = _openDir($v);
                }

            } else {
                $banners[$key]['images'] = array();
            }


        }

        return array(Tags::SUCCESS=>1,"pagination"=>$pagination,  Tags::COUNT=>$count,  Tags::RESULT=>$banners);
    }


    function remove($id){

        $this->db->where('id',intval($id));
        $this->db->delete('ns_banners');

        return array(Tags::SUCCESS=> 1);
    }

    function changeStatus($id,$status){


        $this->db->where('id',intval($id));

        $this->db->update('ns_banners',array(
            'status' => intval($status),
            'updated_at' =>   date("Y-m-d H:i;s",time())
        ));


        return array(Tags::SUCCESS=> 1);
    }

    function add($params = array()){

        extract($params);

        $data = array();
        $errors = array();

        if (isset($image) and !is_array($image))
            $image = json_decode($image, JSON_OBJECT_AS_ARRAY);

        if (!empty($image)) {
            $data["image"] = array();
            $i = 0;
            try {
                if (!empty($image)) {
                    foreach ($image as $value) {
                        $data["image"][$i] = $value;
                        $i++;
                    }
                    $data["image"] = json_encode($data["image"], JSON_FORCE_OBJECT);
                }
            } catch (Exception $e) {

            }

        }

        if (isset($data["image"]) and empty($data["image"])) {
            $errors['image'] = Translate::sprint("Please upload an image");
        }

        //label
        if(isset($title) && $title != "")
            $data['title'] = $title;
        else
            $errors[] = Translate::sprint("title field is not valid!");


        if(isset($description) && $description != "")
            $data['description'] = $description;

        //module
        if(isset($module) && $module != "")
            $data['module'] = $module;
        else
            $errors[] = Translate::sprint("Module field is not valid!");


        if(isset($module_id) && $module_id != "")
            $data['module_id'] = $module_id;
        else
            $errors[] = Translate::sprint("Module ID field is empty!");


        if(isset($is_can_expire) and $is_can_expire==1){
            //auth
            if(isset($date_start) && $date_start != "")
                $data['date_start'] = $date_start;
            else
                $errors[] = Translate::sprint("Date_start field is not valid!");


            if(isset($date_end) && $date_end != "")
                $data['date_end'] = $date_end;
            else
                $errors[] = Translate::sprint("Date_end field is not valid!");
        }

        $data['status'] = 0;
        $data['updated_at'] = date("Y-m-d H:i;s",time());
        $data['created_at'] = date("Y-m-d H:i;s",time());


        if(empty($errors)){

            if(isset($data['module']) and $data['module']=="link"){

                if(!filter_var($data['module_id'],FILTER_VALIDATE_URL)){
                    $errors['link'] = Translate::sprint("Link is not valid");
                }

            }elseif(isset($module_id) && ModulesChecker::isEnabled($module) && $module_id > 0 )
                $data['module_id'] = intval($module_id);
            else
                $errors[] = Translate::sprint("Id field is not valid!");


        }

        if(empty($errors)){

            $this->db->insert('ns_banners',$data);
            $id = $this->db->insert_id();

            return array(Tags::SUCCESS=> 1,Tags::RESULT=>$id);
        }


        return array(Tags::SUCCESS=> 0,Tags::ERRORS=>$errors);
    }

    function edit($params = array()){

        extract($params);

        $data = array();
        $errors = array();

        //module
        if(isset($id) && $id > 0){

        }else{
            $errors[] = Translate::sprint("ID is missing");

        }


        if (isset($image) and !is_array($image))
            $image = json_decode($image, JSON_OBJECT_AS_ARRAY);

        if (!empty($image)) {
            $data["image"] = array();
            $i = 0;
            try {
                if (!empty($image)) {
                    foreach ($image as $value) {
                        $data["image"][$i] = $value;
                        $i++;
                    }
                    $data["image"] = json_encode($data["image"], JSON_FORCE_OBJECT);
                }
            } catch (Exception $e) {

            }

        }

        if (isset($data["image"]) and empty($data["image"])) {
            $errors['image'] = Translate::sprint("Please upload an image");
        }

        //label
        if(isset($title) && $title != "")
            $data['title'] = $title;
        else
            $errors[] = Translate::sprint("title field is not valid!");


        if(isset($description) && $description != "")
            $data['description'] = $description;

        //module
        if(isset($module) && $module != "")
            $data['module'] = $module;
        else
            $errors[] = Translate::sprint("Module field is not valid!");


        if(isset($module_id) && $module_id != "")
            $data['module_id'] = $module_id;
        else
            $errors[] = Translate::sprint("Module ID field is empty!");


        if(isset($is_can_expire) and $is_can_expire==1){
            //auth
            if(isset($date_start) && $date_start != "")
                $data['date_start'] = $date_start;
            else
                $errors[] = Translate::sprint("Date_start field is not valid!");


            if(isset($date_end) && $date_end != "")
                $data['date_end'] = $date_end;
            else
                $errors[] = Translate::sprint("Date_end field is not valid!");
        }

        $data['status'] = 0;
        $data['updated_at'] = date("Y-m-d H:i;s",time());
        $data['created_at'] = date("Y-m-d H:i;s",time());


        if(empty($errors)){

            if(isset($data['module']) and $data['module']=="link"){

                if(!filter_var($data['module_id'],FILTER_VALIDATE_URL)){
                    $errors['link'] = Translate::sprint("Link is not valid");
                }

            }elseif(isset($module_id) && ModulesChecker::isEnabled($module) && $module_id > 0 )
                $data['module_id'] = intval($module_id);
            else
                $errors[] = Translate::sprint("Id field is not valid!");


        }

        if(empty($errors)){

            $this->db->where('id',$id);
            $this->db->update('ns_banners',$data);

            return array(Tags::SUCCESS=> 1);
        }


        return array(Tags::SUCCESS=> 0,Tags::ERRORS=>$errors);
    }


    public function createTable(){


        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'title' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),

            'description' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'image' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),

            'module' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),

            'module_id' => array(
                'type' => 'VARCHAR(150)',
                'default' => NULL
            ),

            'status' => array(
                'type' => 'INT',
                'default' => NULL
            ),


            'date_start' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),

            'date_end' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),

            'is_can_expire' => array(
                'type' => 'INT',
                'default' => NULL
            ),


            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        );

        $this->dbforge->add_field($fields);
        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('ns_banners', TRUE, $attributes);



    }


}


<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */

class Bookmark_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();


    }


    public function clear_removed_cache(){

        $this->db->order_by("updated_at","ASC");
        $bookmarks = $this->db->get("bookmarks",500);
        $bookmarks = $bookmarks->result();

        foreach ($bookmarks as $b){


            if($b->module=="store"){

                $this->db->where("id_store",$b->module_id);
                $c = $this->db->count_all_results("store");

                if($c==0){
                    $this->db->where("id",$b->id);
                    $this->db->delete("bookmarks");
                }
            }

            if($b->module=="event"){

                $this->db->where("id_event",$b->module_id);
                $c = $this->db->count_all_results("event");

                if($c==0){
                    $this->db->where("id",$b->id);
                    $this->db->delete("bookmarks");
                }

            }

            if($b->module=="offer"){

                $this->db->where("id_offer",$b->module_id);
                $c = $this->db->count_all_results("offer");

                if($c==0){
                    $this->db->where("id",$b->id);
                    $this->db->delete("bookmarks");
                }

            }

            $this->db->where("id",$b->id);
            $this->db->update("bookmarks",array(
                'updated_at'=>date('Y-m-d H:i:s',time())
            ));

        }



    }

    public function getList($params=array())
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

        if( isset($user_id) && $user_id >0){
            $data ['user_id'] = $user_id;
        }

        if( isset($guest_id) && $guest_id >0){
            $data ['guest_id'] = $guest_id;
        }

        if( isset($module) && $module != ""){
            $data ['module'] = $module;
        }

        if( isset($module_id) && $module_id != ""){
            $data ['module_id'] = $module_id;
        }


        $this->db->where($data);

        $this->db->from("bookmarks");
        $count = $this->db->count_all_results();


        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        $this->db->where($data);

        $this->db->from("bookmarks");
        $this->db->limit($pagination->getPer_page(),$pagination->getFirst_nbr());

        $this->db->order_by("created_at","DESC");

        $bookmarks = $this->db->get();
        $bookmarks = $bookmarks->result_array();

        //prepare image
        foreach ($bookmarks as $key => $bookmark) {

            if (isset($notification['image'])) {

                $images = (array)json_decode($bookmark['image']);

                $bookmarks[$key]['image'] = array();
                // $new_stores_results[$key]['image'] = $store['images'];
                foreach ($images AS $k => $v) {
                    $bookmarks[$key]['image'][] = _openDir($v);
                }

            } else {
                $bookmarks[$key]['images'] = array();
            }


        }

        return array(Tags::SUCCESS=>1,"pagination"=>$pagination,  Tags::COUNT=>$count,  Tags::RESULT=>$bookmarks);
    }



    function remove($params = array()){

        extract($params);

        $data = array();
        $errors = array();


        if(isset($module_id) && $module_id > 0)
            $data['module_id'] = intval($module_id);
        else
            $errors[] = _lang("Module ID is not exists");

        if(isset($module) && $module != "")
            $data['module'] = $module;
        else
            $errors[] = _lang("Module is not exists");


        if(isset($user_id) && $user_id > 0)
            $data['user_id'] = intval($user_id);
        else
            $errors[] = _lang("User ID is not exists");


        if(isset($guest_id) && $guest_id > 0)
            $data['guest_id'] = intval($guest_id);

        if(!empty($data)){

            $this->db->where($data);
            $this->db->delete('bookmarks');

            return array(Tags::SUCCESS=> 1);
        }


        return array(Tags::SUCCESS=> 0,Tags::ERRORS=>array("err"=>Translate::sprint("Something wrong")));
    }


    function exist($params = array()){

        extract($params);

        $data = array();

        if(isset($module_id) && $module_id > 0)
            $data['module_id'] = intval($module_id);

        if(isset($module) && $module != "")
            $data['module'] = $module;


        if(isset($user_id) && $user_id > 0)
            $data['user_id'] = intval($user_id);


        if(isset($guest_id) && $guest_id > 0)
            $data['guest_id'] = intval($guest_id);


        if(!empty($data)){

            $this->db->where($data);
            $count = $this->db->count_all_results('bookmarks');

            if($count>0)
                return TRUE;

        }


       return FALSE;
    }

    function add($params = array()){

        extract($params);

        $data = array();
        $errors = array();


        if(isset($module_id) && $module_id > 0)
            $data['module_id'] = intval($module_id);
        else
            $errors[] = Translate::sprint("Id field is not valid!");

        if(isset($module) && $module != "")
            $data['module'] = $module;
        else
            $errors[] = Translate::sprint("Module field is not valid!");


        if(isset($user_id) && $user_id > 0)
            $data['user_id'] = intval($user_id);
        else
            $errors[] = Translate::sprint("User ID field is not valid!");

        //check user existing
        if(empty($errors)){

            $this->db->where('id_user', $data['user_id']);
            $count = $this->db->count_all_results('user');

            if ($count == 0){
                $errors[] = Translate::sprint("User ID field is not valid! #2");
            }

        }


        if(isset($guest_id) && $guest_id > 0)
            $data['guest_id'] = intval($guest_id);



        $data['updated_at'] = date("Y-m-d H:i;s",time());
        $data['created_at'] = date("Y-m-d H:i;s",time());


        if(empty($errors)){

            $this->db->where($data);
            $count = $this->db->count_all_results("bookmarks");
            if($count > 0)
                return array(Tags::SUCCESS=> 1);

            $this->db->insert('bookmarks',$data);
            $id = $this->db->insert_id();

            return array(Tags::SUCCESS=> 1,Tags::RESULT=>$id);
        }


        return array(Tags::SUCCESS=> 0,Tags::ERRORS=>$errors);
    }


    function createBookmarksTable(){


        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'module_id' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'module' => array(
                'type' => 'VARCHAR(50)',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'status' => array(
                'type' => 'INT',
                'default' => 0
            ),
            'guest_id' => array(
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
        $this->dbforge->create_table('bookmarks', TRUE, $attributes);



    }

    public function update_fields(){

        if (!$this->db->field_exists('status', 'bookmarks')) {
            $fields = array(
                'status' => array('type' => 'INT', 'after' => 'user_id', 'default' => 0),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('bookmarks', $fields);
        }

    }

}
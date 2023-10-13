<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Notification_history_model extends CI_Model {



    public function __construct()
    {
        parent::__construct();
    }


    public function clear_removed_cache(){

        $this->db->order_by("updated_at","ASC");
        $list = $this->db->get("nsh_notifications",500);
        $list = $list->result();

        foreach ($list as $b){


            if($b->module=="store"){

                $this->db->where("id_store",$b->module_id);
                $c = $this->db->count_all_results("store");

                if($c==0){
                    $this->db->where("id",$b->id);
                    $this->db->delete("nsh_notifications");
                }
            }

            if($b->module=="event"){

                $this->db->where("id_event",$b->module_id);
                $c = $this->db->count_all_results("event");

                if($c==0){
                    $this->db->where("id",$b->id);
                    $this->db->delete("nsh_notifications");
                }

            }

            if($b->module=="offer"){

                $this->db->where("id_offer",$b->module_id);
                $c = $this->db->count_all_results("offer");

                if($c==0){
                    $this->db->where("id",$b->id);
                    $this->db->delete("nsh_notifications");
                }

            }


            $this->db->where("id",$b->id);
            $this->db->update("nsh_notifications",array(
                'updated_at'=>date('Y-m-d H:i:s',time())
            ));

        }



    }

    public function getCount($params=array())
    {

        extract($params);
        $errors = array();


        if(isset($guest_id) && $guest_id==0){
            return array(Tags::SUCCESS=>1,Tags::COUNT=>0,Tags::RESULT=>0);
        }

        if(isset($user_id) && $user_id>0 && isset($guest_id) && $guest_id>0 ){

            $where['user_id'] = $user_id;
            $where['guest_id'] = $guest_id;

        }else{

            if( isset($auth_type) && $auth_type!= ""){
                $data ['auth_type'] = $auth_type;
            }

            if( isset($auth_id) && $auth_id >=0){
                $data ['auth_id'] = $auth_id;
            }
        }



        if( isset($module) && $module != ""){
            $data ['module'] = $module;
        }


        if( isset($module_id) && $module_id != ""){
            $data ['module_id'] = $module_id;
        }


        if( isset($status) && $status >= 0){
            $data ['status'] = intval($status);
        }



        if(!empty($where)){
            $this->db->where("((auth_type='guest' && auth_id=". $where['guest_id'].") 
            OR (auth_type='user' && auth_id=". $where['user_id']."))",NULL,TRUE);
        }

        $this->db->where($data);
        $count = $this->db->count_all_results("nsh_notifications");

        return array(Tags::SUCCESS=>1,Tags::COUNT=>$count,Tags::RESULT=>$count);
    }


    public function getNotification($id)
    {

        $this->db->where('id',$id);
        $notification = $this->db->get('nsh_notifications',1);
        $notification = $notification->result_array();

        if(count($notification)>0){
            return $notification[0];
        }

        return NULL;
    }


    public function getNotifications($params=array())
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

        if(isset($guest_id) && $guest_id==0){
            return array(Tags::SUCCESS=>1,  Tags::COUNT=>0,  Tags::RESULT=>array());
        }

        $where = array();
        if(isset($user_id) && $user_id>0 && isset($guest_id) && $guest_id>0 ){

            $where['user_id'] = $user_id;
            $where['guest_id'] = $guest_id;

        }else{

            if( isset($auth_type) && $auth_type!= ""){
                $data ['auth_type'] = $auth_type;
            }

            if( isset($auth_id) && $auth_id >= 0){
                $data ['auth_id'] = $auth_id;
            }
        }


        if( isset($module) && $module != ""){
            $data ['module'] = $module;
        }


        if( isset($module_id) && $module_id != ""){
            $data ['module_id'] = $module_id;
        }


        if( isset($status) && $status >= 0){
            $data ['status'] = $status;
        }

        if(!empty($where)){
            $this->db->where("((auth_type='guest' && auth_id=". $where['guest_id'].") 
            OR (auth_type='user' && auth_id=". $where['user_id']."))",NULL,TRUE);
        }

        $this->db->where($data);

        $this->db->from("nsh_notifications");
        $count = $this->db->count_all_results();




        $pagination = new Pagination();
        $pagination->setCount($count);
        $pagination->setCurrent_page($page);
        $pagination->setPer_page($limit);
        $pagination->calcul();


        if(!empty($where)){
            $this->db->where("((auth_type='guest' && auth_id=". $where['guest_id'].") 
            OR (auth_type='user' && auth_id=". $where['user_id']."))",NULL,TRUE);
        }

        $this->db->where($data);

        $this->db->from("nsh_notifications");
        $this->db->limit($pagination->getPer_page(),$pagination->getFirst_nbr());


        $this->db->order_by("created_at","DESC");

        $notifications = $this->db->get();
        $notifications = $notifications->result_array();

        //prepare image
        foreach ($notifications as $key => $notification) {

            if (isset($notification['image'])) {

                $images = (array)json_decode($notification['image']);

                $notifications[$key]['image'] = array();
                foreach ($images AS $k => $v) {
                    $img = _openDir($v);
                    if(!empty($img))
                        $notifications[$key]['image'][] = $img;
                }

            } else {
                $notifications[$key]['images'] = array();
            }


        }

        return array(Tags::SUCCESS=>1,"pagination"=>$pagination,  Tags::COUNT=>$count,  Tags::RESULT=>$notifications);
    }



    function remove($id){

        $this->db->where('id',intval($id));
        $this->db->delete('nsh_notifications');

        return array(Tags::SUCCESS=> 1);
    }

    function changeStatus($id,$status){


        $this->db->where('id',intval($id));

        $this->db->update('nsh_notifications',array(
            'status' => intval($status),
            'updated_at' =>   date("Y-m-d H:i;s",time())
        ));


        return array(Tags::SUCCESS=> 1);
    }

    function add($params = array()){

        extract($params);

        $data = array();
        $errors = array();

        //label
        if(isset($label) && $label != "")
            $data['label'] = $label;
        else
            $errors[] = Translate::sprint("label field is not valid!");


        if(isset($label_description) && $label_description != "")
            $data['label_description'] = $label_description;

        //module
        if(isset($module) && $module != "")
            $data['module'] = $module;
        else
            $errors[] = Translate::sprint("Module field is not valid!");


        if(isset($module_id) && $module_id > 0)
            $data['module_id'] = intval($module_id);
        else
            $errors[] = Translate::sprint("Id field is not valid!");


        //auth
        if(isset($auth_type) && $auth_type != "")
            $data['auth_type'] = $auth_type;
        else
            $errors[] = Translate::sprint("Auth_type field is not valid!");


        if(isset($auth_id) && $auth_id > 0)
            $data['auth_id'] = intval($auth_id);
        else
            $errors[] = Translate::sprint("Auth_id field is not valid!");


        if(isset($detail) && $detail != "")
            $data['detail'] = Text::input($detail);

        if(isset($image) && $image != "")
            $data['image'] = $image;


        $data['status'] = 0;

        $data['updated_at'] = date("Y-m-d H:i;s",time());
        $data['created_at'] = date("Y-m-d H:i;s",time());

        if(empty($errors)){

            $this->db->insert('nsh_notifications',$data);
            $id = $this->db->insert_id();


            return array(Tags::SUCCESS=> 1,Tags::RESULT=>$id);
        }


        return array(Tags::SUCCESS=> 0,Tags::ERRORS=>$errors);
    }


    function refresh($params = array()){

        extract($params);

        $data = array();
        $errors = array();

        //label
        if(isset($label) && $label != "")
            $data['label'] = $label;
        else
            $errors[] = Translate::sprint("label field is not valid!");


        if(isset($label_description) && $label_description != "")
            $data['label_description'] = $label_description;

        //module
        if(isset($module) && $module != "")
            $data['module'] = $module;
        else
            $errors[] = Translate::sprint("Module field is not valid!");


        if(isset($module_id) && $module_id > 0)
            $data['module_id'] = intval($module_id);
        else
            $errors[] = Translate::sprint("Id field is not valid!");


        //auth
        if(isset($auth_type) && $auth_type != "")
            $data['auth_type'] = $auth_type;
        else
            $errors[] = Translate::sprint("Auth_type field is not valid!");


        if(isset($auth_id) && $auth_id > 0)
            $data['auth_id'] = intval($auth_id);
        else
            $errors[] = Translate::sprint("Auth_id field is not valid!");


        if(isset($detail) && $detail != "")
            $data['detail'] = Text::input($detail);

        if(isset($image) && $image != "")
            $data['image'] = $image;


        $data['status'] = 0;

        $data['updated_at'] = date("Y-m-d H:i;s",time());
        $data['created_at'] = date("Y-m-d H:i;s",time());

        if(empty($errors)){

            $this->db->where('module', $data['module']);
            $this->db->where('module_id', $data['module_id']);
            $n = $this->db->get("nsh_notifications",1);
            $n = $n->result();

            if(count($n)==0){
                $this->db->insert('nsh_notifications',$data);
                $id = $this->db->insert_id();
            }else{

                unset( $data['created_at']);

                $this->db->where('id',$n[0]->id);
                $this->db->update('nsh_notifications',$data);
                $id = $n[0]->id;
            }

            return array(Tags::SUCCESS=> 1,Tags::RESULT=>$id);
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

            'label' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),

            'label_description' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),


            'image' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),

            'auth_type' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'auth_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),


            'module' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'module_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'detail' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'status' => array(
                'type' => 'INT', //unread (0) - read (1) - removed (-1)
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
        $this->dbforge->create_table('nsh_notifications', TRUE, $attributes);



    }


}


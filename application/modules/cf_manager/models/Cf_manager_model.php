<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cf_manager_model extends CI_Model {


    public $fields_schema = array(
        "textarea" => array(
            "types"=>array("longtext"),
        ),
        "input" => array(
            "types"=>array(
                "email","phone","number","text","location","date","time"
            ),
        ),
    );

    /**
     * @return array
     */
    public function getFieldsSchema()
    {
        return $this->fields_schema;
    }


    public function delete($id,$user_id){

        $this->db->where("cf_id",$id);
        $count = $this->db->count_all_results("category");

        if($count>0){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("This custom fields is linked with other module")));
        }

        $this->db->where("id",$id);
        $this->db->where("editable",0);
        $count = $this->db->count_all_results("cf_list");

        if($count>0){
            return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("Couldn't remove this default custom fields")));
        }

        $this->db->where("id",$id);
        $this->db->where("user_id",$user_id);
        $this->db->delete("cf_list");

        return array(Tags::SUCCESS=>1);

    }


    public function getList0($id){

        $this->db->where("id",$id);
        $cf = $this->db->get("cf_list",1);
        $cf = $cf->result_array();

        if(isset($cf[0]))
            return array(Tags::SUCCESS=>1,Tags::RESULT=> $cf,Tags::COUNT=>1);

        return  array(Tags::SUCCESS=>0);

    }

    public function getCF($id){

        $this->db->where("id",$id);
        $cf = $this->db->get("cf_list",1);
        $cf = $cf->result_array();

        if(isset($cf[0]))
            return $cf[0];

        return NULL;

    }

    public function get($id,$user_id){

        $this->db->where("id",$id);
        $cf = $this->db->get("cf_list",1);
        $cf = $cf->result_array();

        if(isset($cf[0]))
            return $cf[0];

        return NULL;

    }

    public function getList($user_id){

        $this->db->where("editable",1);
        $this->db->order_by("id","desc");
        $cf = $this->db->get("cf_list");
        return $cf->result_array();

    }

    public function getByName($key){

        $this->db->where('label',$key);
        $cf = $this->db->get('cf_list',1);
        $cf = $cf->result_array();

        if(isset($cf[0]))
            return $cf[0];

        return NULL;
    }

    public function createCustomFields($param=array()){

        extract($param);
        $errors = array();

        if(isset($user_id) && $user_id>0){
            $data['user_id'] = intval($user_id);
        }else
            $errors[] = _lang("User ID is not valid");


        if(isset($label) && $label!=""){
            $data['label'] = trim($label);
        }else
            $errors[] = _lang("Please insert label");

        if(isset($default) && $default!=""){
            $data['default_value'] = trim($default);
        }else
            $data['default_value'] = "";


        if(isset($fields) && $fields!=""){

            if (!is_array($fields))
                $fields = json_decode($fields,JSON_OBJECT_AS_ARRAY);

            $result = $this->mCFManager->validateFields($fields);

            if($result[Tags::SUCCESS]==0)
                $errors =  $result[Tags::ERRORS];
            else{
                $data['fields'] = json_encode($result[Tags::RESULT]);
            }

        }

        if(empty($errors)){

            $data["created_at"] = date("Y-m-d H:i:s",time());
            $data["updated_at"] = date("Y-m-d H:i:s",time());
            $data["editable"] = 1;

            $this->db->insert("cf_list",$data);

            return array(Tags::SUCCESS=>1);
        }

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }


    public function editCustomFields($param=array()){

        extract($param);
        $errors = array();


        if(isset($id) && $id>0){
            $data['id'] = intval($id);
        }else
            $errors[] = _lang("ID is not valid");


        if(empty($errors)){
            $this->db->where("id",intval($id));
            $this->db->where("editable",0);
            $count = $this->db->count_all_results("cf_list");
            if($count>0){
                return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("Couldn't edit the default custom fields")));
            }
        }


        if(isset($user_id) && $user_id>0){
            $data['user_id'] = intval($user_id);
        }else
            $errors[] = _lang("User ID is not valid");


        if(isset($label) && $label!=""){
            $data['label'] = trim($label);
        }else
            $errors[] = _lang("Please insert label");




        if(isset($fields) && $fields!=""){

            if (!is_array($fields))
                $fields = json_decode($fields,JSON_OBJECT_AS_ARRAY);

            $result = $this->validateFields($fields);

            if($result[Tags::SUCCESS]==0)
                $errors =  $result[Tags::ERRORS];
            else{
               $data['fields'] = json_encode($result[Tags::RESULT]);
            }

        }


        if(empty($errors)){

            $data["created_at"] = date("Y-m-d H:i:s",time());
            $data["updated_at"] = date("Y-m-d H:i:s",time());

            $this->db->where('id',$data['id'] );
            $this->db->update("cf_list",$data);

            return array(Tags::SUCCESS=>1);
        }

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    public function validateFields($fields){

        $errors = array();


        $data = array();

        $labels_to_check = array();

        foreach ($fields as $key => $field){

            if(isset($field['type']) && preg_match("#.#",$field['type'])){

                if(!isset($field['label']) OR $field['label']==""){
                    $errors[] = Translate::sprintf("Field label is not valid");
                    break;
                }


                if(in_array($field['label'],$labels_to_check)){
                    $errors[] = Translate::sprintf("Field label of (%s) is already exists",array($field['label']));
                    break;
                }

                $labels_to_check[] = $field['label'];

                $field['type'] = explode(".",$field['type']);

                if(count($field['type'])!=2
                    OR (!isset($this->fields_schema[$field['type'][0]])
                OR !in_array($field['type'][1], $this->fields_schema[ $field['type'][0] ]["types"] )    ) ){
                    $errors[] = Translate::sprintf("Field type of (%s) is not valid - line %s",array($field['label'],$key));
                    break;
                }

                if(!isset($field['order']) OR $field['order']==0){
                    $errors[] = Translate::sprintf("Field order is not valid - line %s",array($key));
                    break;
                }

                if(!isset($field['step']) OR $field['step']==0){
                    $errors[] = Translate::sprintf("Field step is not valid - line %s",array($key));
                    break;
                }


                $field['type'] = implode(".",$field['type']);


            }else{
                $errors[] = Translate::sprintf("Field type is not defined - line %s",array($key));
            }

            $field['id'] = parse_cf_string($field['label']);

            $data[] = $field;
        }


        if(empty($errors))
            return array(Tags::SUCCESS=>1,Tags::RESULT=>$data);

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }

    public function create_default_cf(){

        $count = $this->db->count_all_results("cf_list");

        if($count==0){


            $this->db->where('manager',1);
            $user = $this->db->get('user',1);
            $user = $user->result();

            if(!isset($user[0]))
                return;

            $this->db->insert('cf_list',array(
                'label'=>'default_fields',
                'fields'=>'[{"type":"input.location","label":"Address","required":"1","order":"1","step":"1"},{"type":"input.number","label":"Phone Number","required":"0","order":"2","step":"1"}]',
                'user_id'=> $user[0]->id_user,
                'created_at'=>date('Y-m-d h:i:s',time()),
                'updated_at'=>date('Y-m-d h:i:s',time()),
            ));



        }

    }

    public function createCFTable(){


        $this->load->dbforge();

        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),

            'label' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),

            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'app_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),

            'fields' => array(
                'type' => 'TEXT',
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
        $this->dbforge->create_table('cf_list', TRUE, $attributes);



    }

    public function updateFields()
    {

        $this->load->dbforge();

        if (!$this->db->field_exists('editable', 'cf_list')) {
            $fields = array(
                'editable' => array('type' => 'INT', 'default' => 1),
            );
            $this->dbforge->add_column('cf_list', $fields);
        }

        if (!$this->db->field_exists('default_value', 'cf_list')) {
            $fields = array(
                'default_value' => array('type' => 'VARCHAR(100)', 'default' => ""),
            );
            $this->dbforge->add_column('cf_list', $fields);
        }


    }


}


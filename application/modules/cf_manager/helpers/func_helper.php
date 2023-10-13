<?php


class CFManagerHelper{


    public static function getTypeByID($cf_id, $label){

        $ctx = &get_instance();
        $ctx->db->where('id',$cf_id);
        $field = $ctx->db->get("cf_list",1);
        $field = $field->result_array();



        if(isset($field[0])){

            $fields = $field[0]['fields'];
            $fields = json_decode($fields,JSON_OBJECT_AS_ARRAY);
            foreach ($fields as $k => $value){
                if(isset($value['label']) && $value['label']==$label){
                    return $value['type'];
                }
            }
        }

        return "";
    }

    public static function getFieldByID($cf_id, $label){

        $ctx = &get_instance();
        $ctx->db->where('id',$cf_id);
        $field = $ctx->db->get("cf_list",1);
        $field = $field->result_array();



        if(isset($field[0])){

            $fields = $field[0]['fields'];
            $fields = json_decode($fields,JSON_OBJECT_AS_ARRAY);
            foreach ($fields as $k => $value){
                if(isset($value['label']) && $value['label']==$label){
                    return $value;
                }
            }
        }

        return NULL;
    }

    public static function getByID($cf_id){

        $ctx = &get_instance();
        $ctx->db->where('id',$cf_id);
        $field = $ctx->db->get("cf_list",1);
        $field = $field->result_array();

        if(isset($field[0])){
            return $field[0];
        }

        return NULL;
    }


    public static function re_order($array,$fields){

        $data = array();

        foreach ($fields as $field){
            $data[ $field['order'] ] = $array[ $field['label'] ];
        }

        return $data;
    }




}

function parse_cf_string($str, $delimiter = '_'){
    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
    return $slug;
}
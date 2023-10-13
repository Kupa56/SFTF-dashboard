<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Tax_model extends CI_Model
{


    public function __construct()
    {
        parent::__construct();
    }


    public function getTax($id){

        $this->db->where('id',$id);
        $tax = $this->db->get('taxes',1);
        $tax = $tax->result_array();

        if(isset($tax[0]))
            return $tax[0];

        return NULL;
    }

    public function getDefaultTax(){

        $this->db->where('id',DEFAULT_TAX);
        $tax = $this->db->get('taxes',1);
        $tax = $tax->result_array();

        if(isset($tax[0]))
            return $tax[0];

        return NULL;
    }


    public function getTaxes(){

        $taxes = $this->db->get('taxes');
        $taxes = $taxes->result_array();

        return $taxes;
    }

    public function addTax($params =array()){

        $errors = array();
        $data = array();

        extract($params);

        if(isset($name) and $name!="")
            $data['name'] = Text::input($name);
        else
            $errors[] = Translate::sprint('The name of the tax is not mentioned');


        if(isset($value) and $value>0)
            $data['value'] = doubleval($value);
        else
            $errors[] = Translate::sprint('The value of the tax is not mentioned');


        if(empty($errors)){

            $data['created_at'] = date("Y-m-d H:i:s",time());
            $data['updated_at'] = date("Y-m-d H:i:s",time());

            $this->db->insert('taxes',$data);
            return array(Tags::SUCCESS=>1);
        }

        return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
    }


    public function create_table()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'value' => array(
                'type' => 'DOUBLE',
                'default' => NULL
            ),
            'name' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('taxes', TRUE, $attributes);


    }



}
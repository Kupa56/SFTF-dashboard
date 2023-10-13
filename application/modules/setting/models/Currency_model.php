<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Currency_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getAllCurrencies(){

        $currencies = $this->db->get('currencies');
        $currencies = $currencies->result_array();

        return $currencies;
    }


    public function addNewCurrency($data=array()){


        if(isset( $data["symbol_currency"]))
            $symbol = $data["symbol_currency"];
        else
            $symbol = '';

        if(isset($data["name_currency"]))
            $name = $data["name_currency"];
        else
            $name = '';

        if(isset($data["code_currency"]))
            $code = $data["code_currency"];
        else
            $code = '';

        if(isset($data["format_currency"]))
            $format = $data["format_currency"];
        else
            $format = 1;

        if(isset($data["rate_currency"]))
            $rate = doubleval($data["rate_currency"]);
        else
            $rate = 0;


        if(isset($data["cfd"]))
            $cfd = $data["cfd"];
        else
            $cfd = 0;

        if(isset($data["cdp"]))
            $cdp = $data["cdp"];
        else
            $cdp = 0;

        if(isset($data["cts"]))
            $cts = $data["cts"];
        else
            $cts = 0;


        if($code!="" and $name!=""){

            $currency =  array(
                'symbol' => $symbol,
                'format' => intval($format),
                'rate' => $rate,
                'name' => $name,
                'code' => $code,

                'cfd' => $cfd,
                'cdp' => $cdp,
                'cts' => $cts,
            );

            $this->db->where('code',$code);
            $c = $this->db->count_all_results('currencies');

            if($c==0){
                $this->db->where('code',$code);
                $this->db->insert('currencies',$currency);
            }

            return array(Tags::SUCCESS=>1);
        }

        return array(Tags::SUCCESS=>0);
    }

    public function editCurrency($data=array()){

        if(isset( $data["symbol_currency"]))
            $symbol = $data["symbol_currency"];
        else
            $symbol = '';

        if(isset($data["name_currency"]))
            $name = $data["name_currency"];
        else
            $name = '';

        if(isset($data["code_currency"]))
            $code = $data["code_currency"];
        else
            $code = '';

        if(isset($data["format_currency"]))
            $format = $data["format_currency"];
        else
            $format = 1;

        if(isset($data["rate_currency"]))
            $rate = $data["rate_currency"];
        else
            $rate = 0;


        if(isset($data["cfd"]))
            $cfd = $data["cfd"];
        else
            $cfd = 0;

        if(isset($data["cdp"]))
            $cdp = $data["cdp"];
        else
            $cdp = 0;

        if(isset($data["cts"]))
            $cts = $data["cts"];
        else
            $cts = 0;

        if($code!="" and $name!=""){

            $currency =  array(
                'symbol' => $symbol,
                'format' => intval($format),
                'rate' => $rate,
                'name' => $name,

                'cfd' => $cfd,
                'cdp' => $cdp,
                'cts' => $cts,
            );

            $this->db->where('code',$code);
            $this->db->update('currencies',$currency);

            return array(Tags::SUCCESS=>1);
        }

        return array(Tags::SUCCESS=>0);
    }

    public function deleteCurrency($code)
    {

        if (isset($code) and !empty($code)) {
            foreach ($code as $key => $value) {
                $this->db->where('code', $value);
                $this->db->delete('currencies');

                return array(Tags::SUCCESS => 1);
            }

            return array(Tags::SUCCESS => 0);
        }
    }

    public function getCurrency($code){

        if($code!="" ){

            $this->db->where('code',$code);
            $currency = $this->db->get('currencies',1);
            $currency = $currency->result_array();

            if(isset($currency[0]))
                return $currency[0];
        }

        NULL;
    }




    public function initCurrencies()
    {


        if (!$this->db->table_exists('app_config') )
            return;

        $this->createTable();

        //load currencies from json
        $path = AdminTemplateManager::assets("setting","currencies.json");
        $json = MyCurl::get($path);

        $currencies = json_decode($json,JSON_OBJECT_AS_ARRAY);

        foreach ($currencies as $key => $currency){

            $this->db->where("code",$currency['code']);
            $count = $this->db->count_all_results("currencies");

            if($count==0){
                $data =  array(
                    'code' => $currency['code'],
                    'symbol' => $currency['symbol'],
                    'format' => 2,
                    'rate' => 0,
                    'name' => $currency['name'],
                    'created_at' => date("Y-m-d H:i:s",time()),
                    'updated_at' => date("Y-m-d H:i:s",time()),
                );
                $this->db->insert('currencies',$data);
            }

        }

    }


    public function updateFields(){

        if (!$this->db->field_exists('cfd', 'currencies')) {
            $fields = array(
                'cfd' => array('type' => 'VARCHAR(100)', 'default' => "2"),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('currencies', $fields);
        }

        if (!$this->db->field_exists('cdp', 'currencies')) {
            $fields = array(
                'cdp' => array('type' => 'VARCHAR(100)', 'default' => "."),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('currencies', $fields);
        }

        if (!$this->db->field_exists('cts', 'currencies')) {
            $fields = array(
                'cts' => array('type' => 'VARCHAR(100)', 'default' => ","),
            );
            // modify_column : The usage of this method is identical to add_column(), except it alters an existing column rather than adding a new one.
            $this->dbforge->add_column('currencies', $fields);
        }


    }

    private function createTable(){

        //create table if needed

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            'code' => array(
                'type' => 'VARCHAR(10)',
                'default' => NULL
            ),
            'symbol' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),
            'name' => array(
                'type' => 'VARCHAR(60)',
                'default' => NULL
            ),
            'format' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'rate' => array(
                'type' => 'DOUBLE',
                'default' => 0
            ),
            'updated_at' => array(
                'type' => 'DATETIME'
            ),
            'created_at' => array(
                'type' => 'DATETIME'
            ),
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('currencies', TRUE, $attributes);


    }


}


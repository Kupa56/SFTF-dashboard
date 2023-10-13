<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nstranslator_model extends CI_Model {

    public function setup(){

        $languages = Translate::loadJSONs();

        foreach ($languages as $lang){
            $langCode = $lang['lang'];

            if($this->exist($langCode))
                continue;

            $langData = Translate::loadLanguageFromCache($langCode);
            $this->insertAll($langCode, $langData);

        }

    }

    public function exist($code){
        $this->db->where('lang',$code);
        return !($this->db->count_all_results('translate') == 0);
    }
    public function insertAll($lang,$list){

        foreach ($list as $key => $value){
            if($key != "config" && !is_array($value))
                $this->db->insert("translate",array(
                    'lang'  => $lang,
                    '_key'  => $key,
                    '_value'  => $value,
                    'created_at' => date("Y-m-d H:i:s",time()),
                    'updated_at' => date("Y-m-d H:i:s",time()),
                ));
            else
                $this->db->insert("translate",array(
                    'lang'  => $lang,
                    '_key'  => "_config",
                    '_value'  => json_encode($value),
                    'created_at' => date("Y-m-d H:i:s",time()),
                    'updated_at' => date("Y-m-d H:i:s",time()),
                ));
        }
    }

    public function createTable(){

        if ($this->db->table_exists('translate') )
            return;

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            '_key' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            '_value' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            'lang' => array(
                'type' => 'VARCHAR(3)',
                'default' => NULL
            ),
            'module' => array(
                'type' => 'VARCHAR(60)',
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
        $this->dbforge->create_table('translate', TRUE, $attributes);


    }

    
  
}


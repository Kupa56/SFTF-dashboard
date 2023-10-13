<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Config_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    private static $cache_parameters = array();

    public function get($key = NULL)
    {

        if (empty(self::$cache_parameters))
            self::$cache_parameters = $this->getParams();

        if ($key == NULL) {
            return self::$cache_parameters;
        } else if (isset(self::$cache_parameters[$key])) {
            return self::$cache_parameters[$key];
        }

        return NULL;
    }


    public function defined($key = NULL)
    {

        if (empty(self::$cache_parameters))
            self::$cache_parameters = $this->getParams();

        if(isset(self::$cache_parameters[$key])){
            return TRUE;
        }

        return FALSE;
    }

    //save settings
    public function save($key, $value)
    {

        if (!$this->db->table_exists('app_config'))
            return true;

        $type = 'N/A';

        if (is_array($value)) {
            $value = json_encode($value, JSON_FORCE_OBJECT);
            $type = 'json';
        } else if (is_numeric($value)) {

            $value = Text::strToNumber($value);

            if (is_float($value)) {
                $value = floatval($value);
                $type = 'float';
            } else if (is_integer($value)) {
                $value = intval($value);
                $type = 'int';
            } else if (is_double('double')) {
                $value = doubleval($value);
                $type = 'double';
            }

        } else if ($value == 'true' or $value == 'false' or is_bool($value)) {

            if ($value == 'true')
                $value = 1;
            else if ($value == 'false')
                $value = 0;
            else
                if (function_exists('boolval')) {
                    $value = boolval($value);
                }


            $type = 'boolean';

        } else {
            $type = 'string';
        }

        $key = Text::input($key);


        $this->db->where('_key', $key);
        $c = $this->db->count_all_results('app_config');

        if ($c == 0) {

            $this->db->insert('app_config', array(
                    '_key' => $key,
                    'value' => $value,
                    '_type' => $type,
                    'is_verified' => 1,
                    '_version' => APP_VERSION,

                    'updated_at' => date("Y-m-d H-i-s"),
                    'created_at' => date("Y-m-d H-i-s"),
                )
            );

        } else {

            $this->db->where('_key', $key);
            $this->db->update('app_config', array(
                'value' => $value,
                '_type' => $type
            ));
        }

        return TRUE;

    }

    public function getAppConfig()
    {

        if (!$this->db->table_exists('app_config'))
            return array();

        $params = array();
        $config = $this->db->get('app_config');
        $config = $config->result_array();

        //add currency
        $config[] = array(
            'id' => 9999999,
            '_key' => "CURRENCY_OBJECT",
            'value' => json_encode($this->mCurrencyModel->getCurrency(DEFAULT_CURRENCY),JSON_FORCE_OBJECT),
            '_type' => "string",
            'is_verified' => 1,
            'updated_at' => date("Y-m-d H:i:s",time()),
            'created_at' => date("Y-m-d H:i:s",time()),
        );

        return $config;
    }

    public function removeConfig($key)
    {

        $this->db->where('_key', $key);
        $this->db->delete('app_config');

    }


    public function saveAppConfig($data = array())
    {

        $skip = array(
            'CURRENCIES',
            'HASLINKER',
            'FILE',
            'ANDROID_API',
            'IOS_API'
        );

        foreach ($skip as $key) {
            if (isset($data[$key]))
                unset($data[$key]);
        }


        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->save($key, $value);
            }
        }

        return array(Tags::SUCCESS => 1);
    }


    public function getParams()
    {

        if (!$this->db->table_exists('app_config'))
            return array();

        $params = array();
        $config = $this->db->get('app_config');
        $config = $config->result_array();

        foreach ($config as $value) {

            if ($value['_type'] == 'int') {
                $params[$value['_key']] = intval($value['value']);
            } else if ($value['_type'] == 'float') {
                $params[$value['_key']] = floatval($value['value']);
            } else if ($value['_type'] == 'double') {
                $params[$value['_key']] = doubleval($value['value']);
            } else if ($value['_type'] == 'boolean') {
                if ($value['value'] == 1) {
                    $params[$value['_key']] = TRUE;
                } else {
                    $params[$value['_key']] = FALSE;
                }
            } else {
                $params[$value['_key']] = $value['value'];
            }

        }

        return $params;
    }


    /*private function reportIssueOptions()
    {

        $options = _lang("Options 1") . ";" . _lang("Options 2") . _lang("Options 3");

        $this->db->insert('app_config', array(
                '_key' => "ORDER_REPORT_ISSUE_DELIVERY",
                'value' => $options,
                '_type' => "string",
                'is_verified' => 1,
                '_version' => APP_VERSION,

                'updated_at' => date("Y-m-d H-i-s"),
                'created_at' => date("Y-m-d H-i-s"),
            )
        );
    }*/


    public function createConfigTable()
    {

        $this->load->dbforge();
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => TRUE
            ),
            '_key' => array(
                'type' => 'VARCHAR(100)',
                'default' => NULL
            ),
            'value' => array(
                'type' => 'TEXT',
                'default' => NULL
            ),
            '_type' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),
            'is_verified' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            'user_id' => array(
                'type' => 'INT',
                'default' => NULL
            ),
            '_version' => array(
                'type' => 'VARCHAR(30)',
                'default' => NULL
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'default' => NULL
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'default' => NULL
            )
        ));

        $attributes = array('ENGINE' => 'InnoDB');
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('app_config', TRUE, $attributes);


    }


}
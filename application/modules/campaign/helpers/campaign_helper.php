<?php


class VisitsManager
{
    public static function add($data = array())
    {

    }
}


class CampaignManager{

    private static $registered_modules = array();


    public static function register($data=array()){


        /*array(
            "module" => String,
            "api"    => String,
            "custom_config" => Array
        )*/


        if(isset($data['module']) && !is_string($data['module']))
            $data['module'] = strtolower(get_class($data['module']));


        if(!isset(self::$registered_modules[$data['module']])){

            self::$registered_modules[$data['module']]['module'] = $data['module'];
            self::$registered_modules[$data['module']]['api'] = $data['api'];


            if(isset($data['callback_input']))
                self::$registered_modules[$data['module']]['callback_input'] = $data['callback_input'];

            if(isset($data['callback_output']))
                self::$registered_modules[$data['module']]['callback_output'] = $data['callback_output'];

            if(isset($data['custom_parameters']))
                self::$registered_modules[$data['module']]['custom_parameters'] = $data['custom_parameters'];

        }
    }


    public static function load(){
        return self::$registered_modules;
    }


    public static function compile_custom_parameter($module, $parameter=array()){

        $data = array(
            'HTML' => "",
        );

        foreach ($parameter as $key1 => $param){

            $data['HTML'] .= "<div class=\"form-group custom-parameter custom-parameter-".$module." hidden\">";
            $data['HTML'] .= "<label>".ucfirst(Translate::sprint($key1))."</label><br>";

                if($param['type'] == 'checkbox'){
                    foreach ($param['values'] as $key2 => $value) {
                        $data['HTML'] .= "<label><input class=\"".$key1."-".$key2."\" name=\"".$key1."-".$key2."\" value=\"".$value."\" type=\"checkbox\"  checked/>&nbsp;&nbsp;$key2</label>&nbsp;&nbsp;&nbsp;";
                    }
                }else if($param['type'] == 'radio'){
                    $default = 0;
                    foreach ($param['values'] as $key2 => $value) {

                        if($default == 0)
                            $data['HTML'] .= "<label><input class=\"".$key1."\" name='$key1' value=\"".$value."\" type=\"radio\" checked/>&nbsp;&nbsp;$key2</label>&nbsp;&nbsp;&nbsp;";
                        else
                            $data['HTML'] .= "<label><input class=\"".$key1."\" name='$key1' value=\"".$value."\" type=\"radio\"/>&nbsp;&nbsp;$key2</label>&nbsp;&nbsp;&nbsp;";

                        $default = 1;
                    }
                }

            $data['HTML'] .= "</div>";

        }

        return $data;
    }


}


class CampaignModuleR{

    private $module;
    private $api;
    private $callback_input;
    private $callback_output;
    private $custom_parameters;

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param mixed $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * @return mixed
     */
    public function getCallbackInput()
    {
        return $this->callback_input;
    }

    /**
     * @param mixed $callback_input
     */
    public function setCallbackInput($callback_input)
    {
        $this->callback_input = $callback_input;
    }

    /**
     * @return mixed
     */
    public function getCallbackOutput()
    {
        return $this->callback_output;
    }

    /**
     * @param mixed $callback_output
     */
    public function setCallbackOutput($callback_output)
    {
        $this->callback_output = $callback_output;
    }

    /**
     * @return mixed
     */
    public function getCustomParameters()
    {
        return $this->custom_parameters;
    }

    /**
     * @param mixed $custom_parameters
     */
    public function setCustomParameters($custom_parameters)
    {
        $this->custom_parameters = $custom_parameters;
    }



}
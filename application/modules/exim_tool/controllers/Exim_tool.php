<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droidev-Technology / Email: droideve.tech@gmail.com
 * Date: 26/04/2020
 * Time: --
 */

class Exim_tool extends MAIN_Controller {

	private $module = "exim_tool";

	const Encoding = array("UTF-8","UTF-16","ISO-8859-1","ISO-8859-2","ISO-8859-9","GB2312","Big5","Shift_JIS");
	const Delimiter = array("comma","semicolon");


	public function __construct(){
        parent::__construct();

        $this->init($this->module);

    }

    public function onLoad()
    {
        define("EXIM_TOOL_MANAGER","EXIM_TOOL_MANAGER");
		$this->load->model("exim_tool/Exim_tool_model","mEXIMTool");
		$this->load->helper("exim_tool/exim");

	}

    public function onCommitted($isEnabled)
    {

    	if(!$isEnabled)
			return;



    }

	public function plugin_export($param=array()){

    	//module - cols

		$param['unique_id'] = (rand(1,100));

		if(!isset($param['module'])){
			throw new Exception('Please define module name');
		}

		$data = array(
			"html"	 => $this->load->view('exim_tool/plugins/export/html',$param,TRUE),
			"script" => $this->load->view('exim_tool/plugins/export/script',$param,TRUE),
		);

		return $data;

	}



	public function onInstall($user=NULL)
	{
		Translate::updateLanguages("exim_tool");
        return TRUE;
	}

	public function onUninstall($user=NULL)
	{
		// TODO: Implement onUninstall() method.
        return TRUE;
	}

	public function onEnable($user=NULL)
	{
		// TODO: Implement onEnable() method.
		GroupAccess::registerActions($this->module,array(
			EXIM_TOOL_MANAGER,
		));

        return TRUE;
	}

	public function onDisable($user=NULL)
	{
		// TODO: Implement onDisable() method.
        return TRUE;
	}

	public function onUpgrade($user=NULL)
	{
        Translate::updateLanguages("exim_tool");
        return TRUE;
	}

	public function cron()
	{
		// TODO: Implement cron() method.
	}
}

/* End of file ArticleDB.php */

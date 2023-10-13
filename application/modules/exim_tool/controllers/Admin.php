<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droidev-Technology / Email: droideve.tech@gmail.com
 * Date: 26/04/2020
 * Time: --
 */

class Admin extends ADMIN_Controller {

	private $module = "exim_tool";

    public function __construct(){
        parent::__construct();
    }


	public function import(){

		/*$module = RequestInput::get('module');

		if($module!="" && Exim_Importer::isRegistered($module)){
			$data['module'] = $module;
		}else
			redirect(admin_url("error404"));

		$data['title'] = _lang("Import");

		$this->template->header(Template1::DISPLAY,$data);
		$this->template->sidebar();
		$this->load->view('exim_tool/backend/import');
		$this->template->footer();*/

	}


    public function mapping(){

		/*$module = RequestInput::get('module');

		$data['file_encoding'] = RequestInput::get('file_encoding');
		$data['file_delimiter'] = RequestInput::get('file_delimiter');


		if($module!="" && Exim_Importer::isRegistered($module)){
			$data['module'] = $module;
		}else
			redirect(admin_url("error404"));

		$file = RequestInput::get('file');
		$file = FileManager::_openDir($file);

		if(!empty($file))
			$data['file'] = $file;
		else
			redirect(admin_url("exim_tool/import?err=file"));

    	$data['title'] = _lang("Import");

		$this->template->header(Template1::DISPLAY,$data);
		$this->template->sidebar();
		$this->load->view('exim_tool/backend/mapping');
		$this->template->footer();*/


	}

}

/* End of file ArticleDB.php */

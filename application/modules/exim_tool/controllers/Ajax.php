<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droidev-Technology / Email: droideve.tech@gmail.com
 * Date: 26/04/2020
 * Time: --
 */

class Ajax extends AJAX_Controller  {

	private $module = "exim_tool";

    public function __construct()
    {
        parent::__construct();
    }


    private function save_data($data){
		$this->session->set_userdata('export_data', $data);
	}

	private function clear_data(){
		$this->session->set_userdata('export_data', "");
	}

	private function get_saved_data(){
		return $this->session->userdata('export_data');
	}

	public function export(){


		$data = RequestInput::post("data");
		$format = RequestInput::post("format");
		$cols = RequestInput::post("cols");
		$module = RequestInput::post("module");

		if(empty($cols) && empty($data) && $format==""){
			echo json_encode(array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>"Invalid Data")));return;
		}

		if(is_array($data))
			$data = json_encode($data,JSON_FORCE_OBJECT);

		if(is_array($cols))
			$cols = json_encode($cols,JSON_FORCE_OBJECT);


		$data = base64_encode($data);
		//$format = base64_encode($format);
		$cols = base64_encode($cols);

		//save
		$this->save_data($data);
		$data = "in-cache";

		if($format == "csv"){
			$link = site_url('exim_tool/ajax/csv?data='.$data.'&format='.$format.'&cols='.$cols.'&module='.$module);
		}else if($format == "xml"){
			$link = site_url('exim_tool/ajax/xml?data='.$data.'&format='.$format.'&cols='.$cols.'&module='.$module);
		}else if($format == "json"){
			$link = site_url('exim_tool/ajax/json?data='.$data.'&format='.$format.'&cols='.$cols.'&module='.$module);
		}else{
			$link = site_url('exim_tool/ajax/error');
		}


		echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$link));return;

	}

	function error(){
    	echo "invalid export";
	}

	function csv(){

		$this->load->helper('exim_tool/csv_export');

		$data = RequestInput::get("data");
		$cols = RequestInput::get("cols");
		$module = RequestInput::get("module");



		if($data == "in-cache"){
			$data = $this->get_saved_data();
			$this->clear_data();
		}


		if($module == "")
			$module = 'file';

		$data = base64_decode($data);
		$cols = base64_decode($cols);

		if(!is_array($data))
			$data = json_decode($data,JSON_OBJECT_AS_ARRAY);

		if(!is_array($data))
			$data = json_decode($data,JSON_OBJECT_AS_ARRAY);

		if(!is_array($cols))
			$cols = json_decode($cols,JSON_OBJECT_AS_ARRAY);

		if(!is_array($cols))
			$cols = json_decode($cols,JSON_OBJECT_AS_ARRAY);



		$fields = array();

		foreach ($cols as $key => $val){
			$fields[] = _lang($key);
		}


		$csv = new CSVFileExport();
		$csv->setFields($fields);


		foreach ($data as $object){

			$line = array();

			foreach ($fields as $field){
				$line[] = $object[$field];
			}

			$csv->addLine($line);
		}

		$csv->download($module);
	}


	function xml(){

		$this->load->helper('exim_tool/csv_export');

		$data = RequestInput::get("data");
		$cols = RequestInput::get("cols");
		$module = RequestInput::get("module");

		if($data == "in-cache"){
			$data = $this->get_saved_data();
			$this->clear_data();
		}

		if($module == "")
			$module = 'file';

		$data = base64_decode($data);
		$cols = base64_decode($cols);

		if(!is_array($data))
			$data = json_decode($data,JSON_OBJECT_AS_ARRAY);

		if(!is_array($data))
			$data = json_decode($data,JSON_OBJECT_AS_ARRAY);

		if(!is_array($cols))
			$cols = json_decode($cols,JSON_OBJECT_AS_ARRAY);


		if(!is_array($cols))
			$cols = json_decode($cols,JSON_OBJECT_AS_ARRAY);



		$fields = array();

		foreach ($cols as $key => $val){
			$fields[] = _lang($key);
		}

		$lines = array();
		foreach ($data as $object){

			$line = array();

			foreach ($fields as $field){
				$line[$field] = $object[$field];
			}

			$lines[] = $line;

		}



		// function definition to convert array to xml
		function array_to_xml( $data, &$xml_data ) {
			foreach( $data as $key => $value ) {
				if( is_numeric($key) ){
					$key = 'item_'.$key; //dealing with <0/>..<n/> issues
				}
				if( is_array($value) ) {
					$subnode = $xml_data->addChild($key);
					array_to_xml($value, $subnode);
				} else {
					$xml_data->addChild("$key",htmlspecialchars("$value"));
				}
			}
		}


		// creating object of SimpleXMLElement
		$xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');

		// function call to convert array to xml
		array_to_xml($lines,$xml_data);

		$result = $xml_data->asXML();

		$module = $module.'-'.date("y-m-d-h",time()).'.xml';

		//set headers to download file rather than displayed
		header('Content-Type: text/xml');
		header('Content-Disposition: attachment; filename="' . $module . '";');

		echo $result;

	}



	function json(){


		$data = RequestInput::get("data");
		$cols = RequestInput::get("cols");
		$module = RequestInput::get("module");

		if($data == "in-cache"){
			$data = $this->get_saved_data();
			$this->clear_data();
		}

		if($module == "")
			$module = 'file';

		$data = base64_decode($data);
		$cols = base64_decode($cols);

		if(!is_array($data))
			$data = json_decode($data,JSON_OBJECT_AS_ARRAY);

		if(!is_array($data))
			$data = json_decode($data,JSON_OBJECT_AS_ARRAY);

		if(!is_array($cols))
			$cols = json_decode($cols,JSON_OBJECT_AS_ARRAY);


		if(!is_array($cols))
			$cols = json_decode($cols,JSON_OBJECT_AS_ARRAY);



		$fields = array();

		foreach ($cols as $key => $val){
			$fields[] = _lang($key);
		}

		$lines = array();
		foreach ($data as $key => $object){

			$line = array();

			foreach ($fields as $field){
				$line[$field] = $object[$field];
			}

			$lines['item_'.$key] = $line;

		}

		$result = array("data"=>$lines);

		$module = $module.'-'.date("y-m-d-h",time()).'.json';

		//set headers to download file rather than displayed
		header('Content-Type: text/xml');
		header('Content-Disposition: attachment; filename="' . $module . '";');

		echo json_encode($result);

	}




	public function export2_data()
	{

		$format = RequestInput::post("format");
		$cols = RequestInput::post("columns");
		$module = RequestInput::post("module");
		$date_from = RequestInput::post("date_from");
		$date_to = RequestInput::post("date_to");
		$export_request = RequestInput::post("export_request");


		$params = array(
			'format'=>$format,
			'requested_columns'=>$cols,
			'module'=>$module,
			'date_from'=>$date_from,
			'date_to'=>$date_to,
			'export_request'=>$export_request,
		);

		$result = $this->mEXIMTool->generate_data_ex2($params);


		if($result[Tags::SUCCESS]==1 && count($result[Tags::RESULT])>0){

			$data = base64_encode(json_encode($result[Tags::RESULT]));
			//$format = base64_encode($format);
			$cols = base64_encode(json_encode($result['cols']));

			//save
			$this->save_data($data);
			$data = "in-cache";

			if($format == "csv"){
				$link = site_url('exim_tool/ajax/csv?data='.$data.'&format='.$format.'&cols='.$cols.'&module='.$module);
			}else if($format == "xml"){
				$link = site_url('exim_tool/ajax/xml?data='.$data.'&format='.$format.'&cols='.$cols.'&module='.$module);
			}else if($format == "json"){
				$link = site_url('exim_tool/ajax/json?data='.$data.'&format='.$format.'&cols='.$cols.'&module='.$module);
			}else{
				$link = site_url('exim_tool/ajax/error');
			}


			echo json_encode(array(Tags::SUCCESS=>1,Tags::RESULT=>$link));return;
		}



		echo json_encode($result);return;

	}


	public function import2_data(){

		$file = RequestInput::post("file");
		$fields = RequestInput::post("fields");
		$module = RequestInput::post("module");

		$file_encoding = RequestInput::post("file_encoding");
		$file_delimiter = RequestInput::post("file_delimiter");

		if($file_delimiter=="comma")
			$file_delimiter = ",";
		else
			$file_delimiter = ";";

		if(!in_array($file_encoding,Exim_tool::Encoding))
			$file_encoding = "UTF-8";

		$data = Exim_Importer::prepareFields($module,$file,$fields,$file_delimiter,$file_encoding);

		if($data[Tags::SUCCESS]==0 && !isset($data['error_html'])){
			$data['error_html'] = "";
		}

		echo json_encode($data);return;

	}
}

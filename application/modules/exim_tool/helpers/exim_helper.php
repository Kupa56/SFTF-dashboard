<?php


class Exim_Utils{

	public static function query_columns($columns_map=array()){

		//to get simple attr ()
		//get 2d attr

		$prepared_map = array();

		foreach ($columns_map as $key => $attr){
			if(preg_match("#^[a-zA-Z0-9_]+$#i",$attr)){
				$prepared_map[$key][] = $attr;
			}else if(preg_match("#[.]+#i",$attr)){

				$attrs = explode(".",$attr);
				$prepared_map[$key] = $attrs;

			}else if(preg_match("#[()]{1}#i",$attr)) {
				$prepared_map[$key][] = $attr;
			}
		}

		return $prepared_map;
	}

	public static function isMethod($str){

		if(preg_match("#[()]{1}#i",$str)) {
			return preg_replace("#[()]{1}#i","",$str);
		}

		return NULL;
	}

}

class Exim_Importer
{

	private static $registered_import_maps = array();


	public static function isRegistered($module){
		$map = Exim_Importer::getMap($module);

		if(!empty($map))
			return TRUE;


		return FALSE;
	}

	public static function prepareFields($module,$imported_file,$fields,$file_delimiter=",",$file_encoding="UTF-8"){

		$errors = array();

		$map = Exim_Importer::getMap($module);

		$request_index = array();
		$requested_data = array();

		/*if(count($fields) !== count(array_unique($fields)))
			return array(Tags::CODE=>Codes::FAILED,Tags::ERRORS=>array("err"=>_lang("You have duplicated selected field!")));
			*/

		//get first line
		$file = FileManager::_openDir($imported_file);

		if (($handle = fopen($file['path'], "r")) !== FALSE) {
			$line = 0;
			while (($data = fgetcsv($handle, 1000, $file_delimiter)) !== FALSE) {

				if(empty($request_index)){//first line

					for ($c=0; $c < count($data); $c++) {

						foreach ($fields as $k => $f){
							if($f==$data[$c]){
								$request_index[$k] = $c;
							}
						}

					}

					if(!empty($errors))
						break;

				}else{//rest lines

					$requested_data[$line] = array();
					foreach ($request_index as $field => $index){
						if(isset($data[$index]) && $data[$index]!=""){
							$requested_data[$line][$field] = $data[$index];
						}
					}

					if(empty($requested_data[$line])){
						unset($requested_data[$line]);
					}else
						$line++;

				}
			}
			fclose($handle);
		}

		if(!empty($errors))
			return array(Tags::CODE=>Codes::FAILED,Tags::ERRORS=>$errors);


		if(!empty($requested_data)){
			$result = call_user_func($map["callback"],$requested_data);

			if(isset($result[Tags::CODE]) && $result[Tags::CODE]==Codes::SUCCESS){
				//remove imported file
				@FileManager::_removeDir($imported_file);
			}else if(isset($result[Tags::CODE]) && $result[Tags::CODE]==Codes::FAILED){
				$result["error_html"] = Exim_Importer::error_list($result[Tags::ERRORS]);
				$result[Tags::ERRORS] = array("err"=>_lang("None of the rows can be imported"));
			}

			return $result;
		}

		return array(Tags::CODE=>Codes::FAILED);
	}


	private static function error_list($errors=array()){

		$ctx = &get_instance();
		$data['errors'] = $errors;
		$html = $ctx->load->view("exim_tool/backend/error_html",$data,TRUE);

		return $html;
	}

	public static function getImportedFields($module,$imported_file,$file_delimiter=",",$file_encoding="UTF-8"){

		$new_map = array();

		$map = Exim_Importer::getMap($module);
		$registered_cols = $map['columns'];

		$fields = array();

		//get first line
		$imported_file = FileManager::_openDir($imported_file);

		if (($handle = fopen($imported_file['path'], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $file_delimiter)) !== FALSE) {
				for ($c=0; $c < count($data); $c++) {
					$fields[] = $data[$c];
				}
				break;
			}
			fclose($handle);
		}

		$recorded_lines = -1;
		if (($handle = fopen($imported_file['path'], "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 1000, $file_delimiter)) !== FALSE) {
				$isEmpty = TRUE;
				for ($c=0; $c < count($data); $c++) {
					if($data[$c]!="")
						$isEmpty = FALSE;
				}

				if($isEmpty==FALSE)
					$recorded_lines++;
			}
			fclose($handle);
		}


		foreach ($registered_cols as $col){
			$new_map[$col] = "";
			if(in_array($col,$fields)){
				$new_map[$col] = $col;
			}
		}

		return array(
			"callback" => $map['callback'],
			"map"=>$new_map,
			"fields"=>$fields,
			"lines"=>$recorded_lines
		);
	}

	public static function getMap($module){

		if(isset(self::$registered_import_maps[$module]))
			return self::$registered_import_maps[$module];

		return NULL;
	}

	public static function setup($module, $cols=array(), $callback="",$example="",$extras_parameters=array()){


		if(!isset(self::$registered_import_maps[$module])){
			self::$registered_import_maps[$module] = array(
				'columns'=>$cols,
				'callback'=>$callback,
				'example'=>$example,
				'parameters'=>$extras_parameters,
			);
		}

		$ctx = &get_instance();


		$data = array();
		$data['module'] = $module;
		$data['unique_id'] = md5(time().$module);
		$data['modal_id'] = "importer-modal-".$data['unique_id'];
		$data['modal_title'] = "xxxxxxxxxx";

		return array(
			"modal"=> $ctx->load->view('exim_tool/plugins/importer2/modal',$data,TRUE),
			"script"=> $ctx->load->view('exim_tool/plugins/importer2/script',$data,TRUE),
		);

	}

}

class Exim_Exporter
{

	private static $registered_export_maps = array();

	public static function getMap($module){

		if(isset(self::$registered_export_maps[$module]))
			return self::$registered_export_maps[$module];

		return NULL;
	}

	public static function setupUsingOREM($module, $class, $cols=array(),$title=""){


		if(!isset(self::$registered_export_maps[$module])){
			self::$registered_export_maps[$module] = array(
				'class'=>$class,
				'columns'=>$cols,
			);
		}

		$ctx = &get_instance();

		$cols_html = "";

		foreach ($cols as $k => $col){
			$cols_html .= '<span><label><input type="checkbox" class="" value="'.$k.'" checked />&nbsp;&nbsp;'.$k.'</label>&nbsp;&nbsp;&nbsp;&nbsp;</span>';
		}

		$data = array();
		$data['module'] = $module;
		$data['unique_id'] = md5(time().$module);
		$data['modal_id'] = "exporter-modal-".$data['unique_id'];
		$data['cols_html'] = $cols_html;
		$data['modal_title'] = $title;

		return array(
			"modal"=> $ctx->load->view('exim_tool/plugins/export2/modal',$data,TRUE),
			"script"=> $ctx->load->view('exim_tool/plugins/export2/script',$data,TRUE),
		);

	}

}




class Exim_toolManager{


	public static function setCol($col,$value,$row=0){
		return '<exim exim-row="'.$row.'" class="exim-col exim-col-'.$col.'" style="display:none" exim-col-name="'._lang($col).'" data-col="'.$col.'">'.$value.'</exim>';
	}

	private static $order = 0;

	public static function open_export_item(){
		self::$order++;
		return  '<exim-item class="exim-row hidden" exim-row="'.self::$order.'">';
	}

	public static function close_export_item(){
		return  '</exim-item>';
	}


	public static function setupRows($array=array()){

		$exim_html = Exim_toolManager::open_export_item();
		$row_id = self::$order;
		foreach ($array as $key => $col){
			$exim_html .= Exim_toolManager::setCol($key,$col,$row_id);
		}

		$exim_html .= Exim_toolManager::close_export_item();

		return $exim_html;
	}


}

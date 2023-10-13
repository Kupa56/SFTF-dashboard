<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by Console.
 * User: Droidev-Technology / Email: droideve.tech@gmail.com
 * Date: 26/04/2020
 * Time: --
 */

class Exim_tool_model extends CI_Model
{

	private $maximum_lines = 500;

    public function __construct()
    {
        parent::__construct();
    }


    public function generate_data_ex2($params=array()){

    	$errors = array();
    	extract($params);


    	if(isset($module) && !preg_match("#^([a-zA-Z0-9_]+)$#i",$module)
			/*&& !ModulesChecker::isRegistred($module)*/){
			$errors[] = _lang("Module ID is not valid!");
		}

		if(isset($requested_columns) && empty($requested_columns)){
			$errors[] = _lang("Columns is empty!");
		}else{

			$valid_columns = array();

			foreach ($requested_columns as $vc){
				$valid_columns[] = strtolower($vc);
			}

			$requested_columns = $valid_columns;
		}

		if(isset($format) && $format==""){
			$errors[] = _lang("Format is not valid!");
		}


		if(isset($export_request) && $export_request=="specific"){

			if(isset($date_from) && $date_from==""){
				$errors[] = _lang("Date_from is not valid!");
			}

			if(isset($date_to) && $date_to==""){
				$errors[] = _lang("Date_to is not valid!");
			}

		}


		if(empty($errors)){

			$map = Exim_Exporter::getMap($module);

			$class = $map['class'];
			$columns_map = $map['columns'];

			$user = $this->mUser->getUser();
			$app_id = $user->app_id;

			$object = $class::where("app_id",$app_id);

			if(isset($export_request) && $export_request=="specific"){
				$object = $object->where("created_at", ">=",$date_from)
					->where("created_at", "<=",$date_to);
			}



			$objects = $object->limit($this->maximum_lines)->get();

			$prepared_cols = Exim_Utils::query_columns($columns_map);

			$final_data = array();
			foreach ($objects as $object){
				$final_data[] = $this->instance_object($prepared_cols,$requested_columns,$object);
			}


			$new_cols = array();
			foreach ($requested_columns as $col){
				$new_cols[$col] = $col;
			}

			if(count($final_data)==0)
				return array(Tags::SUCCESS=>0,Tags::ERRORS=>array("err"=>_lang("No Records Found")));


			return array(Tags::SUCCESS=>1,Tags::RESULT=>$final_data,"cols"=>$new_cols);
		}

		return array(Tags::SUCCESS=>0,Tags::ERRORS=>$errors);
	}

	/*
	* instance and prepare an object
	*/

	function instance_object($prepared_cols,$requested_columns,$object){

		$new_object = array();

		foreach ($prepared_cols as $key => $pc){

			if(!empty($pc) && in_array($key,$requested_columns)){

				$prepared_object_per_col = $this->prepare_object($pc,$object);
				$new_object[$key] = $prepared_object_per_col;

			}

		}

		return $new_object;
	}


	/*
	* instance and query a column per object as request from client
	*/

	function prepare_object($prepared_col,$object_to_prepare){

		$map = "";
		foreach ($prepared_col as $attr){

			if(Exim_Utils::isMethod($attr)!=NULL){

				$method = Exim_Utils::isMethod($attr);
				if($object_to_prepare!=NULL)
					$object_to_prepare = $object_to_prepare->{$method}();
				else{

				}

				$map = $map."->".$attr;

			}else{

				if($object_to_prepare!=NULL)
					$object_to_prepare = $object_to_prepare->{$attr};

				$map = $map."->".$attr;

				break;
			}

		}



		//echo $map." = $object_to_prepare \n <br>";

		return $object_to_prepare;
	}


}

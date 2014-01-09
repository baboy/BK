<?php
class AppHandler extends bf\core\HttpRequestHandler{
	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = $this->db->getModel($modelName);
		}
		return $this->model;
	}
	function init(){
		$this->getModel("App");
	}
	function registerParam(){
		$fields = array(
				"name"			=>array("type"=>"string"),
				"product_id"	=>array("type"=>"string"),
				"package"		=>array("type"=>"string"),
				"version"		=>array("type"=>"string"),
				"build"			=>array("type"=>"int"),
				"channel"		=>array("type"=>"string", "option"=>true),
				"developer"		=>array("type"=>"string", "option"=>true),
				"description"	=>array("type"=>"string", "option"=>true),
			);
		return $fields;
	}
	function register($param){
		$ret = $this->model->register($param);
		if (empty($ret)) {
			$status = Status::error();
			$status->error = $this->model->last_error;
			return $status;
		}
		$status = Status::status();
		$param["id"] = $ret;
		$status->data = $param;
		return $status;
	}
}
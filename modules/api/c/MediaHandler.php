<?php

class MediaHandler extends bf\core\HttpRequestHandler{
	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = new $modelName();
		}
		return $this->model;
	}
	function init(){
		$this->getModel("Media");
	}
	function queryParam(){
		$fields = array(
				"module"=>array("type"=>"string"),
				"offset"=>array("type"=>"int","default"=>0),
				"count"=>array("type"=>"int","default"=>30),
			);
		return $fields;
	}
	function query($param){
		$data = $this->model->query($param);
		$status = bf\core\Status::status();
		$status->data = $data;
		return $status;
	}
	function detailParam(){
		$fields = array(
				"sid"=>array("type"=>"string")
			);
		return $fields;
	}
	function detail($param){
		$data = $this->model->queryDetail($param["sid"]);
		$status = bf\core\Status::status();
		$status->data = $data;
		return $status;
	}

	function recentParam(){
		$fields = array(
				"sid"=>array("type"=>"string","option"=>true),
				"count"=>array("type"=>"int","option"=>true, "default"=>20),
				"module"=>array("type"=>"string")
			);
		return $fields;

	}
	function recent($param){
		$data = $this->model->queryRecent($param["module"], empty($param["sid"]) ? null:$param["sid"],$param["count"]);
		$status = bf\core\Status::status();
		$status->data = $data;
		return $status;
	}
}
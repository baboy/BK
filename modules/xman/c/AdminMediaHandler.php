<?php

class AdminMediaHandler extends bk\core\HttpRequestHandler{
	function init(){
		global $media;
		$this->model = $media;
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
		if ($param["module"] == "serial") {
			$param["node"] = "SERIAL";
		}
		$data = $this->model->query($param);
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}
	function detailParam(){
		$fields = array(
				"sid"=>array("type"=>"string"),
				"module"=>array("type"=>"string","option"=>true),
			);
		return $fields;
	}
	function detail($param){
		$data = $this->model->queryDetail($param["sid"]);
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}
}
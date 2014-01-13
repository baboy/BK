<?php

class MediaHandler extends bf\core\HttpRequestHandler{
	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = $this->db->getModel($modelName);
		}
		return $this->model;
	}
	function init(){
		$this->getModel("Media");
	}
	function query($param){
		$param = array("module"=>"movie","node"=>"CONTENT");
		$data = $this->model->query($param);
		$status = bf\core\Status::status();
		$status->data = $data;
		return $status;
	}
}
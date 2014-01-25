<?php

class MediaStatisticHandler extends bf\core\HttpRequestHandler{
	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = new $modelName();
		}
		return $this->model;
	}
	function init(){
		$this->getModel("MediaStatistic");
	}
	function viewParam(){
		$fields = array(
				"sid"=>array("type"=>"int")
			);
		return $fields;
	}
	function view($param){
		$this->model->view($param["sid"]);
		return bf\core\Status::status();
	}
}
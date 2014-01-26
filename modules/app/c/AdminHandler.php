<?php
class AdminHandler extends bf\core\HttpRequestHandler{
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
	function admin($param){
		return null;
	}
	function mgr($param){
		
	}

}
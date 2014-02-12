<?php
class Edit extends bf\core\HttpRequestHandler{
	function init(){
		//$this->getModel("Media");
	}
	function loadParam(){
		$fields = array(
				"sid"=>array("type"=>"int"),
			);
		return $fields;
	}
	function load($param){
		return null;
	}
}
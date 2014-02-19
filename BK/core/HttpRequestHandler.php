<?php
namespace bk\core;
class HttpRequestHandler{
	protected $model;
	function getModel($modelName){
		$refl = new \ReflectionClass($this);
		$fn = $refl->getFileName();
		$re = "/.+\/modules\/[^\/]+/";
		preg_match($re, $fn, $m, PREG_OFFSET_CAPTURE);
		$basePath = null;
		if(!empty($m))
			$m = $m[0];
		if(!empty($m))
			$basePath = $m[0];
		$modelFile = $basePath."/m/".$modelName.".php";
		if (empty($this->model)) {
			require_once $modelFile;
			$this->model = $this->db->getModel($modelName);
		}
		return $this->model;
	}
	function init(){
	}

	function logParam(){
		return null;
	}
	function checkFields($fields, $value){
		$validator = new Validator($fields);
		$validator->setValue($value);
		return $validator->check();
	}
}
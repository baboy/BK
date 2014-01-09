<?php
namespace bf\core;
class HttpRequestHandler{
	protected $model;
	function getModel($modelName){
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
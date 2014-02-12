<?php
class AppHandler extends bf\core\HttpRequestHandler{
	function init(){
		$this->getModel("App");
	}
	function admin($param){
		return null;
	}
	function mgr($param){
		
	}

}
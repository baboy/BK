<?php
class EditHandler extends bk\core\HttpRequestHandler{
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
		$status = bk\core\Status::status();
		$status->data = $param;
		return $status;
	}
	function updateParam(){
		$fields = array(
				"sid"		=>	array("type"=>"int",		"option"=>true),
				"title"		=>	array("type"=>"string"),
				"content"	=>	array("type"=>"string",		"option"=>true),
				"m3u8"		=>	array("type"=>"string",		"option"=>true),
				"mp4"		=>	array("type"=>"string",		"option"=>true),
				"summary"	=>	array("type"=>"string",		"option"=>true),
			);
		return $fields;
	}
	function update($param){

	}
}
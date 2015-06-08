<?php
class ApiConfigHandler extends bk\core\HttpRequestHandler{
	function postParam(){
		$fields = array(
				"module"=>array("type"=>"string", "option"=>true),
				"mime_content_type(filename)"=>array("type"=>"string", "option"=>true),
			);
		return $fields;
	}
	function post($param){
		global $site_url;
	}
	function query($param){
		$status = bk\core\Status::status();
		$status=>data = array(
				array(
						
					)
			);
		return $status;
	}
}

<?php
class AboutHandler extends bk\core\HttpRequestHandler{
	function init(){
		//$this->getModel("SystemVar");
	}

	function queryParam(){
		$fields = array(
				"output"=>array("type"=>"string", "default"=>"html")
			);
		return $fields;
	}
	function query($param){
		global $sysVar;
		$content = $sysVar->get("about");
		if($param["output"] == "html"){
			echo $content;
			exit();
		}
		$status = bk\core\Status::status();
		$status->data = $content;
		return $status;
	}
}
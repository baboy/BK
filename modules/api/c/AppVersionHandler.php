<?php

class AppVersionHandler extends bk\core\HttpRequestHandler{

	
	function init(){
		$this->getModel("AppVersion");
	}
	function updateParam(){
		$fields = array(
				"product_id"=>array("type"=>"string"),
				"version"=>array("type"=>"float"),
				"platform"=>array("type"=>"string", "option"=>true,"default"=>"android"),
				"build"=>array("type"=>"int"),
				"mac"=>array("type"=>"string","option"=>true),
				"channel"=>array("type"=>"string","option"=>true),
				"device_id"=>array("type"=>"string","option"=>true)
			);
		return $fields;
	}
	function update($param){
		$app = $this->model->queryLastVersion($param["product_id"], empty($param["channel"]) ? null : $param["channel"],$param["platform"],"publish");
		$status = bk\core\Status::status();
		$data = array("role"=>0, "msg"=>"没有可更新的版本!", "version"=>$param["version"], "build"=>$param["build"]);
		if (!empty($app) && $app->version > $param["version"]) {
			$data["role"] = 10;
			$data["download_url"] = $app->download_url;
			$data["msg"] = "有新版本更新！";
			$data["version"] = $app->version;
			$data["build"] = $app->build;
			$data["link"] = $app->link;
		}
		$status->data = $data;
		return $status;
	}
}
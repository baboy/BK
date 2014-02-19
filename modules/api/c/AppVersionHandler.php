<?php

class AppVersionHandler extends bk\core\HttpRequestHandler{

	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = new $modelName();
		}
		return $this->model;
	}
	function init(){
		$this->getModel("AppVersion");
	}
	function updateParam(){
		$fields = array(
				"package"=>array("type"=>"string"),
				"version"=>array("type"=>"float"),
				"build"=>array("type"=>"int"),
				"mac"=>array("type"=>"string","option"=>true),
				"channel"=>array("type"=>"string","option"=>true),
				"device_id"=>array("type"=>"string","option"=>true)
			);
		return $fields;
	}
	function update($param){
		$app = $this->model->queryLastVersion($param["package"], empty($param["channel"]) ? null : $param["channel"],"publish");
		$status = bk\core\Status::status();
		$data = array("role"=>0, "msg"=>"没有可更新的版本!", "version"=>$param["version"], "build"=>$param["build"]);
		if (!empty($app) && $app->version > $param["version"]) {
			$data["role"] = 10;
			$data["download_url"] = $app->download_url;
			$data["msg"] = "有新版本更新！";
			$data["version"] = $app->version;
			$data["build"] = $app->build;
		}
		$status->data = $data;
		return $status;
	}
}
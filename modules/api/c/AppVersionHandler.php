<?php

class AppVersionHandler extends bk\core\HttpRequestHandler{

	
	function init(){
		$this->getModel("AppVersion");
	}
	function updateParam(){
		$fields = array(
				"product_id"=>array("type"=>"string"),
				"version"=>array("type"=>"string"),
				"os"=>array("type"=>"string", "option"=>true,"default"=>"android"),
				"build"=>array("type"=>"int"),
				"mac"=>array("type"=>"string","option"=>true),
				"channel"=>array("type"=>"string","option"=>true),
				"device_id"=>array("type"=>"string","option"=>true)
			);
		return $fields;
	}
	function update($param){
		$app = $this->model->queryLastVersion($param["product_id"], empty($param["channel"]) ? null : $param["channel"],$param["os"],"publish");
		$status = bk\core\Status::status();
		$data = array("role"=>0, "msg"=>"没有可更新的版本!", "version"=>$param["version"], "build"=>$param["build"]);
		// if($param["product_id"]=="com.tvie.xj.ivideo.pad" || $param["product_id"]=="com.tvie.xj.ivideo"){
		// 	$data["role"] = 10;
		// 	$data["version"] = 10;
		// }
		if (!empty($app) && compareVersion($app->version, $param["version"])) {
			$data["role"] = 10;
			$data["download_url"] = $app->download_url;
			$data["msg"] = $app->description;
			$data["version"] = $app->version;
			$data["build"] = $app->build;
			$data["link"] = $app->link;
		}
		$status->data = $data;
		return $status;
	}
}
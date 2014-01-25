<?php
class AppHandler extends bf\core\HttpRequestHandler{
	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = $this->db->getModel($modelName);
		}
		return $this->model;
	}
	function init(){
		$this->getModel("App");
	}
	function registerParam(){
		$fields = array(
				"name"			=>array("type"=>"string"),
				"package"		=>array("type"=>"string"),
				"developer"		=>array("type"=>"string", "option"=>true),
				"description"	=>array("type"=>"string", "option"=>true),
				"update_time"	=>array("type"=>"string","default"=>date("Y-m-d H:i:s"))
			);
		return $fields;
	}
	function register($param){
		$ret = $this->model->registerApp($param);
		if (empty($ret)) {
			$status = bf\core\Status::error();
			$status->error = $this->model->last_error;
			return $status;
		}
		$status = bf\core\Status::status();
		$param["id"] = $ret;
		$status->data = $param;
		return $status;
	}
	function query($param){
		$data = $this->model->getAppList($param);
		if (empty($data)) {
			$status = bf\core\Status::error();
			$status->error = $this->model->last_error;
			return $status;
		}
		$status = bf\core\Status::status();
		$status->data = $data;
		return $status;
	}
	function queryBuildsParam(){
		$fields = array(
				"id"=>array("type"=>"int","option"=>true),
				"package"=>array("type"=>"string","option"=>true)
			);
		return $fields;
	}
	function queryBuilds($param){
		if ( empty($param) ) {
			return bf\core\Status::errorParam();
		}
		$builds = $this->model->queryAppBuilds($param);
		$status = bf\core\Status::status();
		$app = $this->model->queryApp( $param );
		$app->list = $builds;
		$status->data = $app;
		return $status;
	}
	function addBuildParam(){
		$fields = array(
				"appid"			=>array("type"=>"int"),
				"version"		=>array("type"=>"string"),
				"build"		=>array("type"=>"string"),
				"channel"		=>array("type"=>"string", "option"=>true),
				"developer"		=>array("type"=>"string", "option"=>true),
				"description"	=>array("type"=>"string", "option"=>true),
			);
		return $fields;
	}
	function addBuild($param){
		$ret = $this->model->addAppBuild($param);
		if (empty($ret)) {
			$status = bf\core\Status::error();
			$status->error = $this->model->last_error;
			return $status;
		}
		$status = bf\core\Status::status();
		$param["id"] = $ret;
		$status->data = $param;
		return $status;
	}
}
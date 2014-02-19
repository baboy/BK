<?php
class AppHandler extends bf\core\HttpRequestHandler{
	function init(){
		$this->getModel("App");
	}
	function admin($param){
		return null;
	}
	function registerParam(){
		$fields = array();
		return $fields;
	}
	function register(){
		$status = bf\core\Status::status();

		return $status;
	}
	function queryProduct(){
		$status = bf\core\Status::status();
		$status->data = $this->model->queryAppList(null);
		return $status;
	}
	function queryBuildsParam(){
		$fields = array(
				"id"=>array("type"=>"int")
			);
		return $fields;
	}
	function queryBuilds($param){
		$status = bf\core\Status::status();
		$app = $this->model->queryApp($param);
		$app->list = $this->model->queryAppBuilds($param);
		foreach ($app->list as $key => &$build) {
			$build->download_url = $GLOBALS["relatvie_path"]."/app/build/download/".$build->id;
		}
		$status->data = $app;
		return $status;
	}
	function mgr($param){
		return null;
	}
	function addBuildParam(){
		$fields = array(
				"appid"=>array("type"=>"int"),
				"version"=>array("type"=>"string"),
				"channel"=>array("type"=>"string"),
				"build"=>array("type"=>"string"),
				"description"=>array("type"=>"string"),
				"download_url"=>array("type"=>"string"),
			);
		return $fields;
	}
	function addBuild($param){
		$status = bf\core\Status::status();
		$id = $this->model->addAppBuild($param);
		if (empty($id)) {
			$status = bf\core\Status::error();
			$status->error = $this->model->db->last_error;
			return $status;
		}
		$param["id"] = $id;
		$status->data = $param;
		return $status;
	}
	function downloadParam(){
		$fields = array(
				"build_id"=>array("type"=>"int")
			);
		return $fields;
	}
	function download($param){
		$apps = $this->model->queryAppBuilds($param);
		$app = null;
		if (!empty($apps)) {
			$app = $apps[0];
		}
		$fn = $app->name."-".(empty($app->channel)?"":$app->channel)."-".$app->version.".".$app->build.".apk";
		$url = $app->download_url."?fn=$fn";
		header('Content-type: application/vnd.android.package-archive');
		header('Content-Disposition: attachment; filename="'.$fn.'"');
		header( "HTTP/1.1 301 Moved Permanently" );
    	header("Location: ".$url);
		return null;
	}
}
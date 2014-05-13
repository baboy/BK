<?php

class MediaHandler extends bk\core\HttpRequestHandler{
	function init(){
		//$this->getModel("Media");
		global $media;
		$this->model = $media;
	}
	function queryParam(){
		$fields = array(
				"module"=>array("type"=>"string"),
				"offset"=>array("type"=>"int","default"=>0),
				"count"=>array("type"=>"int","default"=>30),
				"node"=>array("type"=>"string","default"=>"solo"),
			);
		return $fields;
	}
	function query($param){
		$data = $this->model->queryList($param);
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}
	function detailParam(){
		$fields = array(
				"sid"=>array("type"=>"string"),
				"module"=>array("type"=>"string"),
			);
		return $fields;
	}
	function detail($param){
		$action = "query".ucfirst(strtolower($param["module"]))."Detail";
		$data = $this->model->$action($param["sid"]);
		$status = bk\core\Status::status();
		$status->data = $data;
		$status->param = $param;
		$status->action = $action;
		return $status;
	}

	function recentParam(){
		$fields = array(
				"sid"=>array("type"=>"string","option"=>true),
				"count"=>array("type"=>"int","option"=>true, "default"=>20),
				"module"=>array("type"=>"string")
			);
		return $fields;
	}
	function recent($param){
		$data = $this->model->queryRecent($param["module"], empty($param["sid"]) ? null:$param["sid"],$param["count"]);
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}
	function queryRecommend($param){
		$data = $this->model->queryRecommend();
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}
	function queryCategoriesParam(){
		$fields = array(
				"module"=>array("type"=>"string")
			);
		return $fields;
	}
	function queryCategories($param){
		$status = bk\core\Status::status();
		$categories = $this->model->queryCategories($param);
		foreach ($categories as $key => $cate) {
			$cate->api = array(
					"hot"=>$GLOBALS["site_url"]."/api/v1/news/query/",
					"query"=>$GLOBALS["site_url"]."/api/v1/news/query/"
				);
		}
		$status->data = $categories;
		return $status;
	}
}
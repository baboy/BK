<?php

class FileManagerHandler extends bk\core\HttpRequestHandler{

	function init(){
		$this->getModel("MediaFile");
	}
	function addParam(){
		$fields = array(
				"title"=>array("type"=>"string"),
				"node"=>array("type"=>"string","default"=>"dir"),
				"type"=>array("type"=>"string","option"=>true),
				"ext"=>array("type"=>"string","option"=>true),
				"url"=>array("type"=>"string","option"=>true),
				"pid"=>array("type"=>"int","default"=>0),
				"size"=>array("type"=>"int","default"=>0),
				"description"=>array("type"=>"string","option"=>true),
			);
		return $fields;
	}
	function add($param){
		$status = bk\core\Status::status();
		$fid = $this->model->add($param);
		if (empty($fid)){
			return bk\core\Status::error();
		}
		$param["id"] = $fid;
		$status = bk\core\Status::status();
		$status->data = $param;
		return $status;
		
	}
	function update($param){
		$status = bk\core\Status::status();
		$status->data = $this->model->update(array("a"=>"1","b"=>2), array("c"=>"3","d"=>4));
		return $status;
	}
	function queryParam(){
		$fields = array(
				"node"=>array("type"=>"string","default"=>"file"),
				"pid"=>array("type"=>"string","default"=>"0"),
			);
		return $fields;
	}
	function query($param){
		$status = bk\core\Status::status();
		$status->data = $this->model->query($param);
		return $status;
	}
	function dirs(){
		$status = bk\core\Status::status();
		$status->data = $this->model->query(array("node"=>"dir"));
		return $status;
	}
}
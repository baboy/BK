<?php
class UgcHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("Ugc");
	}
	function queryParam(){
		$fields = array(
				"appid"=>array("type"=>"string"),
				"offset"=>array("type"=>"int","default"=>0),
				"count"=>array("type"=>"int","default"=>30)
			);
		return $fields;
	}
	function query($param){
		$data = $this->model->queryList($param);
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}

	function postParam(){
		$fields = array(
                        "appid"         =>  array('type'=>"string"),
                        "content"       =>  array("type"=>"string"),
                        "tags"          =>  array("type"=>"string","option"=>true),

                        "images"           =>  array("type"=>"json","option"=>true),
                        "video"           =>  array("type"=>"json","option"=>true),

                        "live"          =>  array("type"=>"string","option"=>true, "default"=>0),

                        "duration"      =>  array("type"=>"int","option"=>true),
                        "lat"           =>  array("type"=>"float","option"=>true),
                        "lng"           =>  array("type"=>"float","option"=>true),
                        "addr"          =>  array("type"=>"string","option"=>true),
                        "metadata"      =>  array("type"=>"string","option"=>true)
                    );
		return $fields;
	}
	function post($param){
		$ugc = $param;
		if(isset($ugc["images"])){
			unset($ugc["images"]);
		}
		if(isset($ugc["video"])){
			unset($ugc["video"]);
		}
		$sid = $this->model->addUgc($ugc);
		if(!$sid){
			$status = bk\core\Status::error();
			$status->error = $this->model->db->last_error;
			return $status;
		}
		if(isset($param["images"])){
			$images = &$param["images"];
			for($i = 0, $n = count($images); $i < $n; $i++){
				$image = $images[$i];
				$_id = $this->model->addAttr($sid,"images",$image["url"], $image["thumbnail"]);
				if($_id){
					$param["images"][$i]["id"] = $_id;
				}else{
					$param["images"][$i]["error"] = $this->model->db->last_error;
				}
			}
		}
		if(isset($param["video"])){
			$video = $param["video"];
			$_id = $this->model->addAttr( $sid,"videos",$video["url"], $video["thumbnail"] );
			if($_id){
				$param["video"]["id"] = $_id;
			}else{
				$param["video"]["error"] = $this->model->db->last_error;
			}
		}
		$status = bk\core\Status::status();
		$param["sid"] = $sid;
		$status->param = $param;
		return $status;
	}
}
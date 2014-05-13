<?php
class FeedbackHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("Feedback");
	}

	function postParam(){
		$fields = array(
				"device_id"=>array("type"=>"string"),
				"platform"=>array("type"=>"string"),
				"product_id"=>array("type"=>"string"),
				"os"=>array("type"=>"string"),
				"version"=>array("type"=>"string"),
				"build"=>array("type"=>"string"),
				"content"=>array("type"=>"string"),
				"device_name"=>array("type"=>"string", "option"=>true),
			);
		return $fields;
	}
	function post($param){
		$ret = $this->model->add($param);
		$status = bk\core\Status::status();
		if(!$ret)
			$status->error = $this->model->db->last_error;
		$status->param = $param;
		return $status;
	}
}
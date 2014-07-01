<?php
class APSHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("APS");
	}
	// device 参数
	function registerParam(){
		$fields = array(
				"package"=>array("type"=>"string","option"=>true),
				"product_id"=>array("type"=>"string","alias"=>"package"),
				"build"=>array("type"=>"int"),
				"version"=>array("type"=>"string"),
				"device_id"=>array("type"=>"string"),
				"mac"=>array("type"=>"string","option"=>true),
				"os"=>array("type"=>"string"),
				"os_version"=>array("type"=>"string", "option"=>true),
				"resolution"=>array("type"=>"string", "option"=>true),
				"device_name"=>array("type"=>"string", "option"=>true),
				"manufacturer"=>array("type"=>"string", "option"=>true),
				"cpu"=>array("type"=>"string", "option"=>true),
				"token"=>array("type"=>"string"),
				"sdk_version"=>array("type"=>"string", "option"=>true),
				"access"=>array("type"=>"string", "option"=>true),
				"platform"=>array("type"=>"string", "option"=>true),
				"channel"=>array("type"=>"string", "option"=>true)
			);
		return $fields;
	}
	function register($param){
		$device = $this->model->getUniqueDevice($param["device_id"],$param["product_id"]);
		//如果没有注册，就注册设备
		if(empty($device)){
			$id = $this->model->registerDevice($param);
			if (empty($id)) {
				$status = bk\core\Status::error();
				$status->error = $this->model->db->last_error;
				return $status;
			}
			$param["id"] = $id;
		}else{//更新
			$id = $device["id"];
			$ret = $this->model->updateDevice($param,array("id"=>$id));
		}
		if(empty($param["id"])){
			$status = bk\core\Status::error();
			$status->error = $this->model->db->last_error;
			$status->data = $param;
			return $status;
		}
		$status = bk\core\Status::status();
		return $status;
	}
	function notifyParam(){
		$fields = array(
				"msg"=>array("type"=>"string", "option"=>true),
			);
		return $fields;
	}
	function notify($param){
		$token = "591f325b d9b6c8ec e9164092 7b70bbbd 0e87886d 8ae0a449 69951333 7678599f";
		$token = "591f325bd9b6c8ece91640927b70bbbd0e87886d8ae0a449699513337678599f";
		$token = "7f0a44d8962c5abe20cf5bdf20b6ed21b6ffa00d22a7bf09265b3cfbc438ee77";
		//$token = "2551019205bc2a20d8b0cae0be3ce3a47b81f43d907f60ebea6263ab36688a95";
		$json = notify($token,$param["msg"]);
  		$status = bk\core\Status::status();
  		$status->data = $json;
  		return $status;
	}
}


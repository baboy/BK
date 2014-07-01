<?php
class LoggerHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("AppLogger");
	}
	// device 参数
	function logDeviceParam(){
		$fields = array(
				"appkey"=>array("type"=>"string"),
				"package"=>array("type"=>"string","option"=>true),
				"product_id"=>array("type"=>"string","alias"=>"package"),
				"build"=>array("type"=>"int"),
				"version"=>array("type"=>"string"),
				"device_id"=>array("type"=>"string"),
				"mac"=>array("type"=>"string"),
				"os"=>array("type"=>"string"),
				"os_version"=>array("type"=>"string", "option"=>true),
				"resolution"=>array("type"=>"string", "option"=>true),
				"device_name"=>array("type"=>"string", "option"=>true),
				"manufacturer"=>array("type"=>"string", "option"=>true),
				"cpu"=>array("type"=>"string", "option"=>true),
				"sdk_version"=>array("type"=>"string", "option"=>true),
				"access"=>array("type"=>"string", "option"=>true),
				"platform"=>array("type"=>"string", "option"=>true),
				"ip"=>array("type"=>"string", "option"=>true,"default"=>"{ip}"),
				"channel"=>array("type"=>"string", "option"=>true)
			);
		return $fields;
	}
	//events 参数
	function logEventsParam(){
		$fields = array(
				"event"=>array("type"=>"string"),
				"session_id"=>array("type"=>"string","option"=>true),
				"group"=>array("type"=>"string"),
				"duration"=>array("type"=>"int", "option"=>true),
				"element"=>array("type"=>"string","option"=>true),
				"post_date"=>array("type"=>"string", "alias"=>"date","option"=>true),
			);
		return $fields;
	}
	//errors 参数
	function logErrorsParam(){
		$fields = array(
				"description"=>array("type"=>"string"),
				"session_id"=>array("type"=>"string","option"=>true),
				"post_date"=>array("type"=>"string", "alias"=>"date"),
			);
		return $fields;
	}
	function logParam(){
		$fields = array(
				"data" => array("type"=>"json","name"=>"数据内容"),
				"action" => array("type"=>"json","option"=>true),
			);
		return $fields;
	}
	function log($param){
		$data = $param["data"];
		$act = null;
		if(!isset($data["events"]) && !isset($data["logs"]) && !isset($param["action"])){
			$act = "start";
		}
		//检查device 参数
		$deviceParam = $this->checkFields($this->logDeviceParam(),$data["device"],$data["device"]);
		if(!$deviceParam->isSuccess())
			return $deviceParam;
		$device = $this->model->getUniqueDevice($deviceParam->data["appkey"],$deviceParam->data["device_id"],$deviceParam->data["product_id"]);
		//如果没有注册，就注册设备
		if(empty($device)){
			if($deviceParam->data["appkey"] == "5289ac6056240b9bf11f08d3"){
				$deviceParam->data["os_version"] = $deviceParam->data["os"];
				$deviceParam->data["os"] = "android";
			}
			$sno = $this->model->registerDevice($deviceParam->data);
			if (empty($sno)) {
				$status = bk\core\Status::error();
				$status->error = $this->model->db->last_error;
				return $status;
			}
			$device = $deviceParam->data;
			$device["sno"] = $sno;
		}else{//更新
			$sno = $device["sno"];
			$p = $deviceParam->data;
			$ret = $this->model->updateDevice($p,array("sno"=>$sno));
		}
		$sno = $device["sno"];
		
		if($act == "start"){
			$d = $deviceParam->data;
			$d["sno"] = $sno;
			$this->model->addDeviceLog($d);
		}
		
		$ver = $deviceParam->data["version"];
		$build = $deviceParam->data["build"];

		$status = bk\core\Status::status();
		$status->data["sno"] = $sno;
		if(!empty($data["events"])){
			$ids = array();
			foreach ($data["events"] as $key => $evt) {
				$r = $this->checkFields($this->logEventsParam(),$evt);
				if(!$r->isSuccess()){
					continue;
				}
				$value = $r->data;
				$value["sno"] = $sno;
				$value["version"] = $ver;
				$value["build"] = $build;
				$t = time();
				if(isset($value["date"])){
					$t = strtotime($value["date"]);
					unset($value["date"]);
				}
				$value["post_date"] = date("Y-m-d H:i:s",$t>0?$t:time());
				$event_id = $this->model->addEventLog($value);
				if(!empty($event_id)){
					$ids[] = $event_id;
				}else{
					$ids[] = $this->model->db->last_error;
					//$ids[] = $value;
				}
			}
			$status->data["events"] = $ids;
		}
		if(!empty($data["logs"])){
			$ids = array();
			foreach ($data["logs"] as $key => $value) {
				$value["sno"] = $sno;
				$value["sno"] = $sno;
				$value["version"] = $ver;
				$value["build"] = $build;
				$t = time();
				if(isset($value["date"])){
					$t = strtotime($value["date"]);
					unset($value["date"]);
				}
				$value["post_date"] = date("Y-m-d H:i:s",$t>0?$t:time());
				$event_id = $this->model->addErrorLog($value);
				if(!empty($event_id)){
					$ids[] = $event_id;
				}
			}
			$status->data["logs"] = $ids;
		}
		$status->act = $act;
		return $status;
	}
}


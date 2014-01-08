<?php
class Logger{
	private $model;
	function getModel($modelName){
		if (empty($this->model)) {
			require_once dirname(__FILE__)."/../m/$modelName.php";
			$this->model = $this->db->getModel($modelName);
		}
		return $this->model;
	}
	function init(){
		$this->getModel("LogModel");
	}
	function logParam(){
		$fields = array(
				"data" => array("type"=>"json","name"=>"数据内容")
			);
		return $fields;
	}
	function checkFields($fields, $value){
		$validator = new bf\core\Validator($fields);
		$validator->setValue($value);
		return $validator->check();
	}
	// device 参数
	function logDeviceParam(){
		$fields = array(
				"appkey"=>array("type"=>"string"),
				"package"=>array("type"=>"string"),
				"product_id"=>array("type"=>"string"),
				"build"=>array("type"=>"int"),
				"version"=>array("type"=>"string"),
				"device_id"=>array("type"=>"string"),
				"mac"=>array("type"=>"string"),
				"os"=>array("type"=>"string"),
				"resolution"=>array("type"=>"string", "option"=>true),
				"device_name"=>array("type"=>"string", "option"=>true),
				"manufacturer"=>array("type"=>"string", "option"=>true),
				"cpu"=>array("type"=>"string", "option"=>true),
				"sdk_version"=>array("type"=>"string", "option"=>true),
				"access"=>array("type"=>"string", "option"=>true),
				"platform"=>array("type"=>"string", "option"=>true),
				"channel"=>array("type"=>"string", "option"=>true)
			);
		return $fields;
	}
	//events 参数
	function logEventsParam(){
		$fields = array(
				"event"=>array("type"=>"string"),
				"group"=>array("type"=>"string"),
				"element"=>array("type"=>"string"),
				"post_date"=>array("type"=>"string", "alias"=>"date"),
			);
		return $fields;
	}
	//errors 参数
	function logErrorsParam(){
		$fields = array(
				"description"=>array("type"=>"string"),
				"post_date"=>array("type"=>"string", "alias"=>"date"),
			);
		return $fields;
	}
	function log($param){
		$param = $param["data"];
		//检查device 参数
		$deviceParam = $this->checkFields($this->logDeviceParam(),$param["device"]);
		if(!$deviceParam->isSuccess())
			return $deviceParam;
		$device = $this->model->getUniqueDevice($deviceParam->data["appkey"],$deviceParam->data["product_id"],$deviceParam->data["device_id"],$deviceParam->data["package"]);
		//如果没有注册，就注册设备
		if(empty($device)){
			$sno = $this->model->registerDevice($deviceParam->data);
			if (empty($sno)) {
				$status = Status::error();
				$status->error = $this->model->last_error;
				return $status;
			}
			$device = $deviceParam->data;
			$device["sno"] = $sno;
		}
		$sno = $device["sno"];
		$ver = $deviceParam->data["version"];
		$build = $deviceParam->data["build"];

		$status = bf\core\Status::status();
		$status->data["sno"] = $sno;
		if(!empty($param["events"])){
			$ids = array();
			foreach ($param["events"] as $key => $value) {
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
				}
			}
			$status->data["events"] = $ids;
		}
		if(!empty($param["errors"])){
			$ids = array();
			foreach ($param["errors"] as $key => $value) {
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
			$status->data["errors"] = $ids;
		}
		return $status;
	}
}


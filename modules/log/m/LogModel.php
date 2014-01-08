<?php
define("TABLE_DEVICE", "device");
define("TABLE_EVENT", "event");
define("TABLE_LOG", "log");
class LogModel extends bf\core\Dao{
	function getUniqueDevice($appkey,$product_id,$device_id,$package){
		$param = array("appkey"=>$appkey,"product_id"=>$product_id,"device_id"=>$device_id,"package"=>$package);
		$records = $this->select(TABLE_DEVICE, null, $param);
		return count($records)>=1?objectToArray($records[0]):false;
	}
	function registerDevice($param){
		$ret = $this->insert(TABLE_DEVICE, $param);
		return $ret;
	}
	function addEventLog($param){
		$ret = $this->insert(TABLE_EVENT, $param);
		return $ret;
	}
	function addErrorLog($param){
		$ret = $this->insert(TABLE_LOG, $param);
		return $ret;
	}
}
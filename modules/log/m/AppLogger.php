<?php
define("TABLE_DEVICE", "device");
define("TABLE_EVENT", "event");
define("TABLE_LOG", "log");
class AppLogger extends bk\core\Model{
	function getUniqueDevice($appkey,$device_id,$package){
		$param = array("appkey"=>$appkey,"device_id"=>$device_id,"package"=>$package);
		$records = $this->db->select(TABLE_DEVICE, null, $param);
		return count($records)>=1?objectToArray($records[0]):false;
	}
	function registerDevice($param){
		$ret = $this->db->insert(TABLE_DEVICE, $param);
		return $ret;
	}
	function updateDevice($param,$cond){
		$ret = $this->db->update(TABLE_DEVICE, $param, $cond);
		return $ret;
	}
	function addEventLog($param){
		$ret = $this->db->insert(TABLE_EVENT, $param);
		return $ret;
	}
	function addErrorLog($param){
		$ret = $this->db->insert(TABLE_LOG, $param);
		return $ret;
	}
}
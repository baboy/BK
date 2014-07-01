<?php
define("TABLE_DEVICE_TOKEN", "ios_device_token");
class APS extends bk\core\Model{
	function getUniqueDevice($device_id, $product_id){
		$param = array("device_id"=>$device_id, "product_id"=>$product_id);
		$records = $this->db->select(TABLE_DEVICE_TOKEN, null, $param);
		return count($records)>=1?objectToArray($records[0]):false;
	}
	function registerDevice($param){
		$ret = $this->db->insert(TABLE_DEVICE_TOKEN, $param);
		return $ret;
	}
	function updateDevice($param,$cond){
		$ret = $this->db->update(TABLE_DEVICE_TOKEN, $param, $cond);
		return $ret;
	}
}
<?php

namespace bf\core;

function objectToArray($d) {
	if (is_object($d)) {
		$d = get_object_vars($d);
	}
	if (is_array($d)) {
		return array_map(__FUNCTION__, $d);
	}
	else {
		return $d;
	}
}
class Validator{
	private $fields = null;
	private $value = null;
	function __construct($fields){
		$this->fields = $fields;
	}
	function setValue($value){
		$this->value = $value;
	}
	function check(){
		$params = array();
		$status = Status::status();
		foreach( $this->fields as $k=>$v ){
			$name = !empty($v["name"]) ? $v["name"]:$k;
			$alias = isset($v["alias"]) ? $v["alias"] : false;
			$val = $this->getRequestValueByConfig($k, $v);
			if (empty($val) && $alias) {
				$val2 = $this->getRequestValueByConfig($alias, $arr[$alias]);
				if (!empty($val2)) {
					$val = $val2;
				}
			}
			if ($val===false) {
				$status = Status::errorParam();
				$status->msg = "$name($k)字段错误";
				break;
			}
			if ($val === null) {
				continue;
			}
			$params[$k] = $val; 			
		}
		if(!empty($params))
			$status->data = $params;
		return $status;
	}
	function getRequestValueByConfig($k, $fieldConf){
		if (!isset($fieldConf) ) {
			return $this->requestValue($k);
		}
		$option = isset($fieldConf["option"]) ? true : false;
		$hasDefVal = isset($fieldConf["default"]);
		$option |= $hasDefVal;
		$defVal = $hasDefVal ? $fieldConf["default"] : null;
		$type = $fieldConf["type"];

		$val = $this->requestValue($k);
		if($val && isset($v["trim"]) && $fieldConf["trim"]){
			$val = trim($val);
		}
		$isZero = ( $val === "0" || $val === 0 );
		//如果必须 但是没值
		if(!$option && empty($val) && !$isZero && !$hasDefVal) 
			return false;
		if($option && empty($val) && !$isZero && !$hasDefVal)
			return null;
		if($type == "int"){
			$val = intval($val);
		}else if($type == "float"){
			$val = floatval($val);
		}else if($type == "json"){
			$val = json_decode(stripslashes($val));
			$val = empty($val)?$val:objectToArray($val);
		}
		//如果为空并且是必须的 并且没有默认值
		if( empty($val) && !$isZero && !$option && !$hasDefVal )
			return false;
			
		if(empty($val) && $hasDefVal)
			$val = $defVal;

		return $val;
	}
	function requestValue($k){
		if(!empty($this->value)){
			if (isset($this->value["$k"])) {
				return trim( $this->value[$k] );
			}
			return false;
		}
		if( isset($_POST[$k]) ){
			return trim( $_POST[$k] );
		}
		if( isset($_GET[$k]) ){
			return trim( $_GET[$k] );
		}
		return false;
	}
}
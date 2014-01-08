<?php
	function requestMethod(){
		$method = $_SERVER["REQUEST_METHOD"];
		$_method = requestValue("_method");
		if( in_array($_method, array("GET","PUT","DELETE","POST","HEAD")) ){
			$method = $_method;
		}
		switch($method){
			case "GET":

				break;
			case "POST":

				break;
			case "PUT":
				parse_str(file_get_contents('php://input'), $args);
				if(!isset($_POST))
					$_POST = array();
				foreach($args as $k=>$v)
					$_POST[$k] = $v;
				break;
		}
		return $method;
	}

	function getClientIp(){ 
		if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
			$ip = getenv("HTTP_CLIENT_IP"); 
		else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
			$ip = getenv("HTTP_X_FORWARDED_FOR"); 
		else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
			$ip = getenv("REMOTE_ADDR"); 
		else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
			$ip = $_SERVER['REMOTE_ADDR']; 
		else 
			$ip = "unknown"; 
		return($ip); 
	}
	function getClientLongIp(){ 
		return ip2long(getClientIp()); 
	}
	function requestValue($k){
		if( isset($_POST[$k]) ){
			return trim( $_POST[$k] );
		}
		if( isset($_GET[$k]) ){
			return trim( $_GET[$k] );
		}
		return false;
	}
	function _getHttpRequestConfigValue($k, $arr){
		if (!isset($arr[$k]) ) {
			return requestValue($k);
		}
		$v = $arr[$k];
		$option = isset($v["option"]) ? true : false;
		$hasDefVal = isset($v["default"]);
		$option |= $hasDefVal;
		$defVal = $hasDefVal ? $v["default"] : null;
		$type = $v["type"];
		$val = requestValue($k);
		if($val && isset($v["trim"]) && $v["trim"]){
			$val = trim($val);
		}

		//如果必须 但是没值
		if(!$option && empty($val) && $val!=="0" && $val!==0 && !$hasDefVal) 
			return false;
		if($option && empty($val) && $val!=="0" && $val!==0 && !$hasDefVal)
			return null;

		if($type == "int"){
			$val = intval($val);
		}else if($type == "float"){
			$val = floatval($val);
		}
		if( empty($val) && !$option && !$hasDefVal )
			return false;
			
		if(empty($val) && $hasDefVal)
			$val = $defVal;

		return $val;
	};
	function checkRequestFields($arr){
		
		$ret = array();
		foreach( $arr as $k=>$v ){
			$alias = isset($v["alias"]) ? $v["alias"] : false;
			$val = _getHttpRequestConfigValue($k, $arr);
			if (empty($val) && $alias) {
				$val2 = _getHttpRequestConfigValue($alias, $arr);
				if (!empty($val2)) {
					$val = $val2;
				}
			}
			if ($val===false) {
				//echo "key:".$k.",".$alias.",".$getValue($alias, $arr);
				return false;
			}
			if ($val === null) {
				continue;
			}
			$ret[$k] = $val; 			
		}
		return $ret;
	}
	function startsWith($haystack, $needle) {
    	$length = strlen($needle); 
 		return (substr($haystack, 0, $length) === $needle); 
	}
	function endsWith($haystack, $needle) {
  		$length = strlen($needle); 
 		$start =  $length *-1; //negative
 		return (substr($haystack, $start, $length) === $needle); 
	}
	function isEmail($s){
		$re = "/^[-_A-Za-z0-9]+[-A-Za-z0-9_\.]*@[-_A-Za-z0-9]+\.[-_A-Za-z0-9\.]+$/";
		return $s?preg_match($re, $s):false;
	}
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

	function encodeSeq($num){
		$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$to = strlen($dict);
		$ret = '';
		do{
			$ret = $dict[bcmod($num, $to)] . $ret;
			$num = bcdiv($num, $to);
		} while ($num > 0); 
		return $ret;
	}
	
	function decodeSeq($num){
		$num = strval($num);
		$dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$from = strlen($dict);
		$len = strlen($num);
		$dec = 0;
		for($i = 0;$i < $len;$i++){
			$pos = strpos($dict, $num[$i]);
			$dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
		}
		return $dec;
	}
?>

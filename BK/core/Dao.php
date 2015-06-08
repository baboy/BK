<?php
namespace bk\core;
use bk\core\database\DB as DB;
include_once "database/db.inc";
class Dao extends DB{
	private $config = null;
	function __construct($config = null){
		$this->config = $config;
	}
	function getSql($key){
		foreach($this->config as $mod=>$sqls){
			if (isset($sqls[$key])) {
				return $sqls[$key];
			}
		}
		return null;
	}
	function __call($name, $args){
		$sql = $this->getSql($name);
		if (!empty($sql)) {
			if(!empty($args)){
				foreach ($args[0] as $key => $value) {
					$sql = str_replace("{".$key."}", $value, $sql);
				}
			}
			try{
				$ret = $this->query($sql);
			}catch(Exception $e){

			}
			return $ret;
		}
		return false;
		//$functionName = "db_$name";
		//$function = new ReflectionFunction($functionName);
	 	//return $function->invokeArgs($args);
	}
	function execute($sql, $param=null){

		$re_table = '/\[([^ ]+)\]/';
		$sql = preg_replace($re_table, DB_TABLE_PREFIX."$1", $sql);
		if($param){
			$re_placeholder = '/\{([^ ]+)\}/';
			$n = preg_match_all($re_placeholder, $sql, $m);
			if($n && $m){
				$m = $m[1];
				foreach($m as $k){
					$sql = str_replace("{{$k}}", addslashes($param[$k]), $sql);
				}
			}
		}
		return parent::execute($sql);
	}
	function getModel($modelName){
		return new $modelName($this->config);
	}
}
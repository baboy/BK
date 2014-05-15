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
	function getModel($modelName){
		return new $modelName($this->config);
	}
}
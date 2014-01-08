<?php
namespace bf\core;
use bf\core\database\DB as DB;
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
		if(!empty($args)){
			foreach ($args[0] as $key => $value) {
				$sql = str_replace("{".$key."}", $value, $sql);
			}
		}
		$ret = $this->query($sql);
		return $ret;
	}
	function execute(){
	}
	function getModel($modelName){
		return new $modelName($this->config);
	}
}
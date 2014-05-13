<?php
namespace bk\core\database;
require_once 'database.inc';

class DB{
	var $last_error = null;
	public function __construct(){
	}
	private function checkParam ($param){
		$p = array();
		foreach ($param as $key => $value) {
			if(!startsWith($key, "`"))
				$key = "`$key`";
			$p[$key] = $value;
		}
		return $p;
	}
	private function setCondition($_db, $conditions){
		if ( !is_array($conditions) || empty($conditions) ) {
			return;
		}
		$arr = array();
		foreach ($conditions as $k => $v ) {
			if (!is_numeric($k)) {
				array_push($arr, array($k, $v));
				continue;
			}
			if (!is_array($v)) {
				$conditions = array($conditions);
				break;
			}
		}
		if (!empty($arr)) {
			$conditions = $arr;
		}
		if($conditions){
			foreach($conditions as $cond ) { 
				if(count($cond)<2)
					continue;

				$field = $cond[0];
				// if (!startsWith($field, "`")) {
				// 	$field = "`$field`";
				// }
				$where = $cond[1];
				$operator = count($cond)>=3?$cond[2]:null;
				$_db->condition("`$field`",$where, $operator);
			}
		}
		return $_db;
	}
	
	private function expression($_db, $expression){
		
	}
	
	private function range($_db, $range){
		if ( !is_array($range) || count($range)==0) {
			return;
		}
		if($range){
			$start = $range[0];
			$end = $range[1];
			$_db->range($start, $end);
		}
		return $_db;
	}
	
	private function order($_db, $order){
		if ( !is_array($order) || count($order)==0) {
			return;
		}
		if($order){
			$key = $order[0];
			$direction = $order[1];
			$_db->orderBy($key, $direction);
		}
		return $_db;
	}
	
	public function insert($table, $param){
		$this->last_error = null;
		$ret = false;
		try {
			$param = $this->checkParam($param);
			$ret = db_insert($table)->fields($param)->execute();	
		}
		catch (\PDOException $e){
			$ret = false;
			$this->last_error = $e;
		} 
		catch (Exception $e) {
			$ret = false;
			$this->last_error = $e;
		}
		return $ret;
	}
	
	public function select($table, $fields=null, $conditions=null, $range=null,$order=null){
		$this->last_error = null;
		$_db = db_select($table, "t");
		if ($fields) {
			//$_db->fields("t", $fields);
			foreach ($fields as $key => $value) {
				 if (strpos( $value, " as ") > 0) {
				 	$a = explode(" as ",$value);
				 	$_db->addField("t", $a[0], $a[1]);
				 }else{
				 	$_db->addField("t", $value);
				 }
			}
		}else{
			$_db->fields("t");
		}
		$this->setCondition($_db, $conditions);
		$this->order($_db, $order);
		$this->range($_db, $range);
		$ret = false;
		try {
			$ret = $_db ->execute()->fetchAll();
		} catch (Exception $e) {
			$this->last_error = $e;
		}
		return $ret;
	}
	
	public function query($sql){
		$this->last_error = null;
		$ret = false;
		try {
			$ret = db_query($sql)->fetchAll();
		} catch (Exception $e) {
			$this->last_error = $e;
		}
		return $ret;
	}

	public function update($table, $param, $conditions){
		$this->last_error = null;
		$ret = false;
		try {
			$_db = db_update($table);
			$this->setCondition($_db, $conditions);
			$param = $this->checkParam($param);
			$_db->fields($param);
			$ret = $_db->execute();
		} catch (Exception $e) {
			print $e;
			$this->last_error = $e;
		}
		return $ret;
	}

	public function delete($table, $conditions){
		$this->last_error = null;
		$_db = db_delete($table);
		$this->setCondition($_db, $conditions);
		$ret = false;
		try {
			$ret = $_db->execute();
		} catch (Exception $e) {
			$this->last_error = $e;
		}
		return $ret;
	}
}
$db = new DB();
global $db;
?>
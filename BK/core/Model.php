<?php
namespace bk\core;
class Model{
	function __construct($config = null){
		$this->config = $config;
		global $__DB__;
		$this->db = $__DB__;
	}
	function getWhereSql($cond, $table_alias = null){
		$sql = null;
		foreach ($cond as $key => $value) {
			if (empty($sql)) {
				$sql = " ";
			}else{
				$sql .= " AND ";
			}
			$field = sprintf("`%s`='%s' ", $key, addslashes($value));

			if (!empty($table_alias)) {
				$field = sprintf("%s.%s", $table_alias, $field);
			}
			$sql .= $field;
		}
		return " ".$sql;
	}
	function getSetSql($param, $table_alias = null){
		$sql = null;
		foreach ($param as $key => $value) {
			if (empty($sql)) {
				$sql = " ";
			}else{
				$sql .= ", ";
			}
			$field = sprintf("`%s`='%s'", $key, addslashes($value));
			if (!empty($table_alias)) {
				$field = sprintf("%s.%s", $table_alias, $field);
			}
			$sql .= $field;
		}
		return " ".$sql;
	}
	function getInsertSql($table,$param){
		$fields = array();
		$values = array();
		foreach ($param as $key => $value) {
			$fields[] = sprintf("`%s`", $key);
			$values[] = sprintf("'%s'", addslashes($value));
		}
		$sql = "INSERT INTO %s(%s) VALUES(%s)";
		$sql = sprintf($sql,$table, implode(",", $fields), implode(",", $values));
		return $sql;
	}
}
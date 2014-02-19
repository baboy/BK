<?php

define('TABLE_MEDIA_FILE', 'media_file');

class MediaFile extends bk\core\Model{
	
	function add($param){
		//$sql = $this->getInsertSql(TABLE_MEDIA_FILE, $param);
		$ret = $this->db->insert(TABLE_MEDIA_FILE, $param);
		return $ret;
	}
	function update($param, $cond){
		$sql = "UPDATE %s SET %s WHERE %s";
		$sql = sprintf($sql,TABLE_MEDIA_FILE, $this->getSetSql($param, "t"), $this->getWhereSql($cond,"t"));
		echo $sql;
	}
	function query($cond){
		$files = $this->db->select(TABLE_MEDIA_FILE, null, $cond);
		return $files;
	}
}
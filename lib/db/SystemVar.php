<?php
define("TABLE_VAR", "var");

class SystemVar extends bk\core\Model{
	function set($k, $v, $group=false){
		$param = array("k" => $k, "v" => $v);
		$v = $this->get($k);
		if($v===false){
			$ret = $this->db->insert(TABLE_VAR, $param);
		}else{
			$ret = $this->db->update(TABLE_VAR, $param);
		}
		return $ret;
	}
	function get($k, $group=false){
		$cond = array("k"=>$k);
		if($group)
			$cond["group"] = $group;
		$ret = $this->db->select(TABLE_VAR, null,$cond);
		if (!empty($ret)) {
			$row = $ret[0];
			return $row->v;
		}
		return false;
	}
}
global $sysVar;
$sysVar = new SystemVar();
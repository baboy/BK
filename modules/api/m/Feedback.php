<?php
define("TABLE_FEEDBACK", "feedback");
class Feedback extends bk\core\Model{
	function add($param){
		$ret = $this->db->insert(TABLE_FEEDBACK, $param);
		return $ret;
	}
}
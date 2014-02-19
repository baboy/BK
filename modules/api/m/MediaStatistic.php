<?php

class MediaStatistic extends bk\core\Model{
	function view($sid){
		$sid = addslashes($sid);
		$sql = "UPDATE wp_media set views=views+1 WHERE id=$sid";
		$this->db->query($sql);
	}
}
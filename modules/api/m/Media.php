<?php
define('MEDIA', '');

class Media extends bf\core\Model{
	function query($param){
		$offset = empty($param["offset"])?0:intval($param["offset"]);
		$count = empty($param["count"])?20:intval($param["count"]);
		unset($param["offset"]);
		unset($param["count"]);
		$fields = array("id as sid","title","tip","tag","thumbnail","pic","duration","score","views","actors","director","area","pubdate");
		
		$medias = $this->db->select("media",$fields,$param,array($offset,$count));
		return $medias;
	}
	function queryDetail($sid){
		$sql = "SELECT m.id as sid, m.title, m.content,m.tip,m.tag,m.thumbnail,m.pic,m.score,m.views,m.actors,m.director,m.area,m.pubdate, v.sd, v.high, v.super, v.original, v.mp4 FROM wp_media m left join wp_media_video v on m.id = v.sid WHERE m.id='%s'";
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		return empty($medias)?null:$medias[0];
	}
	function queryRecent($module,$sid){
		$param = array(array("module", $module));
		if(!empty($sid))
			$param[] = array("id",$sid,"!=");
		
		$offset = empty($param["offset"])?0:intval($param["offset"]);
		$count = empty($param["count"])?20:intval($param["count"]);
		unset($param["offset"]);
		unset($param["count"]);
		$fields = array("id as sid","title","tip","tag","thumbnail","pic","duration","score","views","actors","director","area","pubdate");
		$medias = $this->db->select("media",$fields,$param,array($offset,$count));
		return $medias;
	}
}
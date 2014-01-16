<?php
define('MEDIA', '');

class Media extends bf\core\Model{
	function query($param){
		$offset = empty($param["offset"])?0:intval($param["offset"]);
		$count = empty($param["count"])?20:intval($param["count"]);
		unset($param["offset"]);
		unset($param["count"]);
		/*
		$fields = array("id as sid","title","tip","tag","thumbnail","pic","duration","score","views","actors","director","area","pubdate");
		
		$medias = $this->db->select("media",$fields,$param,array($offset,$count));
		return $medias;
		*/
		$where = null;
		foreach ($param as $key => $value) {
			if (empty($where)) {
				$where = " WHERE ";
			}else{
				$where .= " AND ";
			}
			if ($key == "exclude") {
				$sids = explode(",", $value);
				foreach ($sids as $i => $sid) {
					$where .= " t.id!=$sid ";
				}
				continue;
			}
			$where .= " t.$key='$value' ";
		}
		$sql = "SELECT t.id as sid, t.title,t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate FROM wp_media t $where order by id desc LIMIT $offset, $count";
		$sql = "SELECT m.*, "
			. "attr.value as video_total_count, "
			. "attr2.value as video_update_count "
			. "FROM ($sql) m "
			. "left join wp_media_attr attr on m.sid=attr.sid and attr.`key`='video_total_count' "
			. "left join wp_media_attr attr2 on m.sid=attr.sid and attr2.`key`='video_update_count'";
		$medias = $this->db->query($sql);
		return $medias ? $medias : null;
	}
	function queryDetail($sid){
		/*
		$sql = "SELECT m.id as sid, m.title, m.content,m.tip,m.tag,m.thumbnail,m.pic,m.score,m.views,m.actors,m.director,m.area,m.pubdate, v.sd, v.high, v.super, v.original, v.mp4 FROM wp_media m left join wp_media_video v on m.id = v.sid WHERE m.id='%s'";
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		return empty($medias)?null:$medias[0];
		*/
		$sql = "SELECT t.id as sid, t.title, t.content,t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate, v.sd, v.high, v.super, v.original, v.mp4 FROM wp_media t, wp_media_video v WHERE t.id=v.sid AND t.id=%s ";
		$sql = "SELECT m.*, "
			. "attr.value as video_total_count, "
			. "attr2.value as video_update_count "
			. "FROM ($sql) m "
			. "left join wp_media_attr attr on m.sid=attr.sid and attr.`key`='video_total_count' "
			. "left join wp_media_attr attr2 on m.sid=attr.sid and attr2.`key`='video_update_count'";
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		return empty($medias)?null:$medias[0];
	}
	function queryRecent($module,$sid,$count){
		$param = array("module"=>$module,"count"=>$count);
		if(!empty($sid))
			$param["exclude"] = $sid;
		$medias = $this->query($param);
		return $medias;
	}
}
<?php
class Epg extends bf\core\Model{
	function getChannels(){
		$sql = "select channelid,name,live_url,epg_api,cateid from wp_tvie_live ";
		$sql = "select c.*,e.name as epg_name,e.start_time,e.end_time from ($sql)c left join (select * from wp_tvie_epg where start_time<%s and end_time>%s)e on e.channel_id=c.channelid";
		$t = time();
		$t = "$t";
		$sql = sprintf($sql,$t,$t);
		$sql = "select t.*,cat.name as cate_name,cat.icon as cate_icon from ($sql) t left join wp_tvie_category cat on cat.id=t.cateid order by cat.order_no asc, cat.id asc";
		//echo $sql;
		//return $sql;
		$rows = $this->db->query($sql);
		$channels = array();
		if (!empty($rows)) {
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = $rows[$i];
				$liveEpg = new stdClass();
				$fields = array("name"=>"epg_name", "start_time"=>"start_time", "end_time"=>"end_time");
				$epg = new stdClass();
				foreach($fields as $k=>$k2){
					$epg->$k = $row->$k2;
					unset($row->$k2);
				}
				$row->live_epg = $epg;
				$fields = array("name"=>"cate_name", "icon"=>"cate_icon", "id"=>"cateid");
				$cate = new stdClass();
				foreach($fields as $k=>$k2){
					$cate->$k = $row->$k2;
					unset($row->$k2);
				}
				$group = new stdClass();
				$group = $cate;
				$group->data = array();
				if (count($channels)>0) {
					$g = array_pop($channels);
					if ($g->id == $cate->id) {
						$group = $g;
					}else{
						array_push($channels, $g);
					}
				}
				array_push($group->data, $row);
				array_push($channels, $group);
			}
		}
		return $channels;
	}
	function getEpgs($param){
		if (empty($param["channelid"])) {
			return null;
		}
		$channelid = $param["channelid"];
		$sql = "select * from wp_tvie_epg where channel_id=%s ";
		$where = null;
		if (!empty($param["start"]) && !empty($param["end"])) {
			$where = " AND start_time > %s AND start_time < %s";
			$where = sprintf($where, $param["start"],  $param["end"]);
			$sql .= $where;
		}
		$sql = sprintf($sql, $channelid);
		$rows = $this->db->query($sql);
		return $rows;
	}
	function searchChannel($s=false){
		$sql = "select * from wp_tvie_live ";
		if(!empty($s)){
			$sql .= sprintf(" where name like '%%%%%s%%%%'" ,addslashes($s));
		}
		$sql = "select c.*,e.name as epg_name,e.start_time,e.end_time from ($sql)c left join (select * from wp_tvie_epg where start_time<%s and end_time>%s)e on e.channel_id=c.channelid";
		$t = time();
		$t = "$t";
		$sql = sprintf($sql,$t,$t);
		//echo $sql;
		//return $sql;
		$channels = $this->db->query($sql);
		if (!empty($channels)) {
			foreach($channels as $i=>&$channel){
				$liveEpg = new stdClass();
				$liveEpg->name = $channel->epg_name;
				$liveEpg->start_time = $channel->start_time;
				$liveEpg->end_time = $channel->end_time;
				$channel->live_epg = $liveEpg;
				unset($channel->epg_name);
				unset($channel->start_time);
				unset($channel->end_time);
			}
		}
		return $channels;
	}
	function searchEpg($param){
		if (empty($param["s"])) {
			return null;
		}
		if (empty($param["start"])) {
			$param["start"] = time();
		}
		$sql = "select e.*, DATE_FORMAT(FROM_UNIXTIME(e.start_time),'%%Y-%%m-%%d') as d from wp_tvie_epg e where e.start_time > %s and e.name like '%%%s%%'  order by d asc,e.start_time asc";
		$sql = "select t.*,live.name as channel_name,live.icon,live.epg_api,live.live_url "
			. "from ($sql) t "
			. "left join wp_tvie_live live on live.channelid=t.channel_id";
		$sql = sprintf($sql,addslashes($param["start"]), addslashes($param["s"]));
		$rows = $this->db->query($sql);
		$epgs = array();
		for ($i=0, $n = count($rows); $i < $n; $i++) { 
			$row = $rows[$i];
			$day = $row->d;
			$channel = new stdClass();
			$fields = array("name"=>"channel_name", "icon"=>"icon", "epg_api"=>"epg_api", "live_url"=>"live_url");
			foreach($fields as $k=>$k2){
				$channel->$k = $row->$k2;
				unset($row->$k2);
			};
			unset($row->d);
			$row->channel = $channel;
			$group = new stdClass();
			if (count($epgs)>0) {
				$g = array_pop($epgs);
				if ($g->title == $day) {
					$group = $g;
				}else{
					array_push($epgs, $g);
				}
			}
			if (empty($group->data)) {
				$group->data = array();
			}
			$group->title = $day;
			array_push($group->data, $row);
			array_push($epgs, $group);

		}
		return $epgs;
	}
	function search($param){
		$channels = $this->searchChannel($param["s"]);
		$epgs = $this->searchEpg($param);
		$ret = array("channels"=>$channels, "epgs"=>$epgs);
		return $ret;
	}
	function queryHotChannels(){
		$sql = "select channelid,name,live_url,epg_api,cateid from wp_tvie_live where type='tv' limit 0,12";
		$sql = "select c.*,e.name as epg_name,e.start_time,e.end_time from ($sql)c left join (select * from wp_tvie_epg where start_time<%s and end_time>%s)e on e.channel_id=c.channelid";
		$t = time();
		$t = "$t";
		$sql = sprintf($sql,$t,$t);
		//echo $sql;
		//return $sql;
		$rows = $this->db->query($sql);
		$channels = array();
		if (!empty($rows)) {
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = $rows[$i];
				$liveEpg = new stdClass();
				$fields = array("name"=>"epg_name", "start_time"=>"start_time", "end_time"=>"end_time");
				$epg = new stdClass();
				foreach($fields as $k=>$k2){
					$epg->$k = $row->$k2;
					unset($row->$k2);
				}
				$row->live_epg = $epg;
			}
		}
		return $rows;
	}
}
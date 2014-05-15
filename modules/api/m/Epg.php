<?php
class Epg extends bk\core\Model{
	function getChannels(){
		$sql = "select order_no,channel_id,icon,type,name,live_url,epg_api,cate_id from wp_channel order by cate_id asc, channel_id asc";
		$sql = "select c.*,e.name as epg_name,e.start_time,e.end_time from ($sql)c left join (select * from wp_epg where start_time<%s and end_time>%s group by channel_id)e on e.channel_id=c.channel_id";
		$t = time();
		$t = "$t";
		$sql = sprintf($sql,$t,$t);
		$sql = "select t.*,cat.name as cate_name,cat.icon as cate_icon from ($sql) t left join wp_category cat on cat.id=t.cate_id order by cat.order_no asc, cat.id asc,t.order_no asc";
		//echo $sql;
		//return $sql;
		$rows = $this->db->query($sql);
		$channels = array();
		if (!empty($rows)) {
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = $rows[$i];
				$liveEpg = new stdClass();
				$fields = array("name"=>"epg_name", "start_time"=>"start_time", "end_time"=>"end_time");
				$epg = null;
				foreach($fields as $k=>$k2){
					if (empty($row->$k2)) {
						unset($row->$k2);
						continue;
					}
					if ($epg == null) {
						$epg = new stdClass();
					}
					$epg->$k = $row->$k2;
					unset($row->$k2);
				}
				$row->live_epg = $epg;
				$fields = array("name"=>"cate_name", "icon"=>"cate_icon", "id"=>"cate_id");
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
		if (empty($param["channel_id"])) {
			return null;
		}
		$channelid = $param["channel_id"];
		$sql = "select * from wp_epg where channel_id=%s ";
		$where = null;
		if (!empty($param["start"]) && !empty($param["end"])) {
			$where = " AND start_time > %s AND start_time < %s";
			$where = sprintf($where, $param["start"],  $param["end"]);
			$sql .= $where;
		}
		$sql .= " order by start_time asc";
		$sql = sprintf($sql, $channelid);
		$rows = $this->db->query($sql);
		return $rows;
	}
	function searchChannel($s=false){
		$sql = "select channel_id,name, icon,live_url,type,epg_api,cate_id from wp_channel ";
		if(!empty($s)){
			$sql .= sprintf(" where name like '%%%%%s%%%%'" ,addslashes($s));
		}
		$sql .= " order by order_no asc, channel_id asc ";
		$sql = "select c.*,e.name as epg_name,e.start_time,e.end_time from ($sql)c left join (select * from wp_epg where start_time<%s and end_time>%s)e on e.channel_id=c.channel_id";
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
		$sql = "select e.*, DATE_FORMAT(FROM_UNIXTIME(e.start_time),'%%Y-%%m-%%d') as d from wp_epg e where e.start_time > %s and e.name like '%%%s%%'  order by d asc,e.start_time asc";
		$sql = "select t.*,live.name as channel_name,live.channel_id,live.type as channel_type,live.icon,live.epg_api,live.live_url "
			. "from ($sql) t "
			. "left join wp_channel live on live.channel_id=t.channel_id";
		$sql = sprintf($sql,addslashes($param["start"]), addslashes($param["s"]));
		//echo $sql;
		$rows = $this->db->query($sql);
		$epgs = array();
		for ($i=0, $n = count($rows); $i < $n; $i++) { 
			$row = $rows[$i];
			$day = $row->d;
			$channel = new stdClass();
			$fields = array("name"=>"channel_name", "type"=>"channel_type", "icon"=>"icon", "epg_api"=>"epg_api", "live_url"=>"live_url", "channel_id"=>"channel_id");
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
	function queryNextEpg($ids){
		$where = "e.channel_id=".implode(" OR e.channel_id=",$ids);
		$t = time();
		$where = "(e.start_time > $t AND e.start_time<($t+3600*10)) AND ($where)";
		$sql = "select e.* from wp_epg e where $where order by e.start_time asc";
		$sql= "select * from ($sql) t group by channel_id";
		$sql = sprintf($sql,$t,$t);
		//echo $sql;
		//return $sql;
		$rows = $this->db->query($sql);
		return $rows;
	}
	function queryHotChannels(){
		$sql = "select icon,type, channel_id,name,live_url,epg_api,cate_id from wp_channel where type='tv' limit 0,12";
		$sql = "select c.*,e.name as epg_name,e.start_time,e.end_time from ($sql)c left join (select * from wp_epg where start_time<%s and end_time>%s)e on e.channel_id=c.channel_id";
		$t = time();
		$t = "$t";
		$sql = sprintf($sql,$t,$t);
		//echo $sql;
		//return $sql;
		$rows = $this->db->query($sql);
		$channels = array();
		$cids = array();
		if (!empty($rows)) {
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = $rows[$i];
				$liveEpg = new stdClass();
				$fields = array("name"=>"epg_name", "start_time"=>"start_time", "end_time"=>"end_time");
				$epg = null;
				foreach($fields as $k=>$k2){
					if (empty($row->$k2)) {
						unset($row->$k2);
						continue;
					}
					if ($epg == null) {
						$epg = new stdClass();
					}
					$epg->$k = $row->$k2;
					unset($row->$k2);
				}
				$row->live_epg = $epg;
				$cids[] = $row->channel_id;
			}
			$next_epgs = $this->queryNextEpg($cids);
			for($i = 0, $n = count($rows); $i < $n; $i++){
				$row = $rows[$i];
				$next_epg = null;
				for($j = 0, $n2 = count($next_epgs); $j < $n2; $j++){
					$e = $next_epgs[$j];
					if($e->channel_id == $row->channel_id){
						$next_epg = new stdClass();
						$next_epg->name = $e->name;
						$next_epg->start_time = $e->start_time;
						$next_epg->end_time = $e->end_time;
						break;
					}
				}
				$row->next_epg = $next_epg;
			}
		}

		return $rows;
	}


	function queryChannelSourceList($channel_id){
		$sql = "select icon,type, channel_id,name,live_url,epg_api from wp_channel where channel_id=$channel_id";
		$channel = $this->db->query($sql);
		if (!empty($channel)) {
			$channel = $channel[0];
		}
		$sources = array();
		$source = array("source"=>"Lavatech","channel_id"=>$channel->channel_id, "icon"=>$channel->icon, "name"=>$channel->name, "live_url"=>$channel->live_url);
		$sources[] = $source;
		$sql = "select icon, source,name, channel_id,live_url,reference_url from wp_channel_live_source where channel_id=$channel_id";
		$rows = $this->db->query($sql);
		global $site_url;
		foreach($rows as $i=>$row){
			if($row->source == "CNTV"){
				$row->live_url = $site_url."/api/v1/live/channel/source/url/?channel_id=".$row->channel_id."&source=".$row->source;
			}
			$sources[] = $row;
		}

		$channel->sources = $sources;
		return $channel;
	}
	function queryChannelSource($channel_id, $source){
		$sql = "select icon, source,name, channel_id,live_url,reference_url from wp_channel_live_source where channel_id=$channel_id AND source='$source'"; 
		$rows = $this->db->query($sql);
		$source = empty($rows) ? null:$rows[0];
		return $source;
	}
	function addLog($param){
		$ret = $this->db->insert("epg_log", $param);
		return $ret;
	}
	function queryHotPrograms(){
		
	}
}
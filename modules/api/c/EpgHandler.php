<?php
class EpgHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("Epg");
	}

	function searchParam(){
		$fields = array(
				"s"=>array("type"=>"string")
			);
		return $fields;
	}
	function updateChannelEpgApi(&$obj){
		global $index;
		$index = 0;
		function _f(&$o){
			global $site_url, $index;
			if ( gettype($o) == "array") {
				if (isset($o["epg_api"])) {
					$epg_api = $site_url."/api/v1/live/epgs/".$o["channel_id"]."/?timestamp={timestamp}";
					$o["epg_api"] = $epg_api;
					$o["display_id"] = ++$index;
					return;
				}
			}
			foreach ($o as $k => &$v) {
				if ( gettype($v) == "array") {
					_f($v);
				}
			}
		}
		_f($obj);
	}
	function search($param){
		$t = time();
		$param["start"] = dayStartTime($t-5*3600-24);
		$data = $this->model->search($param);
		$status = bk\core\Status::status();
		$data = objectToArray($data);
		$this->updateChannelEpgApi($data);
		$status->data = $data;
		return $status;
	}

	function queryChannelsParam(){
		$fields = array(
				"group"=>array("type"=>"string","option"=>true)
			);
		return $fields;
	}
	function queryChannels($param){
		$channels = $this->model->getChannels();
		$channelParam = array();
		$data = array("live_backward_days"=> 5,
						"live_playlist_days"=>7,
						"server_time"=>time()-300,
						"serverTime"=>time()-300,
						"live_time_delay"=> 100, 
						"channels"=>$channels);
		$status = bk\core\Status::status();
		$data = objectToArray($data);
		$this->updateChannelEpgApi($data);
		if( $param && isset( $param["group"] ) ){
			$a = $data["channels"];
			$channels = array();
			for($i = 0, $n = count($a); $i < $n; $i++){
				if(empty($channels)){
					$channels = $a[$i]["data"];
				}else{
					$channels = array_merge($channels,  $a[$i]["data"] );
				}
			}
			$data["channels"] = $channels;
		}
		$status->data = $data;
		return $status;
	}
	function queryEpgsParam(){
		$fields = array(
				"channel_id"=>array("type"=>"string"),
				"timestamp"=>array("type"=>"int")
			);
		return $fields;
	}
	function queryEpgs($param){
		$param["start"] = dayStartTime($param["timestamp"]);
		$param["end"] = dayEndTime($param["timestamp"]);
		unset($param["timestamp"]);
		$data = $this->model->getEpgs($param);
		$status = bk\core\Status::status();
		$status->data = array($data);
		return $status;
	}
	function queryHotChannels(){
		global $site_url;
		$channels = $this->model->queryHotChannels();
		$channels = objectToArray($channels);
		$this->updateChannelEpgApi($channels);
		$status = bk\core\Status::status();
		$status->data = $channels;
		return $status;
	}
	function queryChannelSourceParam(){
		$fields = array(
				"channel_id"=>array("type"=>"string")
			);
		return $fields;
	}
	function queryChannelSource($param){
		$channel = $this->model->queryChannelSourceList($param["channel_id"]);
		$status = bk\core\Status::status();
		$status->data = $channel;
		return $status;

	}
	function queryChannelSourceLiveUrlParam(){
		$fields = array(
				"channel_id"=>array("type"=>"string"),
				"source"=>array("type"=>"string"),
			);
		return $fields;
	}
	function queryChannelSourceLiveUrl($param){
		$source = $this->model->queryChannelSource($param["channel_id"], $param["source"]);
		if (empty($source->reference_url)) {
			return null;
		}
		$ret = CurlUtils::get($source->reference_url);
		$ret = json_decode($ret);
		$live_url = null;
		if (!empty($ret) && !empty($ret->iphone)) {
			$live_url = $ret->iphone;
		}
		if (!empty($live_url)) {
			header( "HTTP/1.1 301 Moved Permanently" );
	    	header("Location: ".$live_url);
		}
		return null;
	}

}
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
		function _f(&$o){
			global $site_url;
			if ( gettype($o) == "array") {
				if (isset($o["epg_api"])) {
					$epg_api = $site_url."/api/v1/live/epgs/".$o["channel_id"]."/?timestamp={timestamp}";
					$o["epg_api"] = $epg_api;
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
	function queryChannels(){
		$channels = $this->model->getChannels();
		$channelParam = array();
		$data = array("live_backward_days"=> 5,
						"live_playlist_days"=>7,
						"server_time"=>time(),
						"live_time_delay"=> 100, 
						"channels"=>$channels);
		$status = bk\core\Status::status();
		$data = objectToArray($data);
		$this->updateChannelEpgApi($data);
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
		$channel = $this->model->queryChannelSource($param["channel_id"]);
		$status = bk\core\Status::status();
		$status->data = $channel;
		return $status;

	}

}
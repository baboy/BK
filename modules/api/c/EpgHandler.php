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
	function search($param){
		$t = time();
		$param["start"] = dayStartTime($t-5*3600-24);
		$data = $this->model->search($param);
		$status = bk\core\Status::status();
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
		$status->data = $data;
		return $status;
	}
	function queryEpgsParam(){
		$fields = array(
				"channelid"=>array("type"=>"string"),
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
		$status->data = $data;
		return $status;
	}
	function queryHotChannels(){
		$data = $this->model->queryHotChannels();
		$status = bk\core\Status::status();
		$status->data = $data;
		return $status;
	}

}
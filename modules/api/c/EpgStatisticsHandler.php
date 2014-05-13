<?php
class EpgStatisticsHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("Epg");
	}

	function statisticsParam(){
		$fields = array(
				"name"=>array("type"=>"string"),
				"uid"=>array("type"=>"string","option"=>true),
				"channel_id"=>array("type"=>"string"),
				"start_time"=>array("type"=>"string","option"=>true),
				"end_time"=>array("type"=>"string","option"=>true),
				"s"=>array("type"=>"string","name"=>"播放开始时间戳"),
				"len"=>array("type"=>"string", "name"=>"观看时长")
			);
		return $fields;
	}
	function statistics($param){
		$name = $param["name"];
		$program = parseProgramName($name);
		$program_index = parseProgramIndex($name);
		$param["program"] = $program;
		$param["program_index"] = $program_index;
		$ret = $this->model->addLog($param);
		$status = bk\core\Status::status();
		$status->data = $ret;
		$status->error = $this->model->db->last_error;
		return $status;
	}
}
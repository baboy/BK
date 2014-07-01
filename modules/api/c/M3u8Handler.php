<?php
class M3u8Handler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("Epg");
	}

	function playListParam(){
		$fields = array(
				"channel_id"=>array("type"=>"string"),
				"source"=>array("type"=>"string"),
				"query_string"=>array("type"=>"string"),
				//"end_time"=>array("type"=>"string"),
				//"interval"=>array("type"=>"string")
			);
		return $fields;
	}
	function playList($param){
		$m3u8Param = $this->getM3u8Param($param["query_string"]);
		if($m3u8Param){
			$source = $this->model->queryChannelSource($param["channel_id"],$param["source"]);
			if(empty($source))
				return null;
			$live_url = $source->reference_url;

			$html = $this->createM3u8($live_url, $m3u8Param["start"], $m3u8Param["end"], $m3u8Param["interval"]);
			header("Content-Type: application/x-mpegURL");
			echo $html;
		}
		return null;
	}
	function getM3u8Param($path){
		$re = '/(?:([0-9]{10,})(?:,([0-9]{10,})(?:,([0-9]{4,}))?)?)$/';
		$n = preg_match_all($re, $path, $matches);
		$param = false;
		if($n > 0){
			$s = $matches[1][0];
			$e = $matches[2][0];
			$interval = $matches[3][0];
			$param = array("start"=>$s, "end"=>$e,"interval"=>$interval);
		}
		return $param;
	}
	function createM3u8($live_url, $s, $e, $interval){
		$re = "/\/channels\/([A-Za-z0-9_-]+)\/([a-zA-Z0-9_-]+)\/m3u8:([^\/]+)(?:\/([0-9]{10,})(?:,([0-9]{10,})(?:,([0-9]{4,}))?)?)?/";
		$n = preg_match_all($re, $live_url, $matches);
		$ts_base_url = null;
		if ($n) {
			$custom_name = $matches[1][0];
			$channel_name = $matches[2][0];
			$stream_name = $matches[3][0];
			$ts_base_url = preg_replace($re, "", $live_url);
			$ts_base_url .= "/channels/$custom_name/$channel_name/ts:$stream_name";
		}
		$mod = bcmod($s, $interval);
		if(intval($mod)!=0){
			$s = bcadd($s, "-$mod");
		}
		$html = "#EXTM3U\n"
				."#EXT-X-MEDIA-SEQUENCE:1\n"
				."#EXT-X-TARGETDURATION:5\n\n";
		while( bccomp($s, $e) < 0 ){
			$e2 = bcadd($s, $interval);
			$html .= "#EXTINF:".(intval($interval)/1000).",\n";
			$ts_url = $ts_base_url."/$s,$e2\n";
			$html .= $ts_url;
			$s = $e2;
		}
		$html .= "\n#EXT-X-ENDLIST\n";
		return $html;
	}
}
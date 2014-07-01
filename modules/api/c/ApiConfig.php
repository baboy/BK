<?php
class ApiConfigHandler extends bk\core\HttpRequestHandler{
	function configParam(){
		$fields = array(
				"module"=>array("type"=>"string", "option"=>true),
				"channel"=>array("type"=>"string", "option"=>true),
				"version"=>array("type"=>"string", "option"=>true),
				"product_id"=>array("type"=>"string", "option"=>true),
				"build"=>array("type"=>"int", "option"=>true),
				"package"=>array("type"=>"string", "option"=>true),
				"app"=>array("type"=>"string"),
			);
		return $fields;
	}
	function testApi($param, &$conf){
		global $site_url;
		$site_url = "http://114.215.109.123";
		if( !empty($param) && !empty($param["channel"]) && $param["channel"]=="test"){
			$conf["modules"] = array(
					array(
						"id"=>"serial",
						"position"=>"0,0,0.2286,1",
						"app"=>"com.tvie.ibox.live.mianyang",
						"param"=>array(
								"query_channels_api"=>"http://10.33.0.251/api/public/mcms/getLivelist",
								"channel_id"=>66
							),
						"title"=>"直播",
						"icon"=>"",
						"pic"=>"$site_url/static/images/serial.jpg",
						"api"=>"$site_url/api/v1/serial/query/",
						"filter"=>""
					),
					array(
						"id"=>"movie",
						"position"=>"0.2286,0,0.3857,0.5",
						"title"=>"电影",
						"icon"=>"",
						"pic"=>"$site_url/static/images/movie.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=22",
						"filter"=>""
					),
					array(
						"id"=>"zongyi",
						"position"=>"0.6143,0,0.3857,0.5",
						"title"=>"电视剧",
						"icon"=>"",
						"pic"=>"$site_url/static/images/zy.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=23",
						"filter"=>""
					),
					array(
						"id"=>"lanmu",
						"position"=>"0.2286,0.5,0.3,0.5",
						"title"=>"新闻",
						"icon"=>"",
						"pic"=>"$site_url/static/images/wp.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=4",
						"filter"=>""
					),
					array(
						"id"=>"star",
						"position"=>"0.5286,0.5,0.2357,0.5",
						"title"=>"综艺",
						"icon"=>"",
						"pic"=>"$site_url/static/images/star.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=21",
						"filter"=>""
					),
					array(
						"id"=>"hudong",
						"position"=>"0.7643,0.5,0.2357,0.5",
						"title"=>"动漫",
						"icon"=>"",
						"pic"=>"$site_url/static/images/chat.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=24",
						"filter"=>""
					));
			$conf["api"] = array(
						"related"=>"http://10.33.0.254/mcms/box/mod/vod/query.php",
						"detail"=>"http://10.33.0.254/mcms/box/mod/vod/",
						"views"=>"$site_url/api/v1/statistic/view/"
					);
		}
	}
	function config($param){
		$app = $param["app"];
		return $this->$app($param);
	}
	function iBox($param){
		$conf = include "v1.config.php";
		if(isset($param["product_id"]) && $param["product_id"] == "com.tvie.ITV"){
			$param["channel"] = "live";
		}
		//$param["channel"] = "v2";
		if( !empty($param) && !empty($param["channel"]) ){
			$channel = $param["channel"];
			if ($channel=="tvie") {
				$channel="v1";
			}
			if ($channel=="mianyang") {
				$channel="mianyang2";
			}
			$conf = include "$channel.config.php";
		}
		$this->testApi($param, $conf);
		$status = bk\core\Status::status();
		$status->data = $conf;
		$status->param = $param;
		return $status;
	}
	function itv(){
		$status = bk\core\Status::status();
		$status->data = array("splash"=>array("pic"=>"http://pic.dbw.cn/0/05/67/27/5672789_845897.jpg"));
		$status->data["config"] = array(
			"app_store" => "https://itunes.apple.com/cn/app/zhong-guo-shou-ji-dian-shi/id431046861?l=en&mt=8",
			"app_store_comment_msg" => "您的肯定是我们进步的动力，如果您觉得我们的App还可以，请赏个好评吧！",
		);
		return $status;
	}
	function lava(){
		return $this->itv();
	}
	function platform(){

		$status = bk\core\Status::status();
		$status->data = array("splash"=>array("pic"=>"http://pic.dbw.cn/0/05/67/27/5672789_845897.jpg"));
		$status->data["background"] = "http://app.tvie.com.cn/storage/79W43xQcPFsl4uFaiBF5VKnQMBiHQ";
		//$status->data["background"] = "http://app.tvie.com.cn/static/images/desktop/mianyang.png";
		$status->data["applications"] = array(
			array(
					"level"=>100,
					"name"=>"iBox-Vod",
					"package"=>"com.lava.ibox.vod",
					"version"=>"1",
					"download_url"=>"http://app.tvie.com.cn/app/build/download/73"
				)
		);
		return $status;
	}
}

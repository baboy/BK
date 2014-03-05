<?php
class ApiConfigHandler extends bk\core\HttpRequestHandler{
	function configParam(){
		$fields = array(
				"channel"=>array("type"=>"string", "option"=>true),
				"version"=>array("type"=>"string", "option"=>true),
				"build"=>array("type"=>"int", "option"=>true),
				"package"=>array("type"=>"string", "option"=>true)
			);
		return $fields;
	}
	function testApi($param, &$conf){
		if( !empty($param) && !empty($param["channel"]) && $param["channel"]=="test"){
			$conf["modules"] = array(
					array(
						"id"=>"serial",
						"position"=>"0,0,0.2286,1",
						"app"=>"com.tvie.ibox.live.mianyang",
						"param"=>array(
								"query_channels_api"=>"http://10.33.0.251/api/public/mcms/getLivelist"
							),
						"title"=>"直播",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/serial.jpg",
						"api"=>"http://app.tvie.com.cn/api/v1/serial/query/",
						"filter"=>""
					),
					array(
						"id"=>"movie",
						"position"=>"0.2286,0,0.3857,0.5",
						"title"=>"电影",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/movie.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=22",
						"filter"=>""
					),
					array(
						"id"=>"zongyi",
						"position"=>"0.6143,0,0.3857,0.5",
						"title"=>"电视剧",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/zy.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=23",
						"filter"=>""
					),
					array(
						"id"=>"lanmu",
						"position"=>"0.2286,0.5,0.3,0.5",
						"title"=>"新闻",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/wp.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=4",
						"filter"=>""
					),
					array(
						"id"=>"star",
						"position"=>"0.5286,0.5,0.2357,0.5",
						"title"=>"综艺",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/star.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=21",
						"filter"=>""
					),
					array(
						"id"=>"hudong",
						"position"=>"0.7643,0.5,0.2357,0.5",
						"title"=>"动漫",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/chat.png",
						"api"=>"http://10.33.0.254/mcms/xchannel/mod/vod/query.php?cid=24",
						"filter"=>""
					));
			$conf["api"] = array(
						"related"=>"http://10.33.0.254/mcms/box/mod/vod/query.php",
						"detail"=>"http://10.33.0.254/mcms/box/mod/vod/",
						"views"=>"http://app.tvie.com.cn/api/v1/statistic/view/"
					);
		}
	}
	function config($param){
		$conf = array(
				"background"=>"http://app.tvie.com.cn/static/images/background.png",
				"splash"=>"http://app.tvie.com.cn/static/images/background.png",
				"api"=>array(
						"related"=>"http://app.tvie.com.cn/api/v1/movie/recent/query/",
						"detail"=>"http://app.tvie.com.cn/api/v1/movie/detail/query/",
						"views"=>"http://app.tvie.com.cn/api/v1/statistic/view/"
					),
				"modules"=>array(
					array(
						"id"=>"serial",
						"position"=>"0,0,0.2286,1",
						"app"=>"com.tvie.ibox.live.mianyang",
						"param"=>array(
								"query_channels_api"=>"http://api.vdnplus.com/api/public/mcms/getLivelist"
							),
						"title"=>"直播",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/serial.jpg",
						"api"=>"http://app.tvie.com.cn/api/v1/serial/query/",
						"filter"=>""
					),
					array(
						"id"=>"movie",
						"position"=>"0.2286,0,0.3857,0.5",
						"title"=>"电影",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/movie.png",
						"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"filter"=>""
					),
					array(
						"id"=>"zongyi",
						"position"=>"0.6143,0,0.3857,0.5",
						"title"=>"电视剧",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/zy.png",
						"api"=>"http://app.tvie.com.cn/api/v1/serial/query/",
						"filter"=>""
					),
					array(
						"id"=>"lanmu",
						"position"=>"0.2286,0.5,0.3,0.5",
						"title"=>"王牌栏目",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/wp.png",
						"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"filter"=>""
					),
					array(
						"id"=>"star",
						"position"=>"0.5286,0.5,0.2357,0.5",
						"title"=>"明星",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/star.png",
						"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"filter"=>""
					),
					array(
						"id"=>"hudong",
						"position"=>"0.7643,0.5,0.2357,0.5",
						"title"=>"互动",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/chat.png",
						"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"filter"=>""
					)
				)
				);
		if( !empty($param) && !empty($param["channel"]) && $param["channel"]=="mianyang"){
			$conf["modules"] = array(
					//最左
					array(
						"id"=>"live",
						"position"=>"0,0,0.2286,0.5",
						"app"=>"com.tvie.ibox.live.mianyang",
						"param"=>array(
								"query_channels_api"=>"http://175.155.13.135/api/public/mcms/getLivelist"
							),
						"title"=>"直播",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/03/live.png",
						"api"=>"http://app.tvie.com.cn/api/v1/serial/query/",
						"filter"=>""
					),
					// 
					array(
						"id"=>"wenhuaxinxi",
						"position"=>"0,0.5,0.2286,0.5",
						"title"=>"文化信息",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/2009925122214308.jpg",
						//"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=21&num=30",
						"filter"=>""
					),
					array(
						"id"=>"wenhuayishu",
						"position"=>"0.2286,0,0.3857,0.5",
						"title"=>"文化艺术",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/whys.jpg",
						//"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=22&num=30",
						"filter"=>""
					),
					// line2
					array(
						"id"=>"wenwubaohu",
						"position"=>"0.6143,0,0.2,0.5",
						"title"=>"文物保护",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/2012119103418926.jpg",
						//"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=23&num=30",
						"filter"=>""
					),

					array(
						"id"=>"youshengduwu",
						"position"=>"0.8143, 0, 0.1857,0.5",
						"title"=>"有声读物",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/xxxx1.jpg",
						//"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=24&num=30",
						"filter"=>""
					),
					array(
						"id"=>"shizhengyaowen",
						"position"=>"0.2286,0.5,0.3,0.5",
						"title"=>"时政要闻",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/01421_9505551.jpg",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=1&num=30",
						"filter"=>""
					),
					array(
						"id"=>"qunzhonghuodong",
						"position"=>"0.5286,0.5,0.2357,0.5",
						"title"=>"群众活动",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/10032335_272928.jpg",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=4&num=30",
						"filter"=>""
					),
					array(
						"id"=>"hudong",
						"position"=>"0.7643,0.5,0.2357,0.5",
						"title"=>"涪江行",
						"icon"=>"",
						"pic"=>"http://175.155.13.135/mcms/wp-content/uploads/2014/02/fujiang.jpg",
						"api"=>"http://175.155.13.135/mcms/box/mod/vod/query.php?cid=17&num=30",
						"filter"=>""
					)
				);
			$conf["api"] = array(
						"related"=>"http://175.155.13.135/mcms/box/mod/vod/query.php",
						"detail"=>"http://175.155.13.135/mcms/box/mod/vod/",
						"views"=>"http://app.tvie.com.cn/api/v1/statistic/view/"
					);
		}
		$this->testApi($param, $conf);
		$status = bk\core\Status::status();
		$status->data = $conf;
		$status->param = $param;
		return $status;

	}
}

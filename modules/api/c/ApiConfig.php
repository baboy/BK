<?php
class ApiConfigHandler extends bf\core\HttpRequestHandler{
	function config(){
		$conf = array(

				"related_api"=>"http://app.tvie.com.cn/api/v1/movie/recent/query/",
				"background"=>"http://app.tvie.com.cn/static/images/background.png",
				"modules"=>array(
					array(
						"id"=>"serial",
						"position"=>"0,0,0.2286,1",
						"title"=>"电视剧",
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
						"title"=>"综艺",
						"icon"=>"",
						"pic"=>"http://app.tvie.com.cn/static/images/zy.png",
						"api"=>"http://app.tvie.com.cn/api/v1/movie/query/",
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
		$status = bf\core\Status::status();
		$status->data = $conf;
		return $status;

	}
}
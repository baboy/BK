<?php
class ApiConfigHandler extends bf\core\HttpRequestHandler{
	function config(){
		$conf = array(
			"status"=>1,
			"msg"=>"OK",
			"data"=>array(
				"modules"=>array(
					array(
						"position"=>"0,0,0.2286,1",
						"title"=>"电视剧",
						"icon"=>"",
						"pic"=>"",
						"api"=>"",
						"filter"=>""
					),
					array(
						"position"=>"0.2286,0,0.3857,0.5",
						"title"=>"电影",
						"icon"=>"",
						"pic"=>"",
						"api"=>"",
						"filter"=>""
					),
					array(
						"position"=>"0.6143,0,0.3857,0.5",
						"title"=>"综艺",
						"icon"=>"",
						"pic"=>"",
						"api"=>"",
						"filter"=>""
					),
					array(
						"position"=>"0.2286,0.5,0.3,0.5",
						"title"=>"王牌栏目",
						"icon"=>"",
						"pic"=>"",
						"api"=>"",
						"filter"=>""
					),
					array(
						"position"=>"0.5286,0.5,0.2357,0.5",
						"title"=>"明星",
						"icon"=>"",
						"pic"=>"",
						"api"=>"",
						"filter"=>""
					),
					array(
						"position"=>"0.7643,0.5,0.2357,0.5",
						"title"=>"互动",
						"icon"=>"",
						"pic"=>"",
						"api"=>"",
						"filter"=>""
					)
				)
			)
		);
		$status = bf\core\Status::status();
		$status->data = $conf;
		return $status;

	}
}
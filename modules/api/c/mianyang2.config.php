<?php
header("location:http://175.155.13.135/mcms/box/mod/vod/iBox.config.php");
exit();
$conf = array(
		"background"=>"http://app.tvie.com.cn/static/images/desktop/mianyang-background.jpg",
		"splash"=>"http://app.tvie.com.cn/static/images/desktop/mianyang.png",
		"api"=>array(
			"related"=>"http://175.155.13.135/mcms/box/mod/vod/query.php",
			"detail"=>"http://app.tvie.com.cn/api/v1/movie/detail/query/",
			"views"=>"http://175.155.13.135/mcms/box/mod/vod/stats.php"
			)
		);
function getCategories($cid = null){
	$url = "http://175.155.13.135/mcms/xchannel/mod/vod/category.php";
	if($cid)
		$url .= "?cid=".$cid;
	$ret = CurlUtils::get( $url );
	$status = json_decode($ret);
	return $status->data;
}
function createModules(){
	$data = getCategories();
	$cates = array();
	for($i = 0, $n = count($data); $i < $n; $i++){
		$item = $data[$i];
		$cate = array("title"=>$item->title,"tpl"=>"vod");
		$modules = array();
		if($item->hasSubCate){
			$a = getCategories($item->id);
			foreach($a as $k=>$m){
				$module = array();
				$keys = array("pic"=>"icon","title"=>"title");
				foreach ($keys as $k1 => $k2) {
					if(isset($m->$k2)){
						$module[$k1] = $m->$k2;
					}
				}
				$module["param"]["api"] = "http://175.155.13.135/mcms/box/mod/vod/query.php?&num=30&cid=".$m->id;
				$modules[] = $module;
			}
			
		}else{
			$module = array();
			$m = $item;
			$keys = array("pic"=>"icon","title"=>"title");
			foreach ($keys as $k1 => $k2) {
				if(isset($m->$k2)){
					$module[$k1] = $m->$k2;
				}
			}
			$module["param"]["api"] = "http://175.155.13.135/mcms/box/mod/vod/query.php?&num=30&cid=".$m->id;
			$modules[] = $module;
		}
		$count = count($modules);
			$numOfLine = $count>4?4:$count;
			$nLines = $count>4?2:1;
			$numOfLine = intval(($count+1)/$nLines);
			$h = 1.0/$nLines;
			$w = 1.0/$numOfLine;
			$oy = 0;
			if($nLines == 1){
				$h = 0.6;
				$oy = 0.2;
			}
			for($j=0; $j<$count; $j++){
				$col = $j%$numOfLine;
				$row = intval($j/$numOfLine);
				$x = $col*$w;
				$y = $row*$h+$oy;
				$modules[$j]["position"] = "$x,$y,$w,$h";
			}
		$cate["modules"] = $modules;
		$cates[] = $cate;



	}
	$cates[] = 
				array(
					"title"=>"电视",
					"tpl"=>"live",
					"app"=>"com.tvie.ibox.live.mianyang",
					"param"=>array(
							"api"=>"http://app.tvie.com.cn/api/v1/live/channels",
							"query_channels_api"=>"http://api.vdnplus.com/api/public/mcms/getLivelist",
							"channel_id"=>1
						)
				);
	return $cates;
}
function createModules2(){
	$data = getCategories();
	$cates = array();
	$homeModules = array();
	for($i = 0, $n = count($data); $i < $n; $i++){
		$item = $data[$i];
		$module = array();
		$module = array("tpl"=>"special");
		$module["param"]["api"] = "http://175.155.13.135/mcms/box/mod/vod/query.php?&num=30&cid=".$item->id;
		$keys = array("pic"=>"icon","title"=>"title");
		foreach ($keys as $k1 => $k2) {
			if(isset($item->$k2)){
				$module[$k1] = $item->$k2;
			}
		}
		if($item->hasSubCate){
			$a = getCategories($item->id);
			$module["tpl"] = "cate";
			$cates = array();
			foreach($a as $k=>$m){
				$cate = array();
				$keys = array("pic"=>"icon","title"=>"title");
				foreach ($keys as $k1 => $k2) {
					if(isset($m->$k2)){
						$cate[$k1] = $m->$k2;
					}
				}
				$cate["param"]["api"] = "http://175.155.13.135/mcms/box/mod/vod/query.php?&num=30&cid=".$m->id;
				$cates[] = $cate;
			}
			$module["background"]="http://app.tvie.com.cn/static/images/desktop/woods.jpg";
			$module["categories"] = $cates;
		}else{
			$module["background"]="http://app.tvie.com.cn/static/images/desktop/sky.jpg";
		}
		$homeModules[] = $module;
	}
	$count = count($homeModules);
	$numOfLine = $count>4?4:$count;
	$nLines = $count>4?2:1;
	$numOfLine = intval(($count+1)/$nLines);
	$h = 1.0/$nLines;
	$w = 1.0/$numOfLine;
	$oy = 0;
	if($nLines == 1){
		$h = 0.6;
		$oy = 0.2;
	}
	$numOfLine = $count;
	$h = 0.5;
	$w = 0.25;
	$oy = 0.25;
	for($j=0; $j<$count; $j++){
		$col = $j%$numOfLine;
		$row = intval($j/$numOfLine);
		if( $row == ($nLines-1) ){
			$w = 1.0/($count-$numOfLine*$row);
		}
		$x = $col*$w+$j*0.01;
		$y = $row*$h+$oy;
		$homeModules[$j]["position"] = "$x,$y,$w,$h";
	}
	$liveModule = 
				array(
					"title"=>"电视",
					"tpl"=>"live",
					"app"=>"com.tvie.ibox.live.mianyang",
					"param"=>array(
							"api"=>"http://175.155.13.135/mcms/xchannel/mod/live/index2.php",
							"query_channels_api"=>"http://175.155.13.135/api/public/mcms/getLivelist",
							"channel_id"=>1
						)
				);
	$modules = array(
			array("title"=>"首页",
					"tpl"=>"vod",
					"modules"=>$homeModules
				),
			$liveModule

		);
	return $modules;
}
$conf["modules"] = createModules2();

return $conf;
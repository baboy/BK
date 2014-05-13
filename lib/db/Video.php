<?php
define('MEDIA', '');

class Video extends Media{
	/**
	*	检索资源列表 只检索wp_media 表
	*/
	function queryList($param){
		return $this->query($param);
	}
	function querySerialVideos($gid){
		$gid = addslashes($gid);
		$sql = "SELECT t.id as sid, t.title,t.thumbnail,t.pic,t.pubdate,s.`index` FROM wp_media t, wp_media_serial_video s WHERE s.sid=t.id AND s.gid=$gid";
		$sql = "SELECT m.*, v.content, v.sd, v.high, v.super, v.original, v.mp4 FROM ($sql) m,  wp_media_video v WHERE  v.sid=m.sid";
		//echo $sql;

		$videos = $this->db->query($sql);
		return $videos;
	}
	/**
	*	检索资源详情
	*	
	*/
	function queryMovieDetail($sid){
		$sql = "SELECT t.module, t.id as sid, t.title,t.summary,t.author, t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate,t.total_count,t.update_count FROM wp_media t WHERE t.id=%s ";
		
		$sql = "SELECT m.*,v.content, v.sd, v.high, v.super, v.original, v.mp4 "
			. "FROM ($sql) m "
			. "left join wp_media_video v on m.sid=v.sid";
		
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		$media = empty($medias)?null:$medias[0];
		if (empty($media)) {
			return null;
		}
		if ( $media->module == "serial") {
			$videos = $this->querySerialVideos($sid);
			$media->videos = $videos;
		}else {
			$video = new stdClass();
			foreach(array("title","thumbnail","pic","sd","high","super","original","mp4") as $i=>$k){
				if (!empty($media->$k)) {
					$video->$k = $media->$k;
				}
			}
			if(!empty($video)){
				$media->videos = array($video);
			}
		}
		if(!empty($media->videos)){
			//$media->attachements = array("videos"=>$media->videos);
		}
		return $media;
	}
	function parseImages($html){
		$re_img = '/<img\s[^>]*\/?>/';
		$json = array();
		$n = preg_match_all($re_img, $html, $m1);
		$images = array();
		for($i=0; $i<$n; $i++){
			$tag = $m1[0][$i];
			$n2 = preg_match_all('/(\w+)\s*=\s*(?:(?:(?:["\'])([^"\']*)(?:["\']))|([^\/\s]*))/', $tag, $m2);

			$attrs = array();
			for($j=0; $j<$n2; $j++){
				$key = strtolower( $m2[1][$j] );
				$attrs[$key] = $m2[2][$j];
			}
			$item = array("tag"=>$tag);
			$keys = array("src","width","height","alt","title");
			foreach( $keys as $k=> $v){
				if( isset($attrs[$v]) ){
					$k2 = $v;
					if ($k2 == "alt" || $k2 == "title") {
						$k2 = "description";
					}
					$item[$k2] = $attrs[$v];
				}
			}
			$item["placeholder"] = "<!--{IMG-$i}-->";
			$images[] = $item;			
		}	
		return $images;
	}
	function queryNewsDetail($sid){
		$sid = addslashes($sid);
		/*
		$sql = "SELECT m.id as sid, m.title, m.content,m.tip,m.tag,m.thumbnail,m.pic,m.score,m.views,m.actors,m.director,m.area,m.pubdate, v.sd, v.high, v.super, v.original, v.mp4 FROM wp_media m left join wp_media_video v on m.id = v.sid WHERE m.id='%s'";
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		return empty($medias)?null:$medias[0];
		*/
		$sql = "SELECT t.module, t.id as sid, t.title,t.summary,t.author,t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate,t.total_count,t.update_count FROM wp_media t WHERE t.id=%s ";
		$sql = "SELECT m.*,c.content FROM ($sql) m LEFT JOIN wp_media_content c ON m.sid=c.sid";

		/*
		$sql = "SELECT m.*, v.sd, v.high, v.super, v.original, v.mp4 "
			. "FROM ($sql) m "
			. "left join wp_media_video v on m.sid=v.sid";
		*/
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		$media = empty($medias)?null:$medias[0];
		if (empty($media)) {
			return null;
		}
		$html = $media->content;
		//test video
		$html = "<!--{VIDEO-0}-->".$html;

		$images = $this->parseImages($html);
		foreach ($images as $key => &$item) {
			$html = str_replace($item["tag"], $item["placeholder"], $html);
			unset($item["tag"]);
		}
		$media->content = $html;
		if (!empty($images)) {
			$media->images = $images;
		}
		//test
		$video = array(
				"index"=>0,
				"thumbnail"=>"http://static.cnbetacdn.com/newsimg/2014//0218/30_1j0J1m4kn.jpg_w600.jpg",
				"src"=>"http://data.vod.itc.cn/?new=/189/162/KpaGAARfWIt9OPxIxaGJ05.mp4&plat=3&mkey=dH_OdlaXVpud-ZfE-gHil5zOUIFXs6wh",
				"placeholder"=>"<!--{VIDEO-0}-->"
			);
		$media->videos = array($video);

		
		/*
		if (!empty($media) && $media->total_count > 1) {
			$videos = $this->querySerialVideos($sid);
			$media->videos = $videos;
		}else if(!empty($media)){
			$video = new stdClass();
			foreach(array("title","thumbnail","pic","sd","high","super","original","mp4") as $i=>$k){
				if (!empty($media->$k)) {
					$video->$k = $media->$k;
				}
			}
			if(!empty($video)){
				$media->videos = array($video);
			}
		}
		*/
		return $media;
	}
	function queryRecent($module,$sid,$count){
		$param = array("module"=>$module,"count"=>$count);
		if(!empty($sid))
			$param["exclude"] = $sid;
		$medias = $this->query($param);
		return $medias;
	}
	function queryRecommend(){
		$modules = array(
				array("title"=>"电影","module"=>"movie", "param"=>array("module"=>"movie","node"=>"CONTENT","count"=>10)),
				array("title"=>"电视剧","module"=>"serial", "param"=>array("module"=>"serial","node"=>"SERIAL","count"=>10)),
				array("title"=>"新闻","module"=>"news", "param"=>array("module"=>"news","node"=>"CONTENT","count"=>10)),
			);
		foreach ($modules as $key => &$module) {
			$module["data"] = $this->query($module["param"]);
			unset($module["param"]);
		}
		return $modules;
	}
	function queryAttr($sid){
		$sql = "SELECT * FROM wp_media_attr t WHERE t.sid=%s ";
		$sql = sprintf($sql,addslashes($sid));
		$rows = $this->db->query($sql);
		$attrs = array();
		for ($i=0, $n = count($rows); $i < $n; $i++) { 
			$row = $rows[$i];
			$attr = array();
			$attr[($row->key)] = $row->value;
		}

		return $attrs;
	}
	function queryDetail($sid){
		// table: media,content
		$sql = "SELECT t.module, t.id as sid, t.title,t.summary,t.author,t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate,t.total_count,t.update_count FROM wp_media t WHERE t.id=%s ";
		$sql = "SELECT m.*,c.content FROM ($sql) m LEFT JOIN wp_media_content c ON m.sid=c.sid";

		$sql = "SELECT m2.*,v.content, v.sd, v.high, v.super, v.original, v.mp4 "
			. "FROM ($sql) m2 "
			. "left join wp_media_video v on m2.sid=v.sid";
		$sql = sprintf($sql,addslashes($sid));
		$medias = $this->db->query($sql);
		$media = empty($medias)?null:$medias[0];
		if (empty($media)) {
			return null;
		}
		$attrs = $this->queryAttr($sid);
		foreach ($attrs as $key => $value) {
			$media->$key = $value;
		}
		return $media;
	}
	function queryCategories($param){
		$where = $this->getWhereSql($param);
		$sql = "SELECT * FROM wp_media_category WHERE $where";
		$cates = $this->db->query($sql);
		return $cates;
	}
	function addCategory($param){

	}
}
global $video;
$video = new Video();
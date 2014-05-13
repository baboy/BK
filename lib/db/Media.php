<?php
define('MEDIA', '');

class Media extends bk\core\Model{
	function queryAttrs($sids){
		$sid = "(".implode(",", $sids).")";
		$sql = "SELECT * FROM wp_media_attr t WHERE t.sid in $sid ";
		$sql = sprintf($sql,addslashes($sid));
		$rows = $this->db->query($sql);
		$medias = array();
		if (!empty($rows)) {
			foreach ($rows as $key => $item) {
				$sid = $item->sid;

				$attrs = array();
				if(isset($medias[$sid])){
					$attrs = $medias[$sid];
				}
				$att = $item;
				$attKey = $att->key;
				$group = $item->group;
				if ($group && !isset($attrs[$group])) {
					$attrs[$group] = array();
				}
				$meta = $item->metadata;
				if (!empty($meta)) {
					$meta = json_decode($meta);
				}
				if (!empty($meta)) {
					foreach ($meta as $key => $value) {
						$att->$key = $value;
					}
				}
				if (isset($att->metadata)) {
					unset($att->metadata);
					unset($att->original);
				}
				if ( isset($attrs[$group]) ) {
					$attrs[$group][] = $att;
				}else{
					$attrs[$attKey] = $att;
				}
				$medias[$sid] = $attrs;
			}
		}
		return $medias;
	}
	/**
	*	检索资源列表 只检索wp_media 表
	*/
	function queryList($param){
		$offset = empty($param["offset"])?0:intval($param["offset"]);
		$count = empty($param["count"])?20:intval($param["count"]);
		unset($param["offset"]);
		unset($param["count"]);
		$where = null;
		foreach ($param as $key => $value) {
			if (empty($where)) {
				$where = " WHERE ";
			}else{
				$where .= " AND ";
			}
			if ($key == "exclude") {
				$sids = explode(",", $value);
				foreach ($sids as $i => $sid) {
					$where .= " t.id!=".addslashes($sid);
				}
				continue;
			}
			$where .= sprintf(" t.%s='%s' ",$key,addslashes($value));
		}
		$sql = "SELECT t.id as sid,t.author,t.summary, t.title,t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate,t.total_count,t.update_count FROM wp_media t $where order by id desc LIMIT $offset, $count";
		$medias = $this->db->query($sql);
		if($medias){
			$sids = array();
			foreach ($medias as $key => $media) {
				$sids[] = $media->sid;
			}
			$attachments = $this->queryAttrs($sids);

			foreach ($medias as $key => &$media) {
				if(isset($attachments[$media->sid])){
					$atts = $attachments[$media->sid];
					foreach ($atts as $k => $v) {
						$media->$k = $v;
					}

				}
			}
		}
		return $medias ? $medias : null;
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
		$media = $this->queryDetail($sid);
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
		if (!empty($rows)) {
			foreach ($rows as $key => $item) {
				$att = $item;
				$attKey = $att->key;
				$group = $item->group;
				if ($group && !isset($attrs[$group])) {
					$attrs[$group] = array();
				}
				$meta = $item->metadata;
				if (!empty($meta)) {
					$meta = json_decode($meta);
				}
				if (!empty($meta)) {
					foreach ($meta as $key => $value) {
						$att->$key = $value;
					}
				}
				if (isset($att->metadata)) {
					unset($att->metadata);
					unset($att->original);
				}
				if ( isset($attrs[$group]) ) {
					$attrs[$group][] = $att;
				}else{
					$attrs[$attKey] = $att;
				}
			}
		}
		return $attrs;
	}
	function queryDetail($sid){
		// table: media,content
		$sql = "SELECT t.module, t.id as sid, t.title,t.summary,t.author,t.tip,t.tag,t.thumbnail,t.pic,t.score,t.views,t.actors,t.director,t.area,t.pubdate,t.total_count,t.update_count FROM wp_media t WHERE t.id=%s ";
		$sql = "SELECT m.*,c.content FROM ($sql) m LEFT JOIN wp_media_content c ON m.sid=c.sid";
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
global $media;
$media = new Media();
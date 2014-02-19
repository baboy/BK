<?php
define('MEDIA', '');

class Media extends bk\core\Model{
	/**
	*	检索资源列表 只检索wp_media 表
	*/
	function query($param){
		$offset = empty($param["offset"])?0:intval($param["offset"]);
		$count = empty($param["count"])?20:intval($param["count"]);
		unset($param["offset"]);
		unset($param["count"]);
		/*
		$fields = array("id as sid","title","tip","tag","thumbnail","pic","duration","score","views","actors","director","area","pubdate");
		
		$medias = $this->db->select("media",$fields,$param,array($offset,$count));
		return $medias;
		*/
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
		$attachements = new stdClass();
		if ( $media->module == "serial") {
			$videos = $this->querySerialVideos($sid);
			$attachements->videos = $videos;
		}else {
			$video = new stdClass();
			foreach(array("title","thumbnail","pic","sd","high","super","original","mp4") as $i=>$k){
				if (!empty($media->$k)) {
					$video->$k = $media->$k;
				}
			}
			if(!empty($video)){
				$attachements->videos = array($video);
			}
		}
		$media->attachements = $attachements;
		return $media;
	}
	function queryAttachment($sid){
		$sql = "SELECT t.* FROM wp_media_attachment t WHERE t.sid='$sid' ";
		$rows = $this->db->query($sql);
		$attachments = array();
		if (!empty($rows)) {
			foreach ($rows as $key => $item) {
				$att = $item;
				$type = $item->type;
				if (!isset($attachments[$type])) {
					$attachments[$type] = array();
				}
				$meta = $item->metadata;
				if (!empty($meta)) {
					$meta = json_decode($meta);
				}
				if (empty($meta)) {
					$meta = array();
				}
				foreach ($meta as $key => $value) {
					$att->$key = $value;
				}
				if (isset($att->metadata)) {
					unset($att->metadata);
					unset($att->original);
				}
				$attachments[$type][] = $att;
			}
		}
		return $attachments;
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
		$images = $this->parseImages($html);
		foreach ($images as $key => &$item) {
			$html = str_replace($item["tag"], $item["placeholder"], $html);
			unset($item["tag"]);
		}
		$media->content = $html;
		$attachments = array();
		if (!empty($images)) {
			$attachements["images"] = $images;
		}
		if (!empty($attachements)) {
			$media->attachements = $attachements;
		}
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
global $media;
$media = new Media();
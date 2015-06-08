<?php
define('TABLE_UGC', 'ugc');
define("TABLE_UGC_FILE", "ugc_file");

class Ugc extends bk\core\Model{
	function queryAttrs($sids){
		$sid = "(".implode(",", $sids).")";
		$sql = "SELECT * FROM {".TABLE_UGC_FILE."} t WHERE t.sid in $sid ";
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
				$group = $item->group;
				if ($group && !isset($attrs[$group])) {
					$attrs[$group] = array();
				}
				$meta = $item->metadata;
				if (!empty($meta)) {
					$meta = json_decode($meta);
					$att->metadata = $meta;
				}
				if (isset($att->metadata)) {
					unset($att->metadata);
					unset($att->original);
				}
				if ( isset($attrs[$group]) ) {

					$keys = array("id","url","thumbnail");
					$a = array();
					foreach ( $keys as $k) {
						if(!empty($att->$k))
							$a[$k] = $att->$k;
					}
					if (!empty($meta)) {
						foreach ($meta as $key => $value) {
							$a[$key] = $value;
						}
					}
					$attrs[$group][] = $a;
				}
				$medias[$sid] = $attrs;
			}
		}
		return $medias;
	}
	function handleUser(&$medias){
		foreach($medias as &$media){
			$keys = array("uid", "nickname", "avatar_thumbnail");
			foreach ($keys as $key) {
				$user[$key] = $media->$key;
				unset($media->$key);
			}
			$media->user = $user["uid"] ? $user : null;
		}
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
		$sql = "SELECT t.id as sid,t.uid,t.content,t.addr,t.lat,t.lng, t.title,t.tags,t.pubdate FROM wp_ugc t $where order by id desc LIMIT $offset, $count";
		$sql = "SELECT ugc.*,u.uid,u.nickname,u.avatar_thumbnail FROM ($sql) ugc LEFT JOIN wp_passport_user u ON ugc.uid=u.uid";
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
		$this->handleUser($medias);
		return $medias ? $medias : null;
	}
	function addUgc($param){
		$ret = $this->db->insert(TABLE_UGC, $param);
		return $ret;
	}
	function addAttr($sid, $group, $url, $thumbnail,$metadata){
		if(!empty($metadata)){
			$metadata = json_encode($metadata);
		}

		$param = array("sid"=>$sid,"group"=>$group, "url"=>$url, "thumbnail"=>$thumbnail, "metadata"=>$metadata);
		$ret = $this->db->insert(TABLE_UGC_FILE, $param);
		return $ret;
	}
}
global $ugc;
$ugc = new Ugc();
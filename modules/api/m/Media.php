<?php
define('MEDIA', '');

class Media extends bf\core\Dao{
	function query($param){
		$offset = empty($param["offset"])?0:intval($param["offset"]);
		$len = empty($param["len"])?20:intval($param["len"]);
		$fields = array("id as sid","title", "type as type_name","content","tip","tag","thumbnail","pic","score","views","actors","director","area","pubdate");
		
		$medias = $this->select("media",$fields,$param,array($offset,$len));
		return $medias;
	}
}

<?php

class AppVersion extends bk\core\Model{
	function queryLastVersion($product_id, $channel,$os, $status){
		$where = "app.product_id='$product_id'";
		if (!empty($status))
			$where .= " AND build.status='$status'";
		if ($channel) {
			$where .= " AND build.channel='$channel'";
		}else{
			$where .= " AND build.channel is NULL";
		}
		if($os){
			$where .= " AND build.os='$os'";
		}
		$sql = "select app.*,build.build,build.version,build.description,build.download_url,build.link "
			. "from wp_app app,wp_app_build build "
			. "where $where and build.appid=app.id order by build.version desc, build.build desc limit 0,1";
		//echo $sql;
		$app = $this->db->query($sql);
		return $app?$app[0]:null;
	}
}
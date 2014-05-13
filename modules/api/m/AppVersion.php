<?php

class AppVersion extends bk\core\Model{
	function queryLastVersion($product_id, $channel,$platform, $status){
		$where = "app.product_id='$product_id'";
		if (!empty($status))
			$where .= " AND build.status='$status'";
		if ($channel) {
			$where .= " AND build.channel='$channel'";
		}else{
			$where .= " AND build.channel is NULL";
		}
		if($platform){
			$where .= " AND build.platform='$platform'";
		}
		$sql = "select app.*,build.build,build.version,build.download_url,build.link "
			. "from wp_app app,wp_app_build build "
			. "where $where and build.appid=app.id order by build.version desc, build.build desc limit 0,1";
		$app = $this->db->query($sql);
		return $app?$app[0]:null;
	}
}
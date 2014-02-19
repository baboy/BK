<?php

class AppVersion extends bk\core\Model{
	function queryLastVersion($package, $channel, $status){
		$where = "app.package='$package'";
		if (!empty($status))
			$where .= " AND build.status='$status'";
		if ($channel) {
			$where .= " AND build.channel='$channel'";
		}else{
			$where .= " AND build.channel is NULL";
		}
		$sql = "select app.*,build.build,build.version,build.download_url "
			. "from wp_app app,wp_app_build build "
			. "where $where and build.appid=app.id order by build.version desc, build.build desc limit 0,1";
		$app = $this->db->query($sql);
		return $app?$app[0]:null;
	}
}
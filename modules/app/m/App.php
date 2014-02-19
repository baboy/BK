<?php

define("TABLE_APP", "app");
define("TABLE_APP_BUILD", "app_build");
class App extends bf\core\Dao{
	function registerApp($param){
		$ret = $this->insert(TABLE_APP, $param);
		return $ret;
	}
	function queryAppList($param){
		$ret = $this->select(TABLE_APP,null,$param);
		return $ret;
	}
	function queryApp($param){
		$ret = $this->select(TABLE_APP,null,$param);
		return !empty($ret)?$ret[0]:null;
	}
	function queryAppBuilds($param){
		$where = null;
		foreach($param as $k=>$v){
			if(empty($where)){
				$where = "";
			}else{
				$where .=" AND ";
			}
			$tab = "app";
			if ($k == "build_id") {
				$tab = "build";
				$k = "id";
			}
			$where .= " $tab.$k=$v ";
		}
		$sql = "select app.name,app.package,build.id,build.channel,build.developer,build.version,build.description,build.download_url,build.build from wp_app app, wp_app_build build where app.id=build.appid and $where order by build.pubdate desc";
		$ret = $this->query($sql);
		if (!empty($ret)) {
			//$ret = objectToArray($ret);
		}
		return $ret;
	}

	function addAppBuild($param){
		$ret = $this->insert(TABLE_APP_BUILD, $param);
		return $ret;
	}
}


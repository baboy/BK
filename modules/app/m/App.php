<?php

define("TABLE_APP", "app");
define("TABLE_APP_BUILD", "app_build");
class App extends bf\core\Dao{
	function registerApp($param){
		$ret = $this->insert(TABLE_APP, $param);
		return $ret;
	}
	function getAppList($param){
		$ret = $this->select(TABLE_APP,null,$param);
		if (!empty($ret)) {
			$ret = objectToArray($ret);
		}
		return $ret;
	}

	function addAppBuild($param){
		$ret = $this->insert(TABLE_APP_BUILD, $param);
		if (!empty($ret)) {
			$ret = objectToArray($ret);
		}
		return $ret;
	}
}
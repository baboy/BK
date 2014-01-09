<?php

define("TABLE_APP", "app");
class App extends bf\core\Dao{
	function register($param){
		$ret = $this->insert(TABLE_APP, $param);
		return $ret;
	}
}
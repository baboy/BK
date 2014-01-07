<?php
namespace bf\core;

$dir = dirname(__file__);
require_once $dir."/ApplicationContext.php";
require_once $dir."/BFObjectManager.php";
require_once $dir."/BFRouteManager.php";
require_once $dir."/BFAcl.php";
require_once $dir."/Dao.php";
class ApplicationConfig{
	public function getContextConfig(){
		return null;
	}
	public function getRouteConfig(){
		return null;
	}
	public function getRoleConfig(){
		return null;
	}
	public function getSqlConfig(){
		return null;
	}
}
return new ApplicationConfig();


<?php
namespace bk\core;

$dir = dirname(__file__);
require_once $dir."/../lib/function.php";
require_once $dir."/ApplicationContext.php";
require_once $dir."/BKObjectManager.php";
require_once $dir."/BKRouteManager.php";
require_once $dir."/BKAcl.php";
require_once $dir."/Dao.php";
require_once $dir."/Model.php";
require_once $dir."/status.php";
require_once $dir."/validator.php";
require_once $dir."/HttpRequestHandler.php";
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
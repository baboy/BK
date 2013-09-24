<?php
namespace bf\core;
class Dispatcher{
	protected $config = array();
	protected $router = null;
	protected $acl = null;

	function __construct($config = null){
		if ( empty($config)) {
			return;
		}
		$this->config = $config;
		$this->router = Router::getInstance($config["routes"]);
		$this->acl = Acl::getInstance($config["roles"]);
	}
	static function getInstance($config){
		return new Dispatcher($config);
	}
	function handle(){
		//根据path 分析路由
		$route = $this->router->getCurrentRoute();
		//test
		$rid = "editor";
		$isAllow = $this->acl->isAllow($rid, $route);
		echo "Allow $rid access:".($isAllow?"Yes":"No");
	}
}
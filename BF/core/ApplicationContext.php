<?php
namespace bf\core;
class ApplicationContext{
	protected $config = array();
	protected $router = null;
	protected $acl = null;
	protected $objectManager = null;
	protected $db = null;

	function __construct($config = null){
		if ( empty($config)) {
			return;
		}
		$this->config = $config;
		$this->router = new BFRouteManager($config->getRouteConfig() );
		//$this->acl = $appConfig->getAcl();
		$this->objectManager = new BFObjectManager($config->getContextConfig());
		$this->objectManager->loadObjects($config->getRouteConfig());
		$this->db = new Dao($config->getSqlConfig());

	}
	static function getInstance($config){
		global $_applicationContext;
		if (!$_applicationContext) {
			$_applicationContext = new ApplicationContext($config);
		}
		return $_applicationContext;
	}
	function execute(){
		//获取当前路由
		$route = $this->router->getCurrentRoute();
		$action = $route->action;
		$class = $route->class;
		//获取路由代理
		$obj = $this->objectManager->getObjectProxy( $class );
		$obj->db = $this->db;
		//执行action
		$data = $obj->$action();
		if($route->result->type == "json"){
			if(!empty($data))
				echo json_encode($data);
		}else{
			if(!empty($data))
				extract($data);
			include WEB_ROOT_DIR."/tpl/".$route->result->view;

		}
	}
}
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
		if (empty($route)) {
			header("HTTP/1.0 404 Not Found");
			exit;
		}

		$action = $route->action;
		$class = $route->class;
		//获取路由代理
		$obj = $this->objectManager->getObjectProxy( $class );
		$obj->db = $this->db;
		$obj->init();
		//check param
		$paramAction = $action."Param";
		$fields = $obj->$paramAction();
		$param  = null;
		if (!empty($fields)) {
			$validator = new Validator($fields);
			$param = $validator->check();
			if(!$param->isSuccess()){
				echo json_encode($param);
				return;
			}
			$param = $param->data;
		}
		
		//执行action
		$status = $obj->$action($param);
		if($route->result->type == "json"){
			if(!empty($status))
				echo json_encode($status);
		}else{
			extract($GLOBALS);
			if(!empty($status)){
				extract(array("data"=>$status->data));
			}
			include WEB_ROOT_DIR."/tpl/".$route->result->view;

		}
	}
}
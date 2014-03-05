<?php
namespace bk\core;


$path_prefix = "/bk";
$relatvie_path = "";
$site_url = "http://".$_SERVER["HTTP_HOST"];
$server_port = $_SERVER["SERVER_PORT"];
if (intval($server_port)!=80) {
	$site_url .= ":$server_port";
}

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
		$this->router = new BKRouteManager($config->getRouteConfig() );
		//$this->acl = $appConfig->getAcl();
		$this->objectManager = new BKObjectManager($config->getContextConfig());
		$this->objectManager->loadObjects($config->getRouteConfig());
		global $__DB__;
		$__DB__ = new Dao($config->getSqlConfig());
		$this->db = $__DB__;
		$config->loadLibrary();
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
		//
		$status = $this->doAction($route);
		$this->doOutput($route, $status);
	}
	function doAction($route){
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
			if (!empty($param) && !empty($param->data)){
				$param = $param->data;
			}else{
				$param = null;
			}
		}
		if (empty($param) && empty($fields)) {
			$param = $_GET;
		}
		//执行action
		$status = $obj->$action($param);
		return $status;
	}
	function doOutput($route, $status){
		extract($GLOBALS);
		if(!empty($status) && !empty($status->data)){
			extract(array("data"=>$status->data));
		}
		$type = $route->result->type;
		if(empty($type) || $type == "html"){
			include WEB_ROOT_DIR."/tpl/".$route->result->view;
			return;
		}
		if( $type == "stream"){
			if (!empty($route->result->view)) {
				include WEB_ROOT_DIR."/tpl/".$route->result->view;
			}
			return;
		}
		if ($type=="html-json") {
			ob_start();
			include WEB_ROOT_DIR."/tpl/".$route->result->view;
			$status->data = ob_get_contents();
			ob_end_clean();
		}
		if(!empty($status))
			echo json_encode($status);
	}
}
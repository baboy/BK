<?php
namespace bf\core;

class BFRouteManager{
	public $routes = array();
	function __construct($config = null){

		if (empty($config)) {
			return;
		}
		for ($i=0, $n = count($config); $i < $n ; $i++) { 
			$routeConf = $config[$i];
			$path = $routeConf["path"];
			$this->routes[$path] = $routeConf;
		}
	}
	static function getInstance($config=null){
		global $_routeManager;
		if (!$_routeManager) {
			$_routeManager = new BFRouteManager( $config);
		}
		return $_routeManager;
	}
	function getRouteConfig(){
		return $this->routes;
	}
	function parsePath($path){
		$s = "/api/v1/query/{type}";
		$re = '/^(?:\/([^\{\}]*))(?:\/(?:\{([a-zA-Z0-9_-]+)\}))+/';
		$n = preg_match_all($re, $s, $matches);
		var_dump($matches);

	}
	/**
	*	return 获取当前路由
	*	
	*/
	function getCurrentRoute(){
		$path = $_SERVER["REQUEST_URI"];
		$pos = strpos($path, "?");
		if ($pos !== false){
			$path = substr($path, 0, $pos);
		}
		$prefix = $GLOBALS["path_prefix"];
		if(!empty($prefix) && startsWith($path, $prefix)){
			$path = substr($path, strlen($prefix));
		}
		//$this->parsePath($path);
		$routeConf = isset( $this->routes[$path] ) ? $this->routes[$path] : null;
		if (empty($routeConf)) {
			return false;
		}
		$route = new BFRoute( $routeConf);
		return $route;
	}
}
class BFRoute{
	public $path = null;

	public $class = null;
	public $action = null;
	public $file = null;
	function __construct( $data){
		if (empty($data)) {
			return;
		}
		$this->path = $data["path"];
		$this->file = $data["file"];
		$this->action = $data["action"];
		$this->class = $data["class"];
		$this->result = new \stdClass();

		if ( !empty($data["result"])) {
			foreach ($data["result"] as $k => $v) {
				$this->result->$k = $v;
			}
		}else{
			$this->result->type = "html";
			$this->result->tpl = $this->class."-".$this->action.".php";
		}
	}
}

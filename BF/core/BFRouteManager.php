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
			$this->routes[$path] = new BFRoute($routeConf);
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
		$route = isset( $this->routes[$path] ) ? $this->routes[$path] : null;
		if (!empty($route)) {
			return $route;
		}
		foreach ($this->routes as $key => $route) {
			$param = $route->match($path);
			if ($param) {
				foreach ($param as $key => $value) {
					$_GET[$key] = $value;
				}
				return $route;
			}
		}
		return false;
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
	function match($path){
		//$re = '/^((?:\/([a-zA-Z0-9-_]+))*)((?:\/(?:\{([a-zA-Z0-9_-]+)\}))*)/';
		$re = "/\{([a-zA-Z0-9-_]*)\}/";
		$n = preg_match_all($re, $this->path, $keys);
		if ($n>0) {
			$placeholders = $keys[0];
			$keys = $keys[1];
			$re = $this->path;
			for ($i=0,$n = count($placeholders); $i < $n; $i++) { 
				$re = str_replace($placeholders[$i], "([a-zA-Z0-9-_]+)", $re);
			}
		}else{
			return false;
		}
		$re = preg_replace("/\//","\\/",$re);
		$re = "^$re$";
		$n = preg_match("/$re/", $path, $values);
		if ($n>0) {
			$values = array_splice($values,1);
			$param = array();
			for ($i=0, $n = count($keys); $i < $n; $i++) { 
				$k = $keys[$i];
				$v = $values[$i];
				$param[$k] = $v;
			}
			return $param;
		}
		return false;
	}
}

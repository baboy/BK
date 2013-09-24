<?
namespace bf\core;

class Router{
	public $routes = array();
	function __construct($config = null){
		if (empty($config)) {
			return;
		}
		for ($i=0, $n = count($config); $i < $n ; $i++) { 
			$this->routes[] = new Route($config[$i]);
		}
	}
	static function getInstance($config){
		return new Router($config);
	}
	function getAllRoutes(){
		return $this->routes;
	}
	/**
	*	return 获取当前路由
	*	
	*/
	function getCurrentRoute(){
		$path = $_SERVER["REQUEST_URI"];
		foreach ($this->routes as $route) {
			if ($route->route == $path) {
				return $route;
			}
		}
		return false;
	}
}
class Route{
	public $route = null;
	public $controller = null;
	public $view = null;
	function __construct($data){
		if (empty($data)) {
			return;
		}
		$this->route = $data["route"];
		$this->controller = $data["controller"];
		$this->view = new \stdClass();

		if ( isset($data["view"])) {
			foreach ($data["view"] as $k => $v) {
				$this->view->$k = $v;
			}
		}else{
			$this->view->success = $this->controller;
			$this->view->error = "error";
		}
	}
}

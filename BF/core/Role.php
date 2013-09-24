<?
namespace bf\core;

class Role{
	public $rid = null;
	public $parent = null;
	public $routes = array();
	function __construct($data = null){
		if (empty($data)) {
			return;
		}
		$this->rid = $data["rid"];
		$this->parent = $data["parent"];
		$routes = $data["routes"];
		for ($i=0, $n = count($routes); $i < $n; $i++) { 
			$this->routes[] = new Route($routes[$i]);
		}
	}
}
<?php
namespace bk\core;

class BKObjectManager{
	public $objects = array();
	public $aop = array();

	public function __construct( $config = null){
		if (empty($config)) {
			return;
		}
		//装载 objects
		if ( isset($config["objects"])) {
			$objects = $config["objects"];
			$this->loadObjects($objects);
		}
		//装载aop
		if ( isset($config["aop"])) {
			$aop = $config["aop"];
			for ($i=0, $n = count($aop); $i < $n; $i++) { 
				$conf = $aop[$i];
				//set pointcut
				$pointcut = $conf["pointcut"];
				$aspectId = $pointcut["class"].".".$pointcut["action"];
				$this->aop[$aspectId] = $conf;
			}
		}
	}
	public function loadObjects($objects){
		for ($i=0, $n = count($objects); $i < $n; $i++) { 
			$conf = $objects[$i];
			$this->loadObject($conf);
		}
	}
	public function loadObject($objConf){

			$objId = $objConf["class"];
			$conf = isset( $this->objects[$objId] ) ? $this->objects[$objId] : $objConf;
			foreach ($objConf as $key => $value) {
				$conf[$key] = $value;
			}
			$this->objects[$objId] = $conf;
	}

	static function getInstance($config=null){
		global $_objectManager;
		if (!$_objectManager) {
			$_objectManager = new BKObjectManager($config);
		}
		return $_objectManager;
	}
	/**
	*
	*	@return 获取在context 中配置的对象, 自动创建$class 的动态代理
	*	@param $class, string 
	*
	*/
	public function getObjectProxy($class){
		if ( !isset($this->objects[$class])) {
			return null;
		}
		$objConf = $this->objects[$class];
		$obj = new BKObjectProxy( $objConf, $this->aop);

		return $obj;
	}
	
}



/**
* 动态代理类，当调用在context中配置的对象时，都将通过代理
* 当在aop 中配置了改对象的切点时，proxy将自动在各个切点调用代理
*/
class BKObjectProxy{
	public $file = null;
	public $class = null;
	public $action = null;
	public $args = null; 
	public $aop = null;
	public $obj = null;
	public function __construct( $data=null, $aop = null){
		if (!empty($data)) {
			$this->file 	= isset($data["file"]) ? $data["file"] : $data["class"];
			$this->class 	= $data["class"];
			$this->action 	= $data["action"];
			$this->args 	= empty($data["args"])?null:$data["args"];
		}
		$this->aop = $aop;
	}
	function getProxyObject(){
		if(!empty($this->obj))
			return $this->obj;
		$file = "modules/" . $this->file . ".php";
		require_once($file);
		$class = $this->class;
		$obj = new $class();
		$this->obj = $obj;
		return $obj;
	}
	function __set($name, $value){
		$obj = $this->getProxyObject();
		$obj->$name = $value;
	}
	//拦截 代理方法 执行aop
	function __call($name, $args){
		$obj = $this->getProxyObject();
		if (!method_exists($obj, $name)) {
			return false;
		}

		$proxy = new \ReflectionClass($obj);
		$method = $proxy->getMethod($name);
		$aspectId = $this->class . "." . $name;
		$aop = isset( $this->aop[$aspectId] ) ? $this->aop[$aspectId] : false;
		//aop:before
		// TODO:
		$this->callAspect("before", $aop);
		$ret = $method->invokeArgs($obj, $args);
		//aop:after
		//TODO:
		$this->callAspect("after", $aop);

		return $ret;

	}
	public function callAspect($aspectName, $aop = null){
		if ( empty( $aop ) || !isset( $aop[$aspectName] ) ) {
			return false;
		}
		$aspects = $aop[$aspectName];
		for ($i=0, $n = count($aspects); $i < $n; $i++) { 
			$aspect = $aspects[$i];
			$action = $aspect["action"];
			$obj = new BKObjectProxy($aspect, $this->aop);
			$obj->$action();
		}
	}

}
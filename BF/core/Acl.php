<?
namespace bf\core;

class Acl{
	private $roles = array();
	static function getInstance($config){
		if (empty($config)) {
			return;
		}
		$acl = new Acl();
		$roles = array();
		for ($i=0, $n = count($config); $i < $n; $i++) { 
			$role = new Role($config[$i]);
			$rid = $role->rid;
			$roles[$rid] = $role;
		}
		$acl->roles = $roles;
		return $acl;
	}
	public function getRole($rid){
		return $this->roles[$rid];
	}
	/**
	* 	result 添加一个角色
	*	@param $role 用户对应的角色对象
	*	@param $parent 继承于某个角色
	*
	*/
	public function addRole($role, $parent = null){
		$rid = $role->rid;
		if ( !empty($parent) ) {
			$role->parent = $parent;
		}
		$this->roles[$rid] = $role;
	}
	/**
	*	result 对应角色可操作的路由
	*	@param $rid 角色id
	*
	*/
	public function getRoleRoutes($rid){
		$role = $this->roles[$rid];
		if (empty($role)) {
			return false;
		}
		$routes = $role->routes;
		if ( !empty($role->parent) ) {
			$parentRoutes = $this->getRoleRoutes($role->parent);
			if (!empty($parentRoutes)) {
				$routes = array_merge( $routes, $parentRoutes);
			}
		}
		return $routes;
	}
	/**
	*	return BOOL类型，判断该角色是否有对相关路由进行相关操作的权限
	*	@param $rid 用户角色
	*	@param $action 动作 标示 增 删 改 查
	*	@param $route 路由
	*/
	function isAllow($rid, $route){
		$routes = $this->getRoleRoutes( $rid );
		foreach ($routes as $r) {
			if ($r->route == $route->route) {
				return true;
			}
		}
		return false;
	}
}
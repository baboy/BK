<?php
define ("TABLE_USER", "passport_user");
class User extends bk\core\Model{
	function getUserInfo($param){
		$cond = $param;
		if( isset($cond["password"]) ){
			//$cond["password"] = md5( strtolower($cond["password"]) );
		}
		$userinfo = $this->db->select(TABLE_USER,
						array("name",'uid','ukey','nickname','signature','avatar','avatar_thumbnail','desktop','friend_count','follower_count','gender','email','login_date','register_date','birthday','mobile','city'),
						$cond);
		if( empty($userinfo) )
			return false;
		$userinfo = $userinfo[0];
		return $userinfo;
	}
	function register($param){
		$uid = $this->db->insert(TABLE_USER,$param);
		return $uid;
	}
	function login($param){
		$user = $this->getUserInfo($param);
		if(!empty($user)){
			$ukey = UserKey::ukey($user->uid);
			$login_date = date("Y-m-d H:i:s");
			$this->db->update(TABLE_USER, array("ukey"=>$ukey,"login_date"=>$login_date),array('uid'=>$user->uid));
			$user->ukey = $ukey;
			$user->login_date = $login_date;
			return $user;
		}
		return false;
	}

	function update($param, $cond){
		$uid = $this->db->update(TABLE_USER,$param, $cond);
		return $uid;
	}
}
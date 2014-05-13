<?php
define("PASSWORD_MD5", false);
class UserHandler extends bk\core\HttpRequestHandler{
	function init(){
		$this->getModel("User");
	}

	function loginParam(){
		$fields = array(
				"username"=>array("type"=>"string"),
				"password"=>array("type"=>"string"),
			);
		return $fields;
	}
	function login($param){
		$username = $param["username"];
		$cond = array("password"=>$param["password"]);
		if (isEmail($username)) {
			$cond["email"] = $username;
		}else{
			$cond["username"] = $username;
		}
		$user = $this->model->login($cond);
		$status = bk\core\Status::status();
		if(!empty($user)){
			$status->data = $user;
		}else{
			$status = bk\core\Status::error();
			$status->error = $this->db->last_error;
		}
		return $status;
	}
	function loginWithOpenIdParam(){
		return $fields = array(
						"platform"=>array("type"=>"string"),				
						"platform_uid"=>array("type"=>"string"),				
						"nickname"=>array("type"=>"string","option"=>true),				
						"email"=>array("type"=>"string","option"=>true),		
						"signature"=>array("type"=>"string","option"=>true),		
						"avatar"=>array("type"=>"string","option"=>true),				
						"avatar_thumbnail"=>array("type"=>"string","option"=>true)
					);
	}
	function loginWithOpenId($param){
		$cond = array( "platform"=>$param['platform'], "platform_uid"=>$param['platform_uid'] );
		$user = $this->model->login($cond);
		if(empty($user)){
			$uid = $this->model->register($param);
			
			if(empty($uid)){
				$status = bk\core\Status::error();
				$status->error = $this->model->db->last_error;
				return $status;
			}
			$user = $this->model->login(array("uid"=>$uid));
		}
		$status = bk\core\Status::status();
		if(!empty($user)){
			$status->data = $user;
		}else{
			$status = bk\core\Status::error();
			$status->error = $this->db->last_error;
		}
		return $status;
	}

	function registerParam(){
		$fields = array(
						"username"=>array("type"=>"string","option"=>true),
						"email"=>array("type"=>"string","option"=>true),
						"name"=>array("type"=>"string","option"=>true),
						"nickname"=>array("type"=>"string","option"=>true),
						"password"=>array("type"=>"string"),
						"avatar"=>array("type"=>"string","option"=>true),
						"user_site"=>array("type"=>"string","option"=>true),
						"signature"=>array("type"=>"string","option"=>true),
						"gender"=>array("type"=>"int","option"=>true),
						"mobile"=>array("type"=>"string","option"=>true),
						"birthday"=>array("type"=>"string","option"=>true),
						"city"=>array("type"=>"string","option"=>true)
					);
		return $fields;
	}
	function register($param){
		if(!empty($param["username"])){
			$cond = array("username"=>$param["username"]);
		}else if(!empty($param["mobile"])){
			$cond = array("mobile"=>$param["mobile"]);			
		}else {
			$cond = array("email"=>$param["email"]);			
		}
		$exist = $this->model->getUserInfo($cond);
		if($exist){
			$status = bk\core\Status::errorNameDup();
			$msg = "该邮箱已注册";
			if(!empty($param["username"])){
				$msg = "用户名已存在";			
			}else if(!empty($param["mobile"])){
				$msg = "该手机号已注册";			
			}
			$status->msg = $msg;
			return $status;
		}else {
			if(PASSWORD_MD5){
				$param["password"] = md5($param["password"]);
			}
			$uid = $this->model->register($param);
			
			if(empty($uid)){
				$status = bk\core\Status::error();
				$status->error = $this->model->db->last_error;
				return $status;
			}
			$user = $this->model->login(array("uid"=>$uid, "password"=>$param["password"]));
			
			$status = bk\core\Status::status();
			if(!empty($user)){
				$status->data = $user;
			}else{
				$status = bk\core\Status::error();
				$status->error = $this->db->last_error;
			}
			return $status;
		}
	}
	function updateParam(){
		$fields = array(
						"name"=>array("type"=>"string","option"=>true),
						"nickname"=>array("type"=>"string","option"=>true),
						"avatar"=>array("type"=>"string","option"=>true),
						"user_site"=>array("type"=>"string","option"=>true),
						"signature"=>array("type"=>"string","option"=>true),
						"email"=>array("type"=>"string","option"=>true),
						"gender"=>array("type"=>"int","option"=>true),
						"mobile"=>array("type"=>"string","option"=>true),
						"birthday"=>array("type"=>"string","option"=>true),
						"city"=>array("type"=>"string","option"=>true)
					);
		return $fields;
	}
	function update($param){
		global $user;
		if(empty($user))
			return bk\core\Status::notLogin();
		if(empty($param))
			return bk\core\Status::errorParam();
		$cond = array("uid" => $user->uid);
		$this->model->update($param, $cond);
		return bk\core\Status::status();
	}
}
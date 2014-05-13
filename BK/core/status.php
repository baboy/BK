<?php
namespace bk\core;

class Status{
	public $status = self::SuccessCode;
    public $msg = self::SuccessMsg;
    const StatusKey = "status";
    const SuccessCode = 1;
    const NotLoginCode  = 2;
    const ErrorCode = 10;
    const ErrorParamCode = 20;
    const ErrorFuncNameCode = 50;
    const ErrorDatabaseCode = 60;
    const ErrorExistInDatabase = 70;
    const ErrorNameDupCode = 80;
    const ErrorLoginCode = 90;
    const MsgKey = "msg";
    const SuccessMsg = "OK";
    const ErrorMsg = "Error";
    const NotLoginMsg = "没有登录";
    const ErrorParamMsg = "参数错误";
    const ErrorExistInDatabaseMsg = "User name already in database";
    const ErrorUserNameDupMsg = '用户名已经存在！';
    const ErrorLoginPwdMsg = '用户名密码不匹配';

    public function __construct( $info = null ){
        if(!empty($info)){
            foreach ($info as $key => $value) {
                $this->$key = $value;
            }   
        }
    }
    public function isSuccess(){
        return $this->status == self::SuccessCode;
    }

    public static function status(){
        return new Status( array("status"=>self::SuccessCode,"msg"=>self::SuccessMsg) );
    }

    public static function error(){
        return new Status( array("status"=>Status::ErrorCode,"msg"=>Status::ErrorMsg) );
    }

    public static function errorNameDup(){
        return new Status( array("status"=>Status::ErrorNameDupCode,"msg"=>"用户名已经存在！") );
    }

    public static function errorParam(){
        return new Status( array("status"=>Status::ErrorParamCode,"msg"=>Status::ErrorParamMsg) );
    }

    public static function notLogin(){
        return new Status( array("status"=>Status::NotLoginCode,"msg"=>Status::NotLoginMsg) );
    }

    public static function errorLogin(){
        return new Status( array("status"=>Status::ErrorLoginCode,"msg"=>Status::ErrorLoginMsg) );
    }
}
<?php
class XServerHandler extends bk\core\HttpRequestHandler{
	function get(){
		global $sysVar;
		$ip = $sysVar->get("xiaozhu-server");
		echo "x-server: ".$ip;
		return null;
	}
	function post(){
		global $sysVar;
		$sysVar->set("xiaozhu-server", getClientIp());
		return null;
	}
}
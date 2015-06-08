<?php
class IPHandler extends bk\core\HttpRequestHandler{
	function ip($param){
		$status = bk\core\Status::status();
		$status->data = array("ip"=>getClientIp());
		return $status;
	}
}

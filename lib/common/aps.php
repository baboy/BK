<?php
function notify($deviceToken,$msg,$role=0,$id=null,$mod=null){
	if(!$deviceToken || !$msg){
		return Status::$errorParam;
	}

	$pass = 'iLook';
	
	
	$badge = 1;
	$sound = 'cowbell.wav';

	$param = array();
	$data = array('alert' => $msg);
	if ($badge)
		$data['badge'] = 2;//$badge;
	if ($sound){
		$data['sound'] = $sound;
	}
	$data['mod'] = $mod;
	$data['id'] = $id;
	$data['role'] = $role;
	$param["aps"] = $data;
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', '/usr/local/tvie/www/x-team/BK/modules/api/c/ck.pem');
	stream_context_set_option($ctx, 'ssl', 'passphrase', 'fuckyoubaby');
	stream_context_set_option($ctx, 'ssl', 'cafile', '/usr/local/tvie/www/x-team/BK/modules/api/c/entrust_2048_ca.cer');
	$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
	if (!$fp) {
		echo "error:".$err.",".$errstr;
		return false;
	}
	$body = json_encode($param);
	$data = chr(0) . pack("n",32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n",strlen($body)) . $body;
	fwrite($fp, $data);
	fclose($fp);
	$param["deviceToken"] = $deviceToken;
	return $param;
}
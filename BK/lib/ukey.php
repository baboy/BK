<?php
class UserKey{
	// |__2__|__2__|___8___|__4__|__2__|__10__|_10__|_3__|__3___|__4___|
	// prefix  版本号 random bucket type 时间戳 uid 扩展名 check1 check2
	static function getUKeyProtocol(){
		return array(
			array( "name"=>"prefix", 	"length"=>2 ,	"value"=>"11"),
			array( "name"=>"version", 	"length"=>2 ,	"value"=>"01"),
			array( "name"=>"salt", 		"length"=>8 ),
			array( "name"=>"bucket", 	"length"=>4 ,	"value"=>"0001"),
			array( "name"=>"timestamp",	"length"=>10 ),
			array( "name"=>"uid", 		"length"=>10 ),
			array( "name"=>"check1", 	"length"=>3 ,	"check"=>true),
			array( "name"=>"check2", 	"length"=>4,	"check"=>true)
			);
	}	
	static function ukey($uid){
		$protocol = self::getUKeyProtocol();
		$codes = array();
		$saltCode =  rand ( 0, 99999999 );
		//salt
		$codes["salt"] = $saltCode;
		//timestamp
		$timestamp = strrev(time()."");
		$codes["timestamp"] = $timestamp;
		while( strlen($uid) < 10  )
			$uid = "0".$uid;
		$encodeUid = "";
		for ($i=0, $n = strlen($uid); $i < $n; $i++) { 
			$t = intval(substr($timestamp, $i,1));
			$k = intval(substr($uid, $i, 1));
			$c = ($t+$k)%10;
			$encodeUid = $c.$encodeUid;
		}
		//uid
		$codes["uid"] = $encodeUid;
		$seq = "";
		for( $i = 0; $i < count($protocol); $i++ ){
			$item = $protocol[$i];
			$name = $item["name"];
			$len = $item["length"];
			if( isset($item["check"]) && $item["check"] )
				break;
			$code = isset( $item["value"] ) ? $item["value"] : $codes[$name];
			while( strlen($code) <$len  )
				$code = "0".$code;
			$seq .= $code;
		}

		$c1 = 0;
		for($i = 0; $i < strlen($seq); $i++){
			$c1 += intval(substr($seq,$i,1));
		}
		$ori = md5($seq);
		$c2 = decodeSeq( substr($ori, 0, 1).substr($ori,31,1) );
		while( strlen($c1) < 3  )
			$c1 = "0".$c1;
		while( strlen($c2) < 4  )
			$c2 = "0".$c2;
		$seq .= $c1.$c2;
		return encodeSeq($seq);
	}
	static function getUserComponents($ukey){
		$protocol = self::getUKeyProtocol();
		$origin = decodeSeq($ukey);
		$protocolLen = 0;
		foreach($protocol as $item){
			$protocolLen += $item["length"];
		}
		if(strlen($origin) != $protocolLen){
			return false;
		}
		$comp = array("ukey"=>$ukey);
		$comp["origin"] = $origin;
		$index = 0;
		$seq = "";
		for($i = 0; $i < count($protocol); $i++){
			$item = $protocol[$i];
			$k = $item["name"];
			$nbyte = $item["length"];
			$code = substr($origin, $index, $nbyte );
			$comp[$k] = $code;
			$index += $nbyte;
			if( !isset($item["check"]) || !$item["check"] ){
				$seq .= $code;
			}
		}
		$encodeUid = $comp["uid"];
		$timestamp = $comp["timestamp"];
		$uid = "";
		for ($i=0, $n = strlen($encodeUid); $i < $n; $i++) { 
			$t = intval(substr($timestamp, $i,1));
			$k = intval(substr($encodeUid, ($n-$i-1), 1))+10;
			$c = ($k-$t)%10;
			if (empty($uid) && $c==0) {
				continue;
			}
			$uid = $uid.$c;
		}
		$comp["timestamp"] = strrev($comp["timestamp"]);
		$comp["uid"] = $uid;
		$comp["seq"] = $seq;

		$c1 = 0;
		for($i = 0; $i < strlen($seq); $i++){
			$c1 += intval(substr($seq,$i,1));
		}
		$ori = md5($seq);
		$c2 = decodeSeq(substr($ori, 0, 1).substr($ori,31,1));
		while( strlen($c1) < 3  )
			$c1 = "0".$c1;
		while( strlen($c2) < 4  )
			$c2 = "0".$c2;
		if ($comp["check1"] != $c1 || $comp["check2"] != $c2) {
			return false;
		}
		return $comp;
	}
	public function test(){
		$ukey = UserKey::ukey(9999999999);
		$comp = UserKey::getUserComponents("6yIZP2A5YQe6oVegt4MsLcmB");
		echo $ukey;
		var_dump($comp);
	}
}
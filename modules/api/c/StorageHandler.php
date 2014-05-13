<?php

define("StoragePath", "/home/www-data/storage");
define("StorageTempPath", "/home/www-data/tmp");
define("StorageRedirectPath", "/file");
define("StorageNamePrefix", "11");
define("StorageVer", "01");
define("StorageBucket", "0001");
define("StorageTypeImage", "01");
define("StorageTypeVideo", "02");
define("StorageTypeBinary", "03");

class StorageHandler extends bk\core\HttpRequestHandler{
	// |__2__|__2__|___8___|__4__|__2__|__10__|_10__|_3__|__3___|__4___|
		// prefix  版本号 random bucket type 时间戳 uid 扩展名 check1 check2
		function getStorageProtocol(){
			return array(
				array( "name"=>"prefix", 	"length"=>2 ),
				array( "name"=>"version", 	"length"=>2 ),
				array( "name"=>"salt", 		"length"=>8 ),
				array( "name"=>"bucket", 	"length"=>4 ),
				array( "name"=>"type", 		"length"=>2 ),
				array( "name"=>"timestamp",	"length"=>14 ),
				array( "name"=>"key", 		"length"=>10 ),
				array( "name"=>"ext", 		"length"=>3 ),
				array( "name"=>"check1", 	"length"=>3 ,	"check"=>true),
				array( "name"=>"check2", 	"length"=>4,	"check"=>true)
				);
		}
		function getThumbnailConfig(){
			return array(
					"avatar"		=>	array( "width"=>100, "height"=>100 ),
					"thumbnail"		=>	array( "width"=>300, "height"=>200 ),
					"large"			=>	array( "width"=>500, "height"=>500 )
				);
		}
		function getMimetypes(){
			return array(
					"png"	=>	array( "code"=>"001", "mimetype"=>"image/png" ), 
					"jpg"	=>	array( "code"=>"002", "mimetype"=>"image/jpeg" ), 
					"jpeg"	=>	array( "code"=>"002", "mimetype"=>"image/jpeg" ), 
					"bmp"	=>	array( "code"=>"003", "mimetype"=>"image/bmp" ), 
					"gif"	=>	array( "code"=>"004", "mimetype"=>"image/gif" ),

					"mp4"	=>	array( "code"=>"100", "mimetype"=>"video/mp4" ),
					"mp4v"	=>	array( "code"=>"101", "mimetype"=>"video/mp4" ),
					"mov"	=>	array( "code"=>"102", "mimetype"=>"video/quicktime" ),
					"avi"	=>	array( "code"=>"110", "mimetype"=>"video/avi" ),
					"wmv"	=>	array( "code"=>"111", "mimetype"=>"video/wmv" ),
					"3gp"	=>	array( "code"=>"120", "mimetype"=>"video/3gpp" ),
					"3gp2"	=>	array( "code"=>"121", "mimetype"=>"video/3gpp2" ),
					"mpe"	=>	array( "code"=>"130", "mimetype"=>"video/mpeg" ),
					"mpg"	=>	array( "code"=>"131", "mimetype"=>"video/mpeg" ),
					"mpeg"	=>	array( "code"=>"132", "mimetype"=>"video/mpeg" ),
					"mpv2"	=>	array( "code"=>"133", "mimetype"=>"video/mpeg" ),
					"webm"	=>	array( "code"=>"140", "mimetype"=>"video/webm" ),
					"mkv"	=>	array( "code"=>"150", "mimetype"=>"video/mkv" ),
					"ogv"	=>	array( "code"=>"160", "mimetype"=>"video/ogg" ),
					"flv"	=>	array( "code"=>"161", "mimetype"=>"video/x-flv" ),
					"f4v"	=>	array( "code"=>"162", "mimetype"=>"video/x-flv" ),


					"mp3"	=>	array( "code"=>"200", "mimetype"=>"audio/mpeg" ),
					"wma"	=>	array( "code"=>"201", "mimetype"=>"audio/x-ms-wma" ),
					"m4a"	=>	array( "code"=>"202", "mimetype"=>"audio/mp4" ),
					"mp4a"	=>	array( "code"=>"203", "mimetype"=>"audio/mp4" ),
					"oga"	=>	array( "code"=>"210", "mimetype"=>"audio/ogg" ),

					"txt"	=>	array( "code"=>"800", "mimetype"=>"text/plain" ),
					"xml"	=>	array( "code"=>"810", "mimetype"=>"text/xml" ),
					"html"	=>	array( "code"=>"820", "mimetype"=>"text/html" ),
					"css"	=>	array( "code"=>"830", "mimetype"=>"text/css" ),
					"js"	=> 	array( "code"=>"840", "mimetype"=>"application/javascript" ),
					"zip"	=>	array( "code"=>"850", "mimetype"=>"application/zip" ),
					"ipa"	=>	array( "code"=>"851", "mimetype"=>"application/zip" ),
					"rar"	=>	array( "code"=>"852", "mimetype"=>"application/x-rar-compressed" ),
					"tar"	=>	array( "code"=>"853", "mimetype"=>"application/x-tar" ),
					"gz"	=>	array( "code"=>"854", "mimetype"=>"application/x-tar" ),
					"7z"	=>	array( "code"=>"855", "mimetype"=>"application/x-7z-compressed" )
				);

		}
		function getStorageExtCode($ext){
			if( empty($ext) )
				return "000";
			$ext = strtolower($ext);
			$mimetypes = $this->getMimetypes();
			$ret = isset( $mimetypes[$ext]  ) ? $mimetypes[$ext] : array("code"=>"000");
			return $ret["code"];
		}
		function getMimeType( $extCode ){
			$mimetypes = $this->getMimetypes();
			foreach( $mimetypes as $item  ){
				if($item["code"] == $extCode)
					return $item["mimetype"];
			}
			return "application/octet-stream";
		}
		// |__2__|__2__|___8___|__4__|__2__|__10__|_10__|_3__|__3___|__4___|
		// prefix  版本号 random bucket type 时间戳 uid 扩展名 check1 check2
		function getStorageName($key,$type=null, $ext=null, $size = null){
			if(!$type) $type="binary";
			if($type=="image") 
				$type = StorageTypeImage;
			else if($type=="video")
				$type = StorageTypeVideo;
			else 
				$type = StorageTypeBinary;

			$saltCode =  rand ( 0, 99999999 );

			$codes = array();
			$codes["ext"] = $this->getStorageExtCode($ext);
			$codes["salt"] = $saltCode;
			$codes["key"] = $key;
			$codes["prefix"] = StorageNamePrefix;
			$codes["version"] = StorageVer;
			$codes["bucket"] = StorageBucket;
			$codes["type"] = $type;
			$codes["timestamp"] = bcmul(sprintf("%.4f",microtime(true)),10000);
			$protocol = $this->getStorageProtocol();

			$seq = "";
			for( $i = 0; $i < count($protocol); $i++ ){
				$item = $protocol[$i];
				$k = $item["name"];
				$len = $item["length"];
				if( isset($item["check"]) && $item["check"] )
					break;
				$code = $codes[$k];
				while( strlen($code) <$len  )
					$code = "0".$code;
				$seq .= $code;
			}
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
			$seq .= $c1.$c2;
			return encodeSeq($seq);
		}

		function getStorageComponents($fid){
			$protocol = $this->getStorageProtocol();
			$origin = decodeSeq($fid);
			$protocolLen = 0;
			foreach($protocol as $item){
				$protocolLen += $item["length"];
			}
			if(strlen($origin) != $protocolLen){
				return false;
			}
			$comp = array("fid"=>$fid);
			$comp["origin"] = $origin;
			$b = 0;
			$seq = "";
			for($i = 0; $i < count($protocol); $i++){
				$item = $protocol[$i];
				$k = $item["name"];
				$nbyte = $item["length"];
				$code = substr($origin, $b, $nbyte );
				$comp[$k] = $code;
				$b += $nbyte;
				if( !isset($item["check"]) || !$item["check"] ){
					$seq .= $code;
				}
			}
			$comp["seq"] = $seq;
			$comp["mimetype"] = $this->getMimeType($comp["ext"]);

			$timestamp = $comp["timestamp"];
			$relative_path = date('/Y/m/d', substr( $timestamp, 0, 10 ) );
			$hash = md5($comp["key"]);
			$relative_path .= "/".substr($hash, 0,2)."/".substr($hash, 2,2)."/".substr(md5( $timestamp ), 0, 2);
			$comp["file_dir"] = StoragePath.$relative_path;
			$comp["file_path"] = $comp["file_dir"]."/".$fid;
			$comp["path"] = $relative_path."/".$fid;
			$comp["proxy_path"] = StorageRedirectPath."/".$comp["path"];
			$comp["proxy_path_dir"] = StorageRedirectPath."/".$relative_path;

			$check = $this->checkStorageComponents($comp);

			return $check ? $comp : false;
		}
		function checkStorageComponents($comp){
			if(!$comp)
				return false;
			foreach( $comp as $k => $v  ){
				if( empty($v)  )
					return false;
			}
			if( $comp["prefix"] !=  StorageNamePrefix)
				return false;
			$seq = $comp["seq"]; 
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
			if( $c1 != $comp["check1"] || $c2 != $comp["check2"]  )
				return false;
			return true;
		}
		function getStoragePath($fid){
			$comp = $this->getStorageComponents( $fid  );
			if(!$comp)
				return false;
			$file_dir = $comp["file_dir"];
			if(!file_exists($file_dir))
				mkdir($file_dir, 755, true);
			$file_path = $comp["file_path"];
			return $file_path;
		}
		function getStorageTempPath($fn){
			$ext = pathinfo($fn, PATHINFO_EXTENSION);
			$fn = md5($fn);
			if($ext)
				$fn .= ".$ext";

			return StorageTempPath."/$fn";
		}
		function getOffset($fp){
			$offset = 0;
			if( file_exists($fp) ){
				$offset = filesize($fp);
			}
			return $offset;
		}
		function write($fp,$len = null){
			$status = bk\core\Status::status();
			$status->data = array();
			$data = file_get_contents('php://input', 'a');
			if($data){
				if( $handle = fopen($fp,"a") ){
					$len = $len > strlen($data) ? false : $len;
					$wlen = 0;
					if($len){
						$wlen = fwrite($handle,$data, $len);
					}else{
						$wlen = fwrite($handle,$data);
					}
					$status->data["write_length"] = $wlen;
					$status->data["write"] = $wlen===false?"cannot write":"can write";
					$status->data["data_length"] = strlen($data);
					$status->data["fp"] = $fp;
					fclose($handle);
					
				}else{
					$json = Status::$error;
					$status->data["write_length"] = 0;
					$status->msg =  "open file<$fid> error";
				}	
			}
			else{
				$status->data["write_length"] = 0;
				$status->msg = "no data";		
			}
			return $status;
		}
		function save($tempPath, $scale=false){
			global $user, $site_url;
			$ext = pathinfo($tempPath, PATHINFO_EXTENSION);
			$type = is_video($tempPath) ? "video" : (is_image($tempPath) ? "image" : "binary");
			
			$fid= $this->getStorageName($user->uid, $type, $ext);
			$path = $this->getStoragePath($fid);
			$ok = rename($tempPath, $path);
			$status = bk\core\Status::status();
			if($ok){
				$data = array("ext"=>$ext);
				$data["url"] = $site_url."/storage/".$fid;	
				if( $scale  ){
					$thumbnailConf = $this->getThumbnailConfig();
					foreach($scale as $thumbnailStyle){
						if( isset( $thumbnailConf[$thumbnailStyle]  )  ){
							$param = $thumbnailConf[$thumbnailStyle] ;
							$data[$thumbnailStyle] = $data["url"].".".$thumbnailStyle;
						}
					}
				}
				$status->data = $data;
			}
			else{
				$status = bk\core\Status::error();
				$status->msg = "move file error";
			}
			return $status; 
		}
		function postParam(){
			return $fields = array(
						"fid"=>array("type"=>"string"),
						"desc"=>array("type"=>"string", "option"=>true),
						"scale"=>array("type"=>"string", "option"=>true),
						"tag"=>array("type"=>"string", "option"=>true),
						"content-length"=>array("type"=>"int", "option"=>true, "default"=>0)
					);
		}
		function post($param){
			global $user;
			$file_size = $param["content-length"];
			if( !$file_size ){
				$file_size = intval( $_SERVER["CONTENT_LENGTH"] );
			}
			$fid = $param["fid"];
			$scale = isset($param["scale"])?$param["scale"]:null;
			if( $scale  ){
				$scale = explode("|", $scale);
			}
			$status = bk\core\Status::status();
			if(!$user){
				$status =bk\core\Status::notLogin();
			}else{
				$data = array("fid"=>$fid);
				$offset = $this->getOffset($fid);
				$lastLen = null;
				$fp = $this->getStorageTempPath($fid);
				if($file_size){
					$data["file_size"] = $file_size;
					$lastLen = $file_size - $offset;
				}
				$status = $this->write($fp, $lastLen);
				$offset = $this->getOffset($fp);
				if( $file_size && $file_size <= $offset ){
					$ret = $this->save($fp, $scale);
					$status = $ret;
					/*
					foreach($ret as $k => $v){
						if( is_array($v) ){
							if (!isset($status->$k)) {
								$status->$k = array();
							}
							foreach( $v as $k2=>$v2 ){
								$status->$k[$k2] = $v2;
							}
						}else{
							$status->$k = $v;
						}
					}
					*/
				}
				if( !isset($status->data) ){
					$status->data = array();
				}
				$status->data["fid"] = $fid;
				$status->data["offset"] = $offset;
				$status->data["last_length"] = $lastLen;
			}
			return $status;
		}
		function queryParam(){
			return $fields = array(
						"fid"=>array("type"=>"string")
					);;
		}
		function query($param){
			if(!$param){
				header("HTTP/1.0 404 Not Found");
				exit;
			}
			$fn = $param["fid"];
			$fid = $param["fid"];
			$a = explode(".", $fid);
			$scale = "";
			if(count($a) > 0) 
				$fid = $a[0];
			if(count($a) > 1)
				$scale = $a[1];
  			  
			$ext = pathinfo($fid, PATHINFO_EXTENSION);

			$comp = $this->getStorageComponents($fid);
			$file_path = $comp["file_path"]; 
			$file_dir = $comp["file_dir"];
			if( file_exists($file_dir."/".$fn) ){
				$proxy_path = $comp["proxy_path_dir"]."/".$fn;
				$fn = requestValue("fn");
				if(!empty($fn)){
					header('Content-Disposition: attachment; filename="'.$fn.'"');
				}
				header("X-Accel-Redirect: $proxy_path");
				header("Content-Type: ".$comp["mimetype"]);
				return null;
			}
			if( !file_exists($file_path) ){
				if( file_exists( $fn ) && !is_dir($fn)  ){
					include_once  $fn;
					return null;
				}
				header("HTTP/1.0 404 Not Found");
				exit;
			}
			$proxy_path = $comp["proxy_path"];
			$thumbnailConf = $this->getThumbnailConfig();

			//custom size
			if( !isset($thumbnailConf[$scale] ) ){
				$n = preg_match("/^(\d+)(?:(?:x)(\d+)?)?.*$/", $scale, $matches);
				if( $n > 0){
					if( count($matches) == 2 )
						$matches[2] = $matches[1];
					$thumbnailConf[$scale] = array("width"=>$matches[1], "height"=>$matches[2]);
				}
			}

			$error = null;
			if( isset($thumbnailConf[$scale] )) {
				$thumbnail_path = $file_path.".".$scale;
				$thumbnail_conf = $thumbnailConf[$scale];
				$hasThumbnail = FALSE;
				if( !file_exists($thumbnail_path)){
					$hasThumbnail = createThumbnail($file_path, $thumbnail_path, $thumbnail_conf["width"], $thumbnail_conf["height"]);
					if(!$hasThumbnail)
						$error = "创建缩略图失败";
				}else{
					$hasThumbnail = TRUE;
				}
				$proxy_path .= $hasThumbnail ? ".$scale" : "";
			}
			header("X-Accel-Redirect: $proxy_path");
			header("Content-Type: ".$comp["mimetype"]);
			//header("Content-Type: ".$comp["mimetype"]);
			exit();
			return null;

		}
		function checkStorageName(){
			$fid = requestValue("fid");
			if($fid){
				$comp = $this->getStorageComponents($fid);
				var_dump($comp);
			}
			return null;
		}
		function createStorageName(){
			$user = self::parseUser("4Eg17LaBz60DSIEOA0");
			$fn = requestValue("fid");
			$ext = pathinfo($fn, PATHINFO_EXTENSION);
			$fid = $this->getStorageName( $user["uid"],  null, $ext);
			$comp = $this->getStorageComponents($fid);
			return $comp;
		}
		function handleRequest(){
			$status = bk\core\Status::status();
			if(isset($_GET["check"])){
				$status->data = $this->checkStorageName();
				return $status;
			}
			if( isset($_GET["create"]) ){
				$status->data = $this->createStorageName();
				return $status;
			}
			switch( $this->httpMethod  ){
			case "GET":
				$status = $this->query();
				return $status;
				break;
			case "POST":
				header('Content-type: application/json; charset=utf8');
				$status = $this->post();
				return $status;
				break;
			}

		}
		function testParam(){
			return $fields = array(
						"fid"=>array("type"=>"string")
					);
		}
		function test( $param ){
			global $user;
			var_dump($user);
			$fid = $param["fid"];
			if( empty($fid) ){
				$fid = $this->getStorageName(time(), "image", "png");
			}
			$comp = $this->getStorageComponents($fid);
			var_dump($comp);
		}
	}
<?php

namespace test;

class Test{
	
	public function test(){
		//echo "Test->test()";

		$s = "/api/v1/query/{type}/{name}";
		$re = '/^((?:\/([a-zA-Z0-9-_]+))*)((?:\/(?:\{([a-zA-Z0-9_-]+)\}))*)/';

		$re = "/\{([a-zA-Z0-9-_]*)\}/";
		$n = preg_match_all($re, $s, $keys);
		if ($n>0) {
			$placeholders = $keys[0];
			$keys = $keys[1];
			$re = $s;
			for ($i=0,$n = count($placeholders); $i < $n; $i++) { 
				$re = str_replace($placeholders[$i], "([a-zA-Z0-9-_]*)", $re);
			}
		}
		$re = preg_replace("/\//","\\/",$re);
		$path = "/api/v1/query/query/movie";
		$n = preg_match("/$re/", $path, $values);
		if ($n>0) {
			unset($values[0]);
		}
		var_dump($keys);
		var_dump($values);
		//echo "<br/>isset-x：".isset($a["x"])."<br/>".empty($a)."<br/>";
		//echo preg_match_all("/(.*\.mp4)(?:\?.*)?$/i", "http://xxx/1.mp41?sss",$matches);
		//var_dump("test");
		//$ret = $this->db->queryChannels();
		//var_dump($ret);
		//return array("title"=>"Test title", "test"=>"test body","data"=>$ret);
	}
	public function after(){
		//echo "aop:after";
	}
	public function before(){
		//echo "aop:before";
	}
}
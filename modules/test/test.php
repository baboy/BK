<?php

namespace test;

class Test{
	
	public function test(){
		//echo "Test->test()";
		$a = array("x"=>1);
		return null;
		//echo "<br/>isset-x：".isset($a["x"])."<br/>".empty($a)."<br/>";
		//echo preg_match_all("/(.*\.mp4)(?:\?.*)?$/i", "http://xxx/1.mp41?sss",$matches);
		//var_dump("test");
		$ret = $this->db->queryChannels();
		//var_dump($ret);
		return array("title"=>"Test title", "test"=>"test body","data"=>$ret);
	}
	public function after(){
		//echo "aop:after";
	}
	public function before(){
		//echo "aop:before";
	}
}
<?php
$s = "电视剧:皇粮胡同十九号(双语)(11)";
$s = "第一动画乐园(上午版)：十二生肖闯江湖";
function parseProgramName($s){
	$confs = array(
			array("directive" => "filter", "re" => '/(.+)(?:[-_\/ ])+.*$/'),
			array("directive" => "filter", "re" => '/([0-9]+)$/', "replace" => true),
			array("directive" => "filter", "re" => '/([^\:]*(?:剧场|专场|剧苑|电视剧|版)\)?(?:[\:]|之|：))/', "replace" => true),
			array("directive" => "filter", "re" => '/\(.+\)$/', "replace" => true),
		);
	$name = $s;
	for($i = 0, $n = count($confs); $i < $n; $i++){
		$conf = $confs[$i];
		$name = trim($name);
		if($conf["directive"] == "filter"){
			$re = $conf["re"];
			if( isset($conf["replace"]) && $conf["replace"]){
				$name = preg_replace($re, "", $name);
				continue;
			}
			if( preg_match($re, $name, $matches) > 0){
				$name = $matches[1];
				continue;
			}
		}
	}
	return trim($name);
}
function parseProgramIndex($s){
	$confs = array(
			array("directive" => "filter", "re" => '/(.+)(?:[-_\/\: ]|：)+.*$/'),
			array("directive" => "filter", "re" => '/([^\:]*(?:剧场|专场|剧苑)(?:[\:]|之|：))/', "replace" => true),
		);
	$index = 0;
	$re_num = '/\(?([0-9]+)\)?$/';

	$name = $s;
	for($i = 0, $n = count($confs); $i < $n; $i++){
		$conf = $confs[$i];
		$name = trim($name);

		if( preg_match($re_num, $name, $matches) > 0){
			$i = intval( $matches[1] );
			if($i > 0)
				$index = $i;
		}
		if($conf["directive"] == "filter"){
			$re = $conf["re"];
			if( isset($conf["replace"]) && $conf["replace"]){
				$name = preg_replace($re, "", $name);
			}
			else if( preg_match($re, $name, $matches) > 0){
				$name = $matches[1];
			}
		}
	}
	if( preg_match($re_num, $name, $matches) > 0){
		$i = intval( $matches[1] );
		if($i > 0)
			$index = $i;
	}
	return $index;
}
$name = parseProgramName($s);
echo $s."<br/>";
echo $name."<br/>";
echo parseProgramIndex($s);
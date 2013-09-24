<?php
	$config = include "config/core.config";
	require "../BF/core/Dispatcher.php";
	require "../BF/core/Role.php";
	require "../BF/core/Router.php";
	require "../BF/core/Acl.php";
	use bf\core;
	$dispatcher = bf\core\Dispatcher::getInstance($config);
	$dispatcher->handle();

	$a = array('a' => 1, "b" => array("a"=>1) );
	$a = array("c"=>array("d"=>1) );
	file_put_contents('test.txt', print_r($a, true));

<?php

define("WEB_ROOT_DIR", getcwd());
chdir("../");
$context = include "config/config.config";
//use bf\core\ApplicationContext as ApplicationContext;
//ApplicationContext::getInstance($context)->execute();


function test($k, $b){
	echo "<br/>";
	echo $k."|".$b;
	echo "<br/>";
}
test($k=1, $b=2);
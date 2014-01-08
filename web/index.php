<?php

define("WEB_ROOT_DIR", getcwd());
chdir("../");
date_default_timezone_set("Asia/Shanghai") ;
$context = include "config/config.config";
use bf\core\ApplicationContext as ApplicationContext;
ApplicationContext::getInstance($context)->execute();


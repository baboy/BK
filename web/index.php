<?php
$context = include "config/config.config";
use bk\core\ApplicationContext as ApplicationContext;
ApplicationContext::getInstance($context)->execute();


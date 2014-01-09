<?php

$context = include "config/config.config";
use bf\core\ApplicationContext as ApplicationContext;
ApplicationContext::getInstance($context)->execute();


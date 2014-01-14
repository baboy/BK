<?php
namespace bf\core;
class Model{
	function __construct($config = null){
		$this->config = $config;
		global $__DB__;
		$this->db = $__DB__;
	}
}
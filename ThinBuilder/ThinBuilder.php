<?php

namespace ThinBuilder;

class ThinBuilder{
	protected $connect;
	protected $db_config;

	public function __construct($db_config){
		$this -> db_config = $db_config;
		$this -> connect = $this -> create_connect($this -> db_config);
	}

	public function create_connect($db_conf){
		$dblib = "{$db_conf['dblib']}:host={$db_conf['host']};dbname={$db_conf['dbname']};charset={$db_conf['charset']}";
		$connect = new \PDO($dblib, $db_conf['user'], $db_conf['password']);
		return $connect;
	}
}
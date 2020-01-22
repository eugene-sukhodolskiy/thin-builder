<?php

namespace ThinBuilder;

class ThinBuilder{
	protected $pdo;
	protected $db_config;

	public function __construct($db_config){
		$this -> db_config = $db_config;
		$this -> pdo = $this -> create_connect($this -> db_config);
	}

	public function create_connect($db_conf){
		$dblib = "{$db_conf['dblib']}:host={$db_conf['host']};dbname={$db_conf['dbname']};charset={$db_conf['charset']}";
		$pdo = new \PDO($dblib, $db_conf['user'], $db_conf['password']);
		return $pdo;
	}

	// $where = [ [], 'AND', [], 'OR', [] ]
	public function select(String $tablename, $fields = [], $where = [], $order_fields = [], String $order_sort = 'DESC', $limit = []){
		list($fields, $where, $order_fields, $limit) = $this -> select_data_preprocessing($fields, $where, $order_fields, $limit);

		if($order_fields != ''){
			$order_fields .= " {$order_sort}";
		}

		$sql = "SELECT {$fields} FROM `{$tablename}` {$where} {$order_fields} {$limit}";
		echo $sql;

		return $this -> pdo -> query($sql) -> fetchAll(\PDO::FETCH_ASSOC);
	}

	private function escape_string_in_arr($arr){
		$result = [];
		foreach ($arr as $key => $value) {
			if(!is_array($value)){
				$result[addslashes($key)] = addslashes($value);
			}else{
				$result[addslashes($key)] = $this -> escape_string_in_arr($value);
			}
		}
		return $result;
	}

	private function select_data_preprocessing($fields, $where, $order_fields, $limit){
		// FIELDS PREPROCESSING
		if(count($fields)){
			$fields = $this -> escape_string_in_arr($fields);
			$fields = '`' . implode('`,`', $fields) . '`';
		}else{
			$fields = '*';
		}

		// ORDER PREPROCESSING
		if(count($order_fields)){
			$order_fields = $this -> escape_string_in_arr($order_fields);
			$order_fields = 'ORDER BY `' . implode('`,`', $order_fields) . '`';
		}else{
			$order_fields = '';
		}

		// WHERE PREPROCESSING
		$where = $this -> where_processing($where);

		// LIMIT PREPROCESSING
		if(count($limit)){
			$limit = $this -> escape_string_in_arr($limit);
			$limit = 'LIMIT ' . implode(',', $limit);
		}else{
			$limit = '';
		}

		return [$fields, $where, $order_fields, $limit];
	}

	public function insert(String $tablename, Array $data){
		$tablename = addslashes($tablename);
		$data = $this -> escape_string_in_arr($data);

		$fields = '`' . implode('`,`', array_keys($data)) . '`';
		$values = "'" . implode("','", array_values($data)) . "'";
		$sql = "INSERT INTO `{$tablename}` ({$fields}) VALUES ($values)";

		echo $sql;
		if($this -> pdo -> query($sql)){
			return $this -> pdo -> lastInsertId();
		}

		return false;
	}

	public function update(String $tablename, Array $data, $where = []){
		$where = $this -> where_processing($where);
		$data = $this -> escape_string_in_arr($data);
		$tablename = addslashes($tablename);

		$pdata = [];
		foreach ($data as $field => $value) {
			$pdata[] = "`{$field}`='{$value}'";
		}

		$sql = "UPDATE `{$tablename}` SET " . implode(',', $pdata) . " {$where}";
		echo $sql;
		return $this -> pdo -> query($sql);
	}

	public function delete(String $tablename, $where = []){
		$tablename = addslashes($tablename);
		$where = $this -> where_processing($where);

		$sql = "DELETE FROM `{$tablename}` {$where}";
		return $this -> pdo -> query($sql);
	}

	private function where_processing($where){
		$where = $this -> escape_string_in_arr($where);
		foreach ($where as $i => $w_item) {
			if(is_array($w_item)){
				if(count($w_item) === 2){
					$w_item = [$w_item[0], '=', $w_item[1]];
				}

				$w_item[0] = "`{$w_item[0]}`";
				if($w_item[1] != 'IN'){
					$w_item[2] = "'{$w_item[2]}'";
				}else{
					$w_item[2] = '(\'' . implode("','", $w_item[2]) . '\')';
				}

				$where[$i] = implode(' ', $w_item);
			}
		}

		$where = 'WHERE ' . implode(' ', $where);

		return $where;
	}
}
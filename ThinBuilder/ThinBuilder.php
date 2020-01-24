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
		return $this -> pdo -> query($sql);
	}

	public function delete(String $tablename, $where = []){
		$tablename = addslashes($tablename);
		$where = $this -> where_processing($where);

		$sql = "DELETE FROM `{$tablename}` {$where}";
		return $this -> pdo -> query($sql);
	}

	private function where_processing($where){
		if(!count($where)){
			return '';
		}

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

	public function drop(String $tablename){
		$tablename = addslashes($tablename);
		$sql = "DROP TABLE `{$tablename}`";
		return $this -> pdo -> query($sql);
	}

	public function truncate(String $tablename){
		$tablename = addslashes($tablename);
		$sql = "TRUNCATE TABLE `{$tablename}`";
		return $this -> pdo -> query($sql);
	}

	public function create_table(String $tablename, Array $fields, String $primary_key, $engine = 'InnoDB'){
		/* $fields = [
			'id' => [
				'type' => 'INT',
				'length' => 11,
				'default' => 'NOT NULL',
				'auto_increment' => true,
				'can_be_null' => true
			],
			'option_key' => [
				'type' => 'VARCHAR',
				'length' => 255,
				'default' => 'NOT NULL'
				'auto_increment' => false,
				'can_be_null' => false
			],
		] */
		 
		$tablename = addslashes($tablename);
		$fields = $this -> escape_string_in_arr($fields);
		$primary_key = addslashes($primary_key);
		$engine = addslashes($engine);

		$fields_str_arr = [];
		foreach ($fields as $name => $options) {
			$length = (isset($options['length']) and !is_null($options['length'])) ? "({$options['length']})" : '';

			if(isset($options['default'])){
				$default = ($options['default'] == 'NULL' or $options['default'] == 'CURRENT_TIMESTAMP') ? "DEFAULT {$options['default']}" : "DEFAULT '{$options['default']}'";
			}else{
				$default = '';
			}

			$auto_increment = (isset($options['auto_increment']) and $options['auto_increment']) ? 'AUTO_INCREMENT' : '';
			$can_be_null = (isset($options['can_be_null']) and $options['can_be_null']) ? 'NULL' : 'NOT NULL';
			
			$fields_str_arr[] = "`{$name}` {$options['type']}{$length} {$can_be_null} {$default} {$auto_increment}";
		}

		$fields_string = implode(', ', $fields_str_arr);
		$sql = "CREATE TABLE IF NOT EXISTS `{$tablename}` ({$fields_string}, PRIMARY KEY (`{$primary_key}`)) ENGINE = {$engine}";

		return $this -> pdo -> query($sql);
	}

	public function table_fields(String $tablename){
		$tablename = addslashes($tablename);

		$sql = "SHOW COLUMNS FROM `{$tablename}`";
		$result = $this -> pdo -> query($sql) -> fetchAll(\PDO::FETCH_NUM);
		$fields = [];
		foreach ($result as $raw_field) {
			list($type, $length) = explode('(', $raw_field[1]);
			$fields[$raw_field[0]] = ['type' => $type];
			$length = intval($length);
			if($length){
				$fields[$raw_field[0]]['length'] = $length;
			}
		}

		return $fields;
	}

	public function tables(){
		$sql = 'SHOW TABLES';
		$result = $this -> pdo -> query($sql) -> fetchAll(\PDO::FETCH_ASSOC);
		return array_map(function($val){
			$k = array_keys($val);
			return $val[$k[0]];
		}, $result);
	}

	public function count(String $tablename, $where = []){
		$tablename = addslashes($tablename);
		$where = $this -> where_processing($where);
		$sql = "SELECT COUNT(*) FROM `{$tablename}` {$where}";
		$result = $this -> pdo -> query($sql) -> fetch(\PDO::FETCH_ASSOC);
		return intval($result['COUNT(*)']);
	}
}
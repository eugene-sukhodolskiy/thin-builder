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

	// $where = [ [], 'AND', [], 'OR', [] ]
	public function select(String $tablename, $fields = [], $where = [], $order_fields = [], String $order_sort = 'DESC', $limit = []){
		// SELECT `field_1`, `field_2` FROM `tablename` WHERE `field_1` = 'value' AND `field_2` LIKE 'value2' ORDER BY `field_1` DESC LIMIT 0, 100
		// SELECT `field_1`, `field_2` FROM `tablename` WHERE `field_1` IN ('val1', 'val2', 'val3')
		
		list($fields, $where, $order_fields, $limit) = $this -> select_data_preprocessing($fields, $where, $order_fields, $limit);

		if($order_fields != ''){
			$order_fields .= " {$order_sort}";
		}

		$sql = "SELECT {$fields} FROM `{$tablename}` {$where} {$order_fields} {$limit}";
		echo $sql;

		return $this -> connect -> query($sql) -> fetchAll(\PDO::FETCH_ASSOC);
	}

	private function select_data_preprocessing($fields, $where, $order_fields, $limit){
		// FIELDS PREPROCESSING
		if(count($fields)){
			$fields = '`' . implode('`,`', $fields) . '`';
		}else{
			$fields = '*';
		}

		// ORDER PREPROCESSING
		if(count($order_fields)){
			$order_fields = 'ORDER BY `' . implode('`,`', $order_fields) . '`';
		}else{
			$order_fields = '';
		}

		// WHERE PREPROCESSING
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

		// LIMIT PREPROCESSING
		if(count($limit)){
			$limit = 'LIMIT ' . implode(',', $limit);
		}else{
			$limit = '';
		}

		return [$fields, $where, $order_fields, $limit];
	}


	public function insert(){
		
	}

	public function update(){
		
	}

	public function delete(){
		
	}
}
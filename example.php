<?php

/**
 * Example working with ThinBuilder
 */

use \ThinBuilder\ThinBuilder;

$tb = new ThinBuilder([
		"dblib" => "mysql",
		"host" => "127.0.0.1",
		"dbname" => "thin-builder",
		"charset" => "utf8",
		"user" => "root",
		"password" => ""
	]);


function insert($tb){
	$rows = [
		['name' => 'Victor', 'email' => 'victor@gmail.com', 'timestamp' => 'NOW()'],
		['name' => 'John', 'email' => 'john@gmail.com', 'timestamp' => 'NOW()'],
		['name' => 'Eugene', 'email' => 'e.sukhodolskiy@outlook.com', 'timestamp' => 'NOW()'],
	];

	return $tb -> insert('users', $rows);
}

function select($tb){
	$where = [
		['name', 'IN', ['John', 'Eugene']]
	];
	$fields = ['id', 'email', 'timestamp'];
	$orderby = ['name'];
	$sort = 'ASC';
	$limit = [0, 10];

	return $tb -> select('users', $fields, $where, $orderby, $sort, $limit);
}

function update($tb){
	$data = ['name' => 'Jan'];
	$where = [
		['name', '=', 'John'], 
		'OR', 
		['id', 2]
	];

	return $tb -> update('users', $data, $where);
}


function delete($tb){
	$where = [
		['email', 'victor@gmail.com']
	];

	return $tb -> delete('users', $where);
}

function tables($tb){
	return $tb -> tables();
}

function truncate($tb){
	return $tb -> truncate('users');
}

function drop($tb){
	return $tb -> drop('users');
}

function create_table($tb){
	$fields = [
		'id' => [
			'type' => 'INT',
			'length' => 11,
			'auto_increment' => true
		],
		'name' => [
			'type' => 'VARCHAR',
			'length' => 100,
			'default' => 'Unknown',
			'can_be_null' => false
		],
		'email' => [
			'type' => 'VARCHAR',
			'length' => 255,
			'can_be_null' => false,
		],
		'timestamp' => [
			'type' => 'TIMESTAMP',
			'default' => 'NOW()'
		]
	];

	$primary_key = 'id';

	return $tb -> create_table('new_table', $fields, $primary_key);
}

function table_fields($tb){
	return $tb -> table_fields('users');
}

function count_rows($tb){
	return $tb -> count('users');
}

function example($tb){
	// return create_table($tb);
	// return insert($tb);
	return select($tb);
	// return count_rows($tb);
	// return update($tb);
	// return delete($tb);
	// return tables($tb);
	// return truncate($tb);
	// return drop($tb);
	// return create_table($tb);
	// return table_fields($tb);
}


dd(example($tb));
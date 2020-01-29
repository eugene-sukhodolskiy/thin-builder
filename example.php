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
	], new FuryDriver());


function insert($tb){
	$rows = [
		['name' => 'Victor', 'email' => 'victor@gmail.com', 'timestamp' => 'NOW()'],
		['name' => 'John', 'email' => 'john@gmail.com', 'timestamp' => 'NOW()'],
		['name' => 'Eugene', 'email' => 'e.sukhodolskiy@outlook.com', 'timestamp' => 'NOW()'],
	];

	$result = [];

	foreach ($rows as $row) {
		$result[] = $tb -> insert('users', $row);
	}

	return $result;
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
	// create_table($tb);
	// insert($tb);
	select($tb);
	count_rows($tb);
	// update($tb);
	// delete($tb);
	// tables($tb);
	// truncate($tb);
	// drop($tb);
	// create_table($tb);
	// table_fields($tb);
}

example($tb);

dd( $tb -> history() -> get_all_history() );
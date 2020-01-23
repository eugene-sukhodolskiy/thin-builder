<?php

use \ThinBuilder\ThinBuilder;

$tb = new ThinBuilder([
		"dblib" => "mysql",
		"host" => "127.0.0.1",
		"dbname" => "thin-builder",
		"charset" => "utf8",
		"user" => "root",
		"password" => ""
	]);

// INSERT
// dd($tb -> insert('users', ['name' => 'Victor', 'email' => 'victor@gmail.com', 'timestamp' => 'NOW()']));
 // dd($tb -> insert('test', ['description' => 'desc']));

// SELECT
// $res = $tb -> select('users', [], [
// 	['name', 'IN', ['John', "Eugene"]]
// ], ['id'], 'ASC', [0, 10]);

// dd($res);


// UPDATE
// dd($tb -> update('users', ['name' => 'Jan'], [['name', '=', 'Jack'], 'OR', ['id', 1]]));

// DELETE
// dd($tb -> delete('users', [['email', 'victor@gmail.com']]));

// TABLES LIST 
// dd($tb -> tables());

// TRUNCATE TABLE
// dd($tb -> truncate('test'));

// DROP TABLE
//dd($tb -> drop('test'));

$tb -> create_table('new_table', [
			'id' => [
				'type' => 'INT',
				'length' => 11,
				'auto_increment' => true
			],
			'option_key' => [
				'type' => 'VARCHAR',
				'length' => 255,
				'default' => 'hello',
				'can_be_null' => true,
				'auto_increment' => false
			],
		], 'id');

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

// SELECT
// $res = $tb -> select('users', [], [
// 	['name', 'IN', ['John', "Eugene"]]
// ], ['id'], 'ASC', [0, 10]);

// dd($res);


// UPDATE
// dd($tb -> update('users', ['name' => 'Jan'], [['name', '=', 'Jack'], 'OR', ['id', 1]]));

// DELETE
// dd($tb -> delete('users', [['email', 'victor@gmail.com']]));
// 
dd($tb -> tables());
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

// dd($tb -> insert('users', ['name' => 'Victor', 'email' => 'victor@gmail.com', 'timestamp' => 'NOW()']));

$res = $tb -> select('users', [], [
	['name', 'IN', ['John', "Eugene"]]
], ['id'], 'ASC', [0, 10]);

dd($res);
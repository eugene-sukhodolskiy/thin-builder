<?php

namespace ThinBuilder;

interface ThinBuilderInterface{
	public function select(String $tablename, $fields = [], $where = [], $order_fields = [], String $order_sort = 'DESC', $limit = []);

	public function insert(String $tablename, Array $data);

	public function update(String $tablename, Array $data, $where = []);

	public function delete(String $tablename, $where = []);

	public function drop(String $tablename);

	public function truncate(String $tablename);

	public function create_table(String $tablename, Array $fields, String $primary_key, $engine = 'InnoDB');

	public function table_fields(String $tablename);

	public function tables();

	public function count(String $tablename, $where = []);
}
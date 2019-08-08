<?php
/**
 * Created by PhpStorm.
 * User: Кроликодин
 * Date: 16.06.2016
 * Time: 19:19
 */

class Model {

	var $conn;

	function __construct() {
		$connection = DatabaseConnection::singleton();
		$this->conn = $connection->conn;
	}

}


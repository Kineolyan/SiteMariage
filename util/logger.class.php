<?php

include_once "dba/dba.class.php";
include_once "vars.class.php";

class Logger {
	private $_db;

	public function __construct() {
		global $DB;

		$this->_db = $DB;
	}

	public function log($message) {
		$this->_db->insert('logs', 
			array('log_time' => 'NOW()', 'message' => Variables::sanitize($message)));
	}

	public function warn($message) {
		$this->log('[Warning] '.$message);
	}

	public function error($message) {
		$this->log('[Error] '.$message);
	}

}

?>
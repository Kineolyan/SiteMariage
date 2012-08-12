<?php

require_once 'dba.class.php';

class DbaPdo extends Dba {

	public function __construct(
			$host='localhost', $database = 'mariage',
			$user='root', $password='') {
		parent::__construct($host, $database, $user, $password);
	}

	public function connect() {
		global $pdo_options;
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;

		$this->_db = new PDO('mysql:host='.$this->_host.';dbname='.$this->_database,
				$this->_user, $this->_password, $pdo_options);
	}

	public function disconnect() {

	}

	public function querySQL($query, $count = false) {
		$response = $this->_db->query($query);

		if (NULL != $this->_response) {
			array_push($this->_cursors, $this->_response);
		}
		$this->_response = $response;

		if ($count) {
			return $this->_response->rowCount();
		}
	}

	public function fetch() {
		return $this->_response->fetch();
	}

	public function endQuery() {
		if (NULL!=$this->_response) {
			$this->_response->closeCursor();
		}
		$this->_response = array_pop($this->_cursors);
	}

	public function insertedId() {
		return $this->_db->lastInsertId();
	}
}

?>
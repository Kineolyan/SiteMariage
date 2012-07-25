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

		$this->m_db = new PDO('mysql:host='.$this->m_host.';dbname='.$this->m_database,
				$this->m_user, $this->m_password, $pdo_options);
	}

	public function disconnect() {

	}

	public function querySQL($query, $count = false) {
		$this->m_response = $this->m_db->query($query);

		if ($count) {
			return $this->m_response->rowCount();
		}
	}

	public function fetch() {
		return $this->m_response->fetch();
	}

	public function endQuery() {
		if (NULL!=$this->m_response) {
			$this->m_response->closeCursor();
			$this->m_response = NULL;
		}
	}

	public function insertedId() {
		return $this->m_db->lastInsertId();
	}
}

?>
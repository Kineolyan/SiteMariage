<?php

require_once 'dba.class.php';

class DbaMysql extends Dba {

	public function __construct($host = 'localhost', $database = 'mariage', $user = 'root', $password = '') {
		parent::__construct($host, $database, $user, $password);
	}

	public function connect() {
		$this->_db = mysql_connect($this->_host, $this->_user, $this->_password);
		if (!$this->_db) {
			throw new Exception('[Dba] Impossible de se connecter : ' . mysql_error());
		}

		if (!mysql_select_db($this->_database)) {
			throw new Exception('[Dba] Impossible de sélectionner la base : ' . mysql_error());
		}
	}

	public function disconnect() {
		mysql_close($this->_db);
	}

	public function querySQL($query, $count = false) {
		$response = mysql_query($query);

		if (!$response) {
			throw new Exception("[Dba] Echec de la requête ($query): " . mysql_error());
		}

		if (NULL != $this->_response) {
			array_push($this->_cursors, $this->_response);
		}
		$this->_response = $response;

		if ($count) {
			return mysql_num_rows($this->_response);
		}
	}

	public function fetch() {
		return mysql_fetch_array($this->_response);
	}

	public function endQuery() {
		if (NULL != $this->_response) {
			// mysql_free_result($this->_response);
		}
		$this->_response = array_pop($this->_cursors);
	}

	public function insertedId() {
		return mysql_insert_id($this->_db);
	}

	public function update($table, $fields, $conditions) {
		$updatedFields = '';
		$first = true;

		foreach ($fields as $field => $value) {
			if (!$first) {
				$updatedFields .= ',';
			}
			else {
				$first = false;
			}

			$updatedFields .= $field . '=' . $this->formatValue($value);
		}

		$this->querySQL("UPDATE $table SET $updatedFields WHERE $conditions", false);
		return mysql_affected_rows($this->_db);
	}
}

?>
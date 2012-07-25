<?php

require_once 'dba.class.php';

class DbaMysql extends Dba {

	public function __construct($host = 'localhost', $database = 'mariage', $user = 'root', $password = '') {
		parent::__construct($host, $database, $user, $password);
	}

	public function connect() {
		$this->m_db = mysql_connect($this->m_host, $this->m_user, $this->m_password);
		if (!$this->m_db) {
			throw new Exception('[Dba] Impossible de se connecter : ' . mysql_error());
		}

		if (!mysql_select_db($this->m_database)) {
			throw new Exception('[Dba] Impossible de sélectionner la base : ' . mysql_error());
		}
	}

	public function disconnect() {
		mysql_close($this->m_db);
	}

	public function querySQL($query, $count = false) {
		$this->m_response = mysql_query($query);

		if (!$this->m_response) {
			throw new Exception("[Dba] Echec de la requête ($query): " . mysql_error());
		}

		if ($count) {
			return mysql_num_rows($this->m_response);
		}
	}

	public function fetch() {
		return mysql_fetch_array($this->m_response);
	}

	public function endQuery() {
		if (NULL != $this->m_response) {
			// mysql_free_result($this->m_response);
			$this->m_response = NULL;
		}
	}

	public function insertedId() {
		return mysql_insert_id($this->m_db);
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
		return mysql_affected_rows($this->m_db);
	}
}

?>
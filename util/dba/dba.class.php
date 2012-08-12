<?php

abstract class Dba {
	protected $_host;
	protected $_database;
	protected $_user;
	protected $_password;

	protected $_db;
	protected $_response;
	protected $_cursors;

	public function __construct($host = 'localhost', $database = 'mariage', $user = 'root', $password = '') {
		$this->_host = $host;
		$this->_database = $database;
		$this->_user = $user;
		$this->_password = $password;

		$this->_db = NULL;
		$this->_response = NULL;
		$this->_cursors = array();

		$this->connect();
	}

	public function test($host = 'localhost', $database = 'mariage', $user = 'root', $password = '') {
		echo '-', $host, $database, $user, $password, '-';
	}

	public function __sleep() {
		$this->endQuery();

		return array('_host', '_database', '_user', '_password');
	}

	public function __wakeup() {
		$this->db = NULL;
		$this->_response = NULL;
		$this->_cursors = array();
		$this->connect();
	}

	abstract public function connect();

	abstract public function disconnect();

	abstract public function querySQL($query, $count = false);

	abstract public function fetch();

	abstract public function endQuery();

	abstract public function insertedId();

	/**
	 * Exécute une insertion en BDD des données fournies.
	 *
	 * @param {String} $table où insérée les données
	 * @param {Array} $elements à insérer dans un tableau colonne => valeur
	 */
	public function insert($table, $elements) {
		$tableFields = '';
		$tableValues = '';

		$first = true;
		foreach ($elements as $field => $value) {
			if (!$first) {
				$tableFields .= ',' . $field;
				$tableValues .= ",'$value'";
			}
			else {
				$first = false;
				$tableFields .= $field;
				$tableValues .= "'$value'";
			}
		}

		$this->querySQL("INSERT INTO $table($tableFields) VALUES($tableValues);");

		return $this->insertedId();
	}

	/**
	 * Exécute une requête Select
	 *
	 * @param string $table est la table de sélection
	 * @param string $fields sont les champs à sélectionner
	 * @param string $conditions sont les conditions sur la sélection
	 * @param array $additionalParameters est un tableau de strings contenant
	 * 		les conditions supplémentaires pour la requête (order by, group by, limit)
	 *
	 * @return int number est le nombre de résultats retournés par la requête
	 */
	public function select($table, $fields, $conditions = '', $additionalParameters = array()) {
		$query = 'SELECT ' . $fields . ' FROM ' . $table;

		if ('' != $conditions) {
			$query .= ' WHERE ' . $conditions;
		}

		if (isset($additionalParameters['groupBy'])) {
			$query .= ' GROUP BY ' . $additionalParameters['groupBy'];
		}

		if (isset($additionalParameters['orderBy'])) {
			$query .= ' ORDER BY ' . $additionalParameters['orderBy'];
		}

		if (isset($additionalParameters['limit'])) {
			$query .= ' LIMIT ' . $additionalParameters['limit'];
		}
		$query .= ';';

		return $this->querySQL($query, true);
	}

	public function get($table, $fields, $conditions = '', $additionalParameters = array()) {
		if (0	< $this->select($table, $fields, $conditions, $additionalParameters)) {
			$result = $this->fetch();
			$this->endQuery();

			return $result;
		}
		else {
			return NULL;
		}
	}

	public function getSingleDatum($table, $field, $conditions = '', $additionalParameters = array()) {
		if (0 < $this->select($table, $field, $conditions, $additionalParameters)) {
			$result = $this->fetch();
			$this->endQuery();

			return $result[0];
		}
		else {
			return NULL;
		}
	}

	public function count($table, $field, $conditions = '1=1') {
		$this->querySQL(
			"SELECT COUNT($field) AS total FROM $table WHERE $conditions;");
		$response = $this->fetch();
		$this->endQuery();
		return $response['total'];
	}

	public function update($table, $fields, $conditions) {
		$updatedFields = '';
		$first = true;

		foreach ($fields as $field => $value) {
			if ("id" == $field) {
				continue;
			}

			if (!$first) {
				$updatedFields .= ',';
			}
			else {
				$first = false;
			}

			var_dump($this->formatValue($value));
			$updatedFields .= $field . '=' . $this->formatValue($value);
		}

		return $this
				->querySQL("UPDATE $table SET $updatedFields WHERE $conditions",
						true);
	}

	protected function formatValue($value) {
		if (is_int($value)) {
			return $value;
		}
		else if ('NOW()' === $value) {
			return 'NOW()';
		}
		else {
			return "'$value'";
		}
	}
}

?>
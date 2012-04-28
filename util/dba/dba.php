<?php 

abstract class Dba {
	private $m_host;
	private $m_database;
	private $m_user;
	private $m_password;
	
	private $m_db;
	private $m_response;
	
	public function __construct(
			$host='localhost', $database = 'mariage',
			$user='root', $password='') {
		$this->m_host = $host;
		$this->m_database = $database;
		$this->m_user = $user;
		$this->m_password = $password;
		
		$this->m_db = NULL;
		$this->m_response = NULL;
		
		$this->connect();
	}
	
	public function __sleep() {
		$this->endQuery();
		
		return array('m_host', 'm_database', 'm_user', 'm_password', 'm_db');
	}
	
	public function __wakeup() {
		$this->connect();
	}
	
	abstract public function connect();
	
	abstract public function disconnect();
	
	abstract public function querySQL($query, $count = false);
	
	abstract public function fetch();
	
	abstract public function endQuery();
	
	public function insert($table, $elements) {
		$tableFields = '';
		$tableValues = '';
		
		$first = true;
		foreach($elements as $field => $value) {
			if (!$first) {
				$tableFields.= ','.$field;
				$tableValues.= ','.$value;
			}
			else {
				$first = false;
				$tableFields.= $field;
				$tableValues.= $value;
			}
		}
		
		$this->querySQL("INSERT INTO $table($tableFields) VALUES($tableValues);");
		$this->endQuery();
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
	public function select($table, $fields, $conditions='', $additionalParameters=array()) {
		$query = 'SELECT '.$fields.' FROM '.$table;
		
		if (''!=$conditions) {
			$query.= ' WHERE '.$conditions;
		}
		
		if (isset($additionalParameters['groupBy'])) {
			$query.= ' GROUP BY '.$additionalParameters['groupBy'];
		}
		
		if (isset($additionalParameters['orderBy'])) {
			$query.= ' ORDER BY '.$additionalParameters['orderBy'];
		}
		
		if (isset($additionalParameters['limit'])) {
			$query.= ' LIMIT '.$additionalParameters['limit'];
		}
		$query.= ';';
		
		return $this->querySQL($query, true);
	}
	
	public function count($table, $field, $conditions='1=1') {
		$this->querySQL("SELECT COUNT($field) AS total FROM $table WHERE $conditions;");
		$response = $this->fetch();
		$this->endQuery();
		return $response['total'];
	}
	
	public function update($table, $fields, $conditions) {
		$updatedFields = '';
		$first = true;
		
		foreach ($fields as $field => $value) {
			if (!$first) {
				$updatedFields.= ',';
			}
			else {
				$first = false;
			}
			
			$updatedFields.= "$field='$value'";
		}
		
		return $this->querySQL("UPDATE $table SET $updatedFields WHERE $conditions", true);
	}
}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
<?php 

class DbaMysql {
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
	
	public function connect() {
		$this->m_db = mysql_connect($this->m_host, $this->m_user, $this->m_password);
		if (!$this->m_db) {
			throw new Exception(
					'[Dba] Impossible de se connecter : ' . mysql_error());
		}   	
    	
		if (!mysql_select_db($this->m_database)) {
			throw new Exception(
					'[Dba] Impossible de sélectionner la base' . mysql_error());
		}
	}
	
	public function disconnect() {
		mysql_close($this->m_db);
	}
	
	public function querySQL($query, $count = false) {
		$this->m_response = mysql_query($query);
		
		if (!$this->m_response) {
			throw new Exception(
					"[Dba] Echec de la requête ($query): " . mysql_error());
		} 
		
		if ($count) {
			return mysql_num_rows($this->m_response);
		}
	}
	
	public function fetch() {
		return mysql_fetch_array($this->m_response);
	}
	
	public function endQuery() {
		if (NULL!=$this->m_response) {
			mysql_free_result($this->m_response);
			$this->m_response = NULL;
		}
	}
	
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
}

?>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
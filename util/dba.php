<?php 

class Dba {
	private $m_host;
	private $m_database;
	private $m_user;
	private $m_password;
	
	private $m_db;
	private $m_response;
	
	public function __construct() {
		$this->m_host = 'localhost';
		$this->m_database = 'mariage';
		$this->m_user = 'root';
		$this->m_password = '';
		
		$this->m_db = NULL;
		$this->m_response = NULL;
		
		$this->connect();
	}
	
	public function __sleep() {
		$this->endQuery();
		$this->disconnect();
		
		return array('m_host', 'm_database', 'm_user', 'm_password');
	}
	
	public function __wakeup() {
		$this->connect();
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
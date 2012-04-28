<?php 

include_once "dba.mysql.php";

class Visitor {
	private $m_db;
	private $m_allowedPages;
	
	private $m_nom;
	
	private $m_isLogged;
	private $m_isAdmin;
	
	public function __construct($db) {
		$this->m_db = $db;
		$this->getAllowedPages('anonymous');
		
		$this->m_nom = '';
				
		$this->m_isAdmin = false;
		$this->m_isLogged = false;
	}
	
	public function __sleep() {
		return array('m_nom', 'm_allowedPages', 'm_isLogged', 'm_isAdmin');
	}
	
	public function __wakeup() {
		$this->m_db = NULL;	
	}
	
	public function db($db) {	$this->m_db = $db; }
	
	public function nom() {	return $this->m_nom;	}
	
	public function isLogged() {	return $this->m_isLogged; }
	
	public function isAdmin() {	return $this->m_isAdmin;	}
	
	public function hasAccess($page) {
		return in_array($this->getPageId($page), $this->m_allowedPages);
	}
	
	public function manageConnection() {
		if (isset($_POST['connexion'])) {
			$login = $_POST['login'];
			$password = $_POST['password'];
			$this->login($login, $password);
		}
		else if (isset($_POST['deconnexion'])) {
			$this->logout();
		}
	}
	
	private function login($login, $password) {
		if (0<$this->m_db->count('users', '*', "login='$login' AND password='$password'")) {
			$this->getAllowedPages($login);
			$this->m_nom = $login;
			$this->m_isLogged = true;
		}
		
		return $this->m_isLogged;
	}
	
	private function logout() {
		$this->getAllowedPages('anonymous');
		$this->m_nom = '';
		$this->m_isLogged = false;
	}
	
	private function getAllowedPages($login) {
		$this->m_db->select('users', 'allowed_pages', "login='$login'");
		$response = $this->m_db->fetch();
		$this->m_allowedPages = explode(',', $response['allowed_pages']);
		$this->m_db->endQuery();
	}
	
	private function getPageId($pageTitle) {
		if (0<$this->m_db->select('pages', 'id', "title='$pageTitle'", true)) {
			$response = $this->m_db->fetch();
			$this->m_db->endQuery();
			return $response['id'];
		}
		else {
			return '-1';
		}
	}
	
}
?>
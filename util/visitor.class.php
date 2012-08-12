<?php

class Visitor {
	private $m_db;
	private $m_allowedPages;

	private $m_nom;
	private $m_id;

	private $m_isLogged;
	private $m_isAdmin;

	public static $DEFAULT_PASSWORD = 'Null';

	public function __construct($db) {
		$this->m_db = $db;
		$this->getAllowedPages('anonymous');

		$this->m_nom = '';
		$this->m_id = 0;

		$this->m_isAdmin = false;
		$this->m_isLogged = false;
	}

	public function __sleep() {
		return array('m_nom', 'm_allowedPages', 'm_isLogged', 'm_isAdmin', 'm_id');
	}

	public function __wakeup() {
		$this->m_db = NULL;
	}

	public function db($db) {	$this->m_db = $db; }

	public function nom() {	return $this->m_nom;	}

	public function id() {	return $this->m_id;	}

	public function isLogged() {	return $this->m_isLogged; }

	public function isAdmin() {	return $this->m_isAdmin;	}

	public function hasAccess($page) {
		if ($this->m_isAdmin) {
			return true;
		}
		else if (is_int($page)) {
			return in_array($page, array_keys($this->m_allowedPages));
		}
		else if (is_string($page)) {
			return in_array($page, array_values($this->m_allowedPages));
		}
		else {
			return false;
		}
	}

	/* -- Gestion de la connexion -- */

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
		$cryptedPasswd = self::cryptPassword($password);

		if (0<$this->m_db->count('users', '*', "login='$login' AND password='$cryptedPasswd'")) {
			$this->getAllowedPages($login);
			$this->m_nom = $login;
			$this->m_isLogged = true;

			$visitor = $this->m_db->get('users', 'admin, id',  "login='$login'");
			$this->m_isAdmin = '1'==$visitor['admin'];
			$this->m_id = intval($visitor['id']);

			$this->m_db->update('users', array('last_connection' => 'NOW()'),
					"login='$login'");
		}

		return $this->m_isLogged;
	}

	private function logout() {
		$this->getAllowedPages('anonymous');
		$this->m_nom = '';
		$this->m_id = 0;
		$this->m_isLogged = false;
		$this->m_isAdmin = false;
	}

	/* -- Méthodes accessoires -- */

	private function getAllowedPages($login) {
		$this->m_db->select('users', 'allowed_pages', "login='$login'");
		$response = $this->m_db->fetch();
		$this->m_db->endQuery();
		$this->m_allowedPages = array();
		foreach (explode(',', $response['allowed_pages']) as $pageId) {
			$this->m_allowedPages[$pageId] =
				$this->m_db->getSingleDatum('pages', 'title', "id='$pageId'");
		}

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

	public static function cryptPassword($value) {
		return crypt($value, 'SaltedByColombeAndOlivier');
	}
}
?>
<?php

class Variables {
	private static $FLASH = 'flash_';
	private static $ERREUR = '__erreur__';
	private static $INFO = '__info__';
	private static $WARNING = '__warning__';
	private static $SUCCESS = '__success__';

	private static $AJAX = '__ajax__';

	private $m_get;
	private $m_post;
	private $m_ajax;
	private $m_flash;
	private $m_isAjaxRequest;

	public function __construct() {
		if ((isset($_POST[self::$AJAX]) && '1'==$_POST[self::$AJAX])
		 || (isset($_GET[self::$AJAX]) && '1'==$_GET[self::$AJAX])) {
			$this->m_ajax = Variables::secureVars(array_merge_recursive($_POST, $_GET));
			$this->m_get = array();
			$this->m_post = array();
			$this->m_isAjaxRequest = true;
		}
		else {
			$this->m_get = Variables::secureVars($_GET);
			$this->m_post = Variables::secureVars($_POST);
			$this->m_ajax = array();
			$this->m_isAjaxRequest = false;
		}

		$this->m_flash = array();
		foreach ($_SESSION as $key => $value) {
			$parts = array();
			if (preg_match("#^".self::$FLASH."([a-zA-Z_].+)$#", $key, $parts)) {
				$this->m_flash[$parts[1]] = $value;
				unset($_SESSION[$key]);
			}
		}
	}

	public static function secureVars($rawData) {
		$secureData = array();
		foreach($rawData as $key => $value) {
			$secureData[$key] = Variables::sanitize($value);
		}

		return $secureData;
	}

	public static function sanitize($value) {
		if (is_array($value)) {
			return Variables::secureVars($value);
		}
		else {
			return htmlspecialchars($value, ENT_QUOTES);
		}
	}

	public function post($name, $type='') {
		return $this->getVar($this->m_post, $name, $type);
	}

	public function get($name, $type='') {
		return $this->getVar($this->m_get, $name, $type);
	}

	public function ajax($name, $type='') {
		return $this->getVar($this->m_ajax, $name, $type);
	}

	public function flash($name, $type='') {
		return $this->getVar($this->m_flash, $name, $type);
	}

	public function setFlash($key, $value) {
		return $_SESSION["flash_".$key] = $value;
	}

	public function isAjaxRequest() {	return $this->m_isAjaxRequest;	}

	public function hasVars($type=NULL) {
		switch ($type) {
			case 'get':
				return !empty($this->m_get);

			case 'post':
				return !empty($this->m_post);

			case 'ajax':
				return !empty($this->m_ajax);

			case 'flash':
				return !empty($this->m_flash);

			default:
				return !(empty($this->m_post) && empty($this->m_get)
					&& empty($this->m_ajax) && empty($this->m_flash));
		}
	}

	private function getVar(&$array, $name, $type) {
		if (isset($array[$name])) {
			return $this->format($array[$name], $type);
		}
		else {
			return NULL;
		}
	}

	private function format($value, $type) {
		switch ($type) {
			case 'int':
				return intval($value);

			case 'string':
				return strval($value);

			default:
				return $value;
		}
	}

	private function setFlashMessage($type, $message) {
		if (!isset($_SESSION[Variables::$FLASH.$type])) {
			$_SESSION[Variables::$FLASH.$type] = array();
		}

		$_SESSION[Variables::$FLASH.$type][] = $message;
	}

	public function erreur($message) {
		$this->setFlashMessage(self::$ERREUR, $message);
	}

	public function warning($message) {
		$this->setFlashMessage(self::$WARNING, $message);
	}

	public function succes($message) {
		$this->setFlashMessage(self::$SUCCESS, $message);
	}

	public function info($message) {
		$this->setFlashMessage(self::$INFO, $message);
	}

	public function renderMessages($type, $titre, $class) {
		$messages = '';

		if (isset($this->m_flash[$type])) {
			foreach ($this->m_flash[$type] as $message) {
				$messages.= "<p class=\"alert $class\">
				<button class=\"close\" data-dismiss=\"alert\">×</button>
				<strong>$titre : </strong>$message
				</p>";
			}
		}

		return $messages;
	}

	public function afficherMessages() {
		$messages = $this->renderMessages(self::$ERREUR, 'Erreur', 'alert-error');
		$messages.= $this->renderMessages(self::$WARNING, 'Warning', 'alert-block');
		$messages.= $this->renderMessages(self::$SUCCESS, 'Succés', 'alert-success');
		$messages.= $this->renderMessages(self::$INFO, 'Info', 'alert-info');

		return $messages;
	}
}
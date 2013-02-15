<?php

class Variables {
	private static $FLASH = 'flash_';
	private static $ERREUR = '__erreur__';
	private static $INFO = '__info__';
	private static $WARNING = '__warning__';
	private static $SUCCESS = '__success__';

	private static $AJAX = '__ajax__';

	private $_get;
	private $_post;
	private $_ajax;
	private $_flash;
	private $_isAjaxRequest;

	public function __construct() {
		if ((isset($_POST[self::$AJAX]) && '1'==$_POST[self::$AJAX])
		 || (isset($_GET[self::$AJAX]) && '1'==$_GET[self::$AJAX])) {
			$this->_ajax = Variables::secureVars(array_merge_recursive($_POST, $_GET));
			$this->_get = array();
			$this->_post = array();
			$this->_isAjaxRequest = true;
		}
		else {
			$this->_get = Variables::secureVars($_GET);
			$this->_post = Variables::secureVars($_POST);
			$this->_ajax = array();
			$this->_isAjaxRequest = false;
		}

		$this->_flash = array();
		foreach ($_SESSION as $key => $value) {
			$parts = array();
			if (preg_match("#^".self::$FLASH."([a-zA-Z_].+)$#", $key, $parts)) {
				$this->_flash[$parts[1]] = $value;
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
		return $this->getVar($this->_post, $name, $type);
	}

	public function get($name, $type='') {
		return $this->getVar($this->_get, $name, $type);
	}

	public function ajax($name, $type='') {
		return $this->getVar($this->_ajax, $name, $type);
	}

	public function flash($name, $type='') {
		return $this->getVar($this->_flash, $name, $type);
	}

	public function setFlash($key, $value) {
		return $_SESSION["flash_".$key] = $value;
	}

	public function isAjaxRequest() {	return $this->_isAjaxRequest;	}

	public function has($key, $type = 'all') {
		switch ($type) {
			case 'get':
				return array_key_exists($key, $this->_get);

			case 'post':
				return array_key_exists($key, $this->_post);

			case 'ajax':
				return array_key_exists($key, $this->_ajax);

			case 'flash':
				return false;

			default:
				return in_array($key, array_merge($this->_get, $this->_post, $this->_ajax));
		} 
	}

	public function hasVars($type=NULL) {
		switch ($type) {
			case 'get':
				return !empty($this->_get);

			case 'post':
				return !empty($this->_post);

			case 'ajax':
				return !empty($this->_ajax);

			case 'flash':
				return !empty($this->_flash);

			default:
				return !(empty($this->_post) && empty($this->_get)
					&& empty($this->_ajax) && empty($this->_flash));
		}
	}

	public function setDefaultGet($name, $value) {
		$this->setDefaultValue($this->_get, $name, $value);
	}

	public function setDefaultPost($name, $value) {
		$this->setDefaultValue($this->_post, $name, $value);
	}

	private function setDefaultValue(&$vars, $name, $value) {
		if (!array_key_exists($name, $vars)) {
			$vars[$name] = $value;
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

		if (isset($this->_flash[$type])) {
			foreach ($this->_flash[$type] as $message) {
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
<?php

class Variables {
	private $m_get;
	private $m_post;
	private $m_ajax;
	private $m_flash;
	private $m_isAjaxRequest;

	public function __construct() {
		if ((isset($_POST['ajax']) && '1'==$_POST['ajax'])
		 || (isset($_GET['ajax']) && '1'==$_GET['ajax'])) {
			$this->m_ajax = $this->secureVars(array_merge_recursive($_POST, $_GET));
			$this->m_get = array();
			$this->m_post = array();
			$this->m_isAjaxRequest = true;
		}
		else {
			$this->m_get = $this->secureVars($_GET);
			$this->m_post = $this->secureVars($_POST);
			$this->m_ajax = array();
			$this->m_isAjaxRequest = false;
		}

		$this->m_flash = array();
		foreach ($_SESSION as $key => $value) {
			$parts = array();
			if (preg_match("#^flash_([a-zA-Z].+)$#", $key, $parts)) {
				$this->m_flash[$parts[1]] = $value;
				unset($_SESSION[$key]);
			}
		}
	}

	private function secureVars($rawData) {
		$secureData = array();
		foreach($rawData as $key => $value) {
			$secureData[$key] = $this->sanitize($value);
		}

		return $secureData;
	}

	private function sanitize($value) {
		if (is_array($value)) {
			return $this->secureVars($value);
		}
		else {
			return htmlspecialchars($value);
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
}
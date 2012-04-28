<?php

class Variables {
	private $m_get;
	private $m_post;
	
	public function __construct() {
		$this->secureVars($_GET, $this->m_get);
		$this->secureVars($_POST, $this->m_post);
	}
	
	private function secureVars(&$rawData, &$secureData) {
		foreach($rawData as $key => $value) {
			$secureData[$key] = $this->sanitize($value);
		}
	}
	
	private function sanitize($value) {
		return htmlspecialchars($value);
	}
	
	public function post($name, $type='') {
		return $this->format($this->m_post[$name], $type);
	}
	
	public function get($name, $type='') {
		return $this->format($this->m_get[$name], $type);
	}
	
	private function format($value, $type) {
		switch ($type) {
			case 'int':
				return intval($value);
				
			default:
				return $value;
		}
	}
}
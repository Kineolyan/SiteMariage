<?php

class Variables {
	private $m_get;
	private $m_post;
	private $m_ajax;
	private $m_isAjaxRequest;
	
	public function __construct() {
		if ((isset($_POST['ajax']) && '1'==$_POST['ajax'])
		 || (isset($_GET['ajax']) && '1'==$_GET['ajax'])) {
			$this->m_ajax = array_merge_recursive($_POST, $_GET);
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
	
	public function isAjaxRequest() {	return $this->m_isAjaxRequest;	}
	
	public function hasVars($type=NULL) {
		switch ($type) {
			case 'get':
				return !empty($this->m_get);
				
			case 'post':
				return !empty($this->m_post);
				
			case 'ajax':
				return !empty($this->m_ajax);
				
			default:
				return !(empty($this->m_post) 
					&& empty($this->m_get) && empty($this->m_ajax));
		}
	}
	
	private function getVar(&$array, $name, $type='string') {
		if (isset($array[$name])) {
			return $this->format($array[$name], $type);
		}
		else {
			switch ($type) {
				case 'int':
					return 0;
					
				case 'string':
					return '';
					
				default:
					return NULL;
			}
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
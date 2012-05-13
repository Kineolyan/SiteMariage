<?php

class Form {
	private $m_name;
	private $m_itemId;
	private $m_useId;
	static private $NO_ID = -1;
	
	public function __construct($name, $itemId = -1) {
		$this->m_name = $name;
		$this->m_itemId = $itemId;
		$this->m_useId = self::$NO_ID!=$itemId;
	}
	
	public function useId($use) {	$this->m_useId = $use;	}
	
	public function create($action, $id='', $class='', $attributes=array()) {
		$parametres = '';
		
		if (''!=$id) {
			$parametres.= " id='$id'";
		}
		if (''!=$class) {
			$parametres.= " class='$class'";
		}
		if (count($attributes)>0) {
			foreach ($attributes as $attribute => $value) {
				$parametres.= " $attribute='$value'";
			}
		}
		
		return "<form action='$action' method='post' $parametres>\n";
	}
	
	public function end() {
		return "</form>\n";
	}
	
	public function input($name, $label, $value='') {
		$for = $this->forValue($name);
		
		return "<label for='$for'>$label</label>
		<input type='text' id='$for' name='{$this->nameValue($name)}' value='$value'/>\n";
	}
	
	public function radio($name, $label, $values) {
		$for = $this->forValue($name);
		
		$radioButtons = "<label for='$for'>$label</label>\n";
		foreach ($values as $value => $state) {
			$for = $this->forValue($name.'_'.$value);
			
			$radioButtons.= "<input type='radio' id='$for' name='{$this->nameValue($name)}' value='$value'";
			if ($state) {
				$radioButtons.= " checked='checked'";
			}
			$radioButtons.= "/><label for='$for'>$label</label>\n";
		}
		
		return $radioButtons;
	}
	
	public function check($name, $label, $values) {
		$checkboxes = "";
		foreach ($values as $value => $state) {
			$for = $this->forValue($name.'_'.$value);
			
			$checkboxes = "<input type='checkbox' id='$for' name='{$this->nameValue($name)}' value='$value'";
			if ($state) {
				$checkboxes.= " checked='checked'";
			}
			$checkboxes.= "/><label for='$for'>$label</label>\n";
		}
		
		return $checkboxes;
	}
	
	public function select($name, $label, $values, $multiple=false) {
		$for = $this->forValue($name);
		
		$select = "<label for='$for'>$label</label>\n";
		$select = "<select id='$for' name='{$this->nameValue($name)}";
		$select.= $multiple? " multiple='multiple' ": '';
		$select.= "'>\n";
		foreach ($values as $value => $state) {
			$select.= "<option value='$value'";
			if ($state) {
				$select.= " selected='selected'";
			}
			$select.= ">$value</option>\n";
		}
		$select.= '</select>';
		
		return $select;
	}
	
	public function hidden($name, $value='') {
		$for = $this->forValue($name);
		
		return "<input type='hidden' name='{$this->nameValue($name)}' value='$value'/>\n";
	}
	
	public function submit($name, $value) {
		$for = $this->forValue($name);
		
		return "<input type='submit' name='{$this->nameValue($name)}' value='$value'/>\n";
	}
	
	// Fonctions accessoires
	
	private function nameValue($name) {
		$suffixe = (self::$NO_ID!=$this->m_itemId && $this->m_useId)?
			/*"[{$this->m_itemId}]"*/ '[]': '';
		return $name.$suffixe;
	}
	
	private function forValue($name) {
		$suffixe = (self::$NO_ID!=$this->m_itemId && $this->m_useId)?
			"[{$this->m_itemId}]": '';
		return $this->m_name.'_'.$name.$suffixe;
	}
}
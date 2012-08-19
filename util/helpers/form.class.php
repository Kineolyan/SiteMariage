<?php

class Form {
	private $_name;
	private $_itemId;
	private $_useId;
	private $_multipleFields;
	public static $NO_ID = -1;
	public static $MULTIPLE_ID = -2;

	public function __construct($name, $itemId = -1) {
		$this->_name = $name;
		if (self::$NO_ID != $itemId) {
			$this->_itemId = $itemId;
			$this->_useId = true;
			$this->_multipleFields = false;
		}
		else if (self::$MULTIPLE_ID == $itemId) {
			$this->_itemId = self::$MULTIPLE_ID;
			$this->_useId = false;
			$this->_multipleFields = true;
		}
		else {
			$this->_itemId = self::$NO_ID;
			$this->_useId = false;
			$this->_multipleFields = false;
		}
	}

	public function useId($use) {	$this->_useId = $use;	}

	public function multipleFields($use) {	$this->_multipleFields = $use;	}

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
		return $this->inputTag('text', $name, $value, array('label' => $label));
	}

	public function password($name, $label, $value='') {
		return $this->inputTag('password', $name, $value, array('label' => $label));
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

	public function check($name, $label, $value, $selected) {
		$checkbox = "";
		$for = $this->forValue($name.'_'.$value);

		$checkbox = "<input type='checkbox' id='$for' name='{$this->nameValue($name)}' value='$value'";
		if ($selected) {
			$checkbox.= " checked='checked'";
		}
		$checkbox.= "/><label class='checkbox' for='$for'>$label</label>\n";

		return $checkbox;
	}

	public function select($name, $label, $values, $selected='', $multiple=false) {
		$selectedFound = false;
		$for = $this->forValue($name);

		$select = '';
		if (''!=$label) {
			$select.= "<label for='$for'>$label</label>\n";
		}
		$select.= "<select ";
		$select.= ''!=$label? "id='$for' ": '';
		$select.= "name='{$this->nameValue($name)}";
		$select.= $multiple? " multiple='multiple' ": '';
		$select.= "'>\n";
		foreach ($values as $value => $valueLabel) {
			$select.= "<option value='$value'";
			if (!$selectedFound && $value == $selected) {
				$select.= " selected='selected'";
				$selectedFound = true;
			}
			$select.= ">$valueLabel</option>\n";
		}
		$select.= '</select>';

		return $select;
	}

	public function hidden($name, $value='') {
		return $this->inputTag('hidden', $name, $value);
	}

	public function submit($name, $value, $params = array()) {
		return $this->inputTag('submit', $name, $value, $params);
	}

	// Fonctions accessoires

	private function inputTag($type, $name, $value, $params = array()) {
		$id = isset($params['id'])?  "id='$params[id]'": '';
		$class = isset($params['class'])?  "class='$params[class]'": '';

		if (isset($params['label']) && '' != $params['label']) {
			if ('' == $id) {
				$for = $this->forValue($name);
				$id = "id='$for'";
			} else {
				$for = $id;
			}
			$labelHtml = "<label for='$for'>$params[label]</label>";
		} else {
			$labelHtml = '';
		}

		return "$labelHtml <input type='$type' $id $class name='{$this->nameValue($name)}' value='$value'/>\n";
	}

	private function nameValue($name) {
		if (self::$NO_ID!=$this->_itemId && $this->_useId) {
			$suffixe = "[{$this->_itemId}]";
		} else if (self::$NO_ID!=$this->_itemId && $this->_multipleFields) {
			$suffixe = '[]';
		} else {
			$suffixe = '';
		}
		return $name.$suffixe;
	}

	private function forValue($name) {
		$suffixe = (self::$NO_ID!=$this->_itemId && ($this->_useId || $this->_multipleFields)	)?
			"[{$this->_itemId}]": '';
		return $this->_name.'_'.$name.$suffixe;
	}
}
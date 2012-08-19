<?php

class Categories {
	private static $categories = array();

	public static function init($db) {
		if (0 < $db->select('categories', 'id, categorie', '')) {
			while ($data = $db->fetch()) {
				Categories::$categories[$data['id']] = $data['categorie'];
			}
		}
		$db->endQuery();
	}

	public static function id($categorie) {
		foreach (Categories::$categories as $key => $value) {
			if ($value == $categorie) {
				return $key;
			}
		}
		return -1;
	}

	public static function categorie($id) {
		if (array_key_exists($id, Categories::$categories)) {
			return Categories::$categories[$id];
		}

		return '';
	}

	public static function getCategories() {
		return self::$categories;
	}
}


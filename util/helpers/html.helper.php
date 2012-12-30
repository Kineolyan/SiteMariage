<?php

class Html {
	private static function getAttribute($nom, $attributs) {
		if (is_array($nom)) {
			foreach ($nom as $key) {
				if (array_key_exists($key, $attributs) && '' != $attributs[$key]) {
					return $attributs[$key];
				}
				return '';
			}
		} else if (is_int($nom) || is_string($nom)) {
			return array_key_exists($nom, $attributs)? $attributs[$nom]: '';
		} else {
			return '';
		}
	}

	public static function img($src, $attributs = array()) {
		return sprintf('<img src="%1$s" class="%4$s" id="%5$s" alt="%3$s" title="%2$s"/>',
			$src 
			, self::getAttribute('title', $attributs)
			, self::getAttribute(array('alt', 'title'), $attributs)
			, self::getAttribute('class', $attributs)
			, self::getAttribute('id', $attributs)
		);
	}

	public static function link($lien, $href, $attributs = array()) {
		return sprintf('<a href="%1$s" class="%4$s" id="%5$s" title="%3$s"/>%2$s</a>',
			$href, $lien
			, self::getAttribute('title', $attributs)
			, self::getAttribute('class', $attributs)
			, self::getAttribute('id', $attributs)
		);
	}

	public static function tip($text, $tip, $placement = 'bottom') {
		echo sprintf('<span class="tip" rel="tooltip" title="%2$s" data-placement="%3$s">%1$s</span>',
			$text, $tip, $placement);
	}
}
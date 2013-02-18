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
		if ('' != self::getAttribute('tab', $attributs)) {
			if (false === strpos('?', $href)) {
				$href.= '?tab='.$attributs['tab'];
			} else {
				$href.= '&tab='.$attributs['tab'];
			}
		}

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

	private static function isTabActive($tab) {
		global $VARS;

		return $tab == $VARS->get('tab');
	}

	private static function tabLink($tab, $title) {
		return sprintf('<li class="%s"><a href="#%s" data-toggle="tab">%s</a><li>'
			, self::isTabActive($tab) ? 'active' : ''
			, $tab, $title);
	}

	public function tabNavigation($tabs) {
		echo '<ul class="nav nav-tabs">';

		foreach ($tabs as $tab => $title) {
			echo self::tabLink($tab, $title);
		}

		echo '</ul>';
	}

	public static function activateTab($tab) {
		echo self::isTabActive($tab) ? 'active' : '';
	}
}
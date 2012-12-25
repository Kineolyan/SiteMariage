<?php

class Utils {
	public static function escape($subject, $char) {
		//var_dump("$subject [$char] -> " . str_replace($char, "\\$char", $subject));
		
		return str_replace($char, "\\$char", $subject);
	}

	public static function escapeQuote($subject) {
		return self::escape($subject, "'");
	}

	public static function escapeDblQuote($subject) {
		return self::escape($subject, '"');
	}
}

?>
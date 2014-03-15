<?php #coding: utf-8

/**
 * @file bf.php
 *
 * This is the bilouFramework
 */

final class bf
{
	const UTF8 = 'UTF8';

	static function buildUrl($page, $args=null, $keepArgs=false) {
		$baseUrl = ($page === null) ? $_SERVER['PHP_SELF'] : BASE_URL.$page;
			
		if (self::is_array($args) && sizeof($args) > 0)
		{
			$firstArg = true;
			@reset($args);
			while (list($name, $value) = @each($args))
			{
				$baseUrl.= (($firstArg === true) ? '?' : '&').$name.'='.$value;
				$firstArg = false;
			}
		}
		return $baseUrl;
	}

	static function redirect($page, $args=null) {
		header('Location: '.self::buildUrl($page, $args));
	}

	static function getBaseUrl() {
		return BASE_URL;
	}

	/* String functions */
	static function trim($string) {
		return trim($string);
	}

	static function is_empty($string) {
		$string = self::trim($string);
		if (empty($string))
			return true;
		return false;
	}

	static function strlen($string, $encoding=self::UTF8) {
		return mb_strlen($string, $encoding);
	}

	static function substr($string, $start, $length=null, $encoding=self::UTF8) {
		return mb_substr($string, $start, (self::is_int($length)) ? $length : self::strlen($string) - $start, $encoding);
	}

	static function convertCase($string, $mode, $encoding=self::UTF8) {
		if (self::strlen($string) && $mode !== null)
			return mb_convert_case($string, $mode, $encoding);
		else
			return $string;
	}


	/* Date functions */

	/* Type functions */
	static function is_int($var) {
		return is_int($var);
	}

	static function is_double($var) {
		return is_double($var);
	}

	static function is_bool($var) {
		return is_bool($var);
	}

	static function is_real($var) {
		return is_real($var);
	}

	static function is_array($var) {
		return is_array($var);
	}

	/* Encoding functions */
	static function utf8_encode($string, $from='ISO-8859-15') {
		return mb_convert_encoding($string, self::UTF8, $from);
	}

	static function utf8_decode($string, $to='ISO-8859-15') {
		return mb_convert_encoding($string, $to, self::UTF8);
	}
}
?>

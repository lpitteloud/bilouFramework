<?php #coding utf-8
/**
 * @file bfSession.php
 */

class bfSession extends bfApi
{
	static function set($key, $value) {
		$_SESSION[$key] = $value;
		return true;
	}

	static function setInArray($arrayName, $key, $value) {
		if (($array = self::get($arrayName)) !== null)
		{
			if (!is_array($array))
				return false;

			$array[$key] = $value;
		}
		else
		{
			$array = array($key => $value);
		}
		return self::set($arrayName, $array);
	}

	static function get($key) {
		if (isset($_SESSION[$key]))
			return $_SESSION[$key];
		else
			return null;
	}

	static function getInArray($arrayName, $key) {
		if (($array = self::get($arrayName)) === null || !is_array($array) || !array_key_exists($key, $array))
			return null;
		else
			return $array[$key];
	}
}
?>

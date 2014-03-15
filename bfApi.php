<?php #coding utf-8
/**
 * @file bfApi.php
 *
 * This is the bilouFramework api class
 */

class bfApi
{
    const   E_OK = 0;
    const   E_SYS = 1;
    const   E_PERM = 2;
    const   E_EMPTY = 3;
    const   E_NOSUCH = 4;
    const   E_INVALID = 5;
    const   E_BAD = 6;
    const   E_SHORT = 7;
    const   E_LONG = 8;

    static protected $errorItem = null;

    static public function ok() {
        return self::ret(self::E_OK);
    }

    static function sys($errorItem=null) {
        return self::ret(self::E_SYS, $errorItem);
    }

    static function perm($errorItem=null) {
        return self::ret(self::E_PERM, $errorItem);
    }

    static function isempty($errorItem=null) {
        return self::ret(self::E_EMPTY, $errorItem);
    }

    static function invalid($errorItem=null) {
        return self::ret(self::E_INVALID, $errorItem);
    }

    static function nosuch($errorItem=null) {
        return self::ret(self::E_NOSUCH, $errorItem);
    }

    static function bad($errorItem=null) {
        return self::ret(self::E_BAD, $errorItem);
    }

    static private function ret($errorCode, $errorItem=null) {
        if ($errorItem !== null)
            self::$errorItem = bf::trim($errorItem);

        return $errorCode;
    }

    static function getErrorItem() {
        return self::$errorItem;
    }

    #################################################################
    ## checkLength() #

    # E_OK
    # E_SHORT
    # E_LONG
    # E_EMPTY
    static function checkLength($name, $string, $min=null, $max=null) {
        $length = bf::strlen($string);

        if ($length === 0)
            return self::isempty($name);
        elseif ($min !== null && $length < $min)
            return self::short($name);
        elseif ($max !== null && $length > $max)
            return self::long($name);
        else
            return self::ok();
    }


    #################################################################
    ## checkInt() #

    # E_OK
    # E_SHORT
    # E_LONG
    # E_EMPTY
    # E_INVALID
    static function checkInt($name, $int, $min=null, $max=null) {
        if ($int === null || empty($int))
            return self::isempty($name);
        elseif (!is_int($int))
            return self::invalid($name);
        elseif ($min !== null && $int < $min)
            return self::short($name);
        elseif ($max !== null && $int > $max)
            return self::long($name);
        else
            return self::ok();
    }

	#################################################################
	## checkCharacters() #

	# E_OK
	# E_INVALID
	static function checkCharacters($name, $string, $pool) {
        $len = bf::strlen($string);
        for($i = 0; $i < $len; $i++)
        {
            if (strpos($pool, $string{$i}) === false)
                return self::invalid($name);
        }
        return self::ok();
	}

    #################################################################
    # checkEmail() #

    # E_OK
    # E_INVALID
    # E_EMPTY
    static private function checkEmail($name, $email)
    {
        if ($email === null || empty($email))
            return self::isempty($name);
        elseif (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $email))
            return self::invalid($name);
        
        return self::ok();
    }
}
?>

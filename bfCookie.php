<?php #coding: utf-8
/**
 * @file bfCookie.php
 */

class bfCookie extends bfApi
{
    static function set($name, $value, $expire=0, $path='/') {
        if (headers_sent())
            return false;

        return setcookie($name, $value, $expire, $path);
    }

    static function get($name, &$value) {
        $value = (isset($_COOKIE[$name])) ? $_COOKIE[$name] : null;
        return true;
    }

    static function delete($name, $path='/') {
        return self::set($name, false, time()-3600, $path);
    }
}
?>

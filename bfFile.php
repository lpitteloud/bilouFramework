<?php #coding: utf-8
/**
 * @file bfFile.php
 */

class bfFile extends bfApi
{
    const RW_TIMEOUT = 5; // reading / writing timeout: check if file is readable / writeable n times before returning a sys error.

    static function getMimeType($filepath) {
        /* Check Fileinfo PECL extension ! */
    }

    static function create($filename, $path=TMP) {
        if (self::exists(self::getRealpath($path.$name)))
            return self::already();

        if (touch($path.$filename) === false)
            return self::sys();

        return self::ok();
    }

    static function exists($filepath) {
        if (file_exists(self::getRealpath($filepath)))
            return true;
        else
            return false;
    }

    static function getRealpath($filepath) {
        return realpath($filepath);
    }

    static function read($filepath, &$content) {
        if (self::exists(self::getRealpath($filepath)) === false)
            return self::nosuch();

        for ($i=0; is_readable($filepath) === false && $i < self::READING_TIMEOUT; $i++)
        {
            if ($i === self::RW_TIMEOUT)
                return self::sys();

            sleep(1);
        }

        if (!($fp = fopen($path, 'r')))
            return self::sys();

        if (flock($fp, LOCK_EX) !== false)
        {
            $content = unserialize(file_get_contents($path));
            flock($fp, LOCK_UN);
        }
        else
            return self::sys();

        fclose($fp);
        return self::ok();
    }

    static function write($filepath, $content) {
        for ($i=0; is_writeable($path) === false && $i < self::RW_TIMEOUT; $i++)
        {
            if ($i === self::RW_TIMEOUT)
                return self::sys();

            sleep(1);
        }

        if (!($fp = fopen($path, 'w')))
            return self::sys();

        if (flock($fp, LOCK_EX) !== false)
        {
            fwrite($fp, serialize($content));
            flock($fp, LOCK_UN);
        }
        else
            return self::sys();

        fclose($fp);
        return self::ok();
    }

    # E_OK
    # E_EMPTY
    # E_NOSUCH
    # E_PERM
    # E_SYS
    static function delete($filepath) {
        if (bf::is_empty($filepath))
            return self::isempty();

        if (self::exists(self::getRealpath($filepath)) === false)
            return self::nosuch();

        // TODO: add perm check

        if (unlink($filepath) == false)
            return self::sys();

        return self::ok();
    }
}
?>

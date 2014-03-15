<?php #coding: utf-8
/**
 * @file bfCrypt.php
 */

class bfCrypt extends bfApi
{
    #################################################################
    ## sha256()

    # string(256)
    static function sha256($stringToCrypt)
    {
        return hash('sha256', $stringToCrypt, false);
    }


    #################################################################
    ## sha512()

    # string(512)
    static function sha512($stringToCrypt)
    {
        return hash('sha512', $stringToCrypt, false);
    }


    #################################################################
    ## md5()

    # string(32)
    static function md5($stringToCrypt)
    {
        return hash('md5', $stringToCrypt, false);
    }
}

?>

<?php
require_once 'CleanCodeClass.php';

class CleanCode extends CleanCodeClass
{
    const VERSION = 'alpha';
    const PHP_REQUIRED_VERSION = '3.1';
    
    public static function checkPhpVersion()
    {
        if(!defined('PHP_VERSION') || PHP_VERSION < self::PHP_REQUIRED_VERSION)
        {
            die('CleanCode requer PHP versão ' . self::PHP_REQUIRED_VERSION . ' ou superior.');
        }
    }
}
?>
<?php
class CleanCodeClass
{
	protected static $debug = false;

	const PHP_REQUIRED_VERSION = '5.3';
	
	public static function checkPhpVersion()
	{
		if(!defined('PHP_VERSION') || PHP_VERSION < self::PHP_REQUIRED_VERSION)
		{
			die('CleanCode Framework require PHP 5.3 or more. (Actually: ' . PHP_VERSION . ')');
		}
	}
	
	public static function debugMode()
	{
		static::$debug = true;
	}
	
	protected static function searchPos($array, $pos, $default = '')
	{
		return isset($array[$pos]) && $array[$pos]? $array[$pos] : $default;
	}
	
	protected static function toCamelCase($uri)
	{
		$parts	= explode('_', self::format_url($uri, false));
		$result = array_shift($parts);
		
		foreach($parts as $part)
		{
			$result .= ucfirst($part);
		}
		
		return $result;
	}
	
	protected static function format_url($string, $is_file)
	{
		$search = array('/á|à|ã|â|ä|Á|À|Ã|Â|Ä/', '/é|è|ê|ẽ|ë|É|È|Ê|Ẽ|Ë/', '/í|ì|ĩ|î|ï|Í|Ì|Î|Ĩ|Ï/', '/ó|ò|ô|õ|ö|Ó|Ò|Õ|Ô|Ö/', '/ú|ù|û|ũ|ü|Ú|Ù|Ũ|Û|Ü/', '/ç/', '/\s/', '/[^\w\_\-\/'.($is_file? '\.' : '').']/', '/\/{2,}/');
		$replace = array('a', 'e', 'i', 'o', 'u', 'c', '_', '', '/');
		
		return preg_replace($search, $replace, strtolower($string));
	}
}

CleanCodeClass::checkPhpVersion();
?>
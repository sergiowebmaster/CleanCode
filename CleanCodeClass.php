<?php
class CleanCodeClass
{
	private static $debugMode = false;
	
	private static $messages = array(
			'directory_not_found' => 'Directory %s is not found!',
			'upload_error' => 'Upload failed!',
			'validation_field_error' => 'Invalid %s!',
			'view_not_found' => '%s is not found!',
			'php_version_error' => 'CleanCode Framework requires PHP 5.3 or more. (Actually: %s)'
	);
	
	public static $means = array();

	const PHP_REQUIRED_VERSION = '5.3';
	
	protected static function showDebug()
	{
		echo 'Debug mode';
		self::$debugMode = true;
	}
	
	protected function debug()
	{
		// Implement
	}
	
	protected static function searchPos($array, $pos, $default = '')
	{
		return isset($array[$pos]) && $array[$pos]? $array[$pos] : $default;
	}
	
	private static function searchMsg($data, $label, $default, $var = '')
	{
		return sprintf(self::searchPos($data, $label, $default), $var);
	}
	
	protected static function msg($label, $var = '')
	{
		return self::searchMsg(self::$messages, $label, $label, $var);
	}
	
	protected static function getMeans($index, $var = '')
	{
		return self::searchMsg(static::$means, $index, 'means' . $index, $var);
	}
	
	public static function checkPhpVersion()
	{
		if(!defined('PHP_VERSION') || PHP_VERSION < self::PHP_REQUIRED_VERSION)
		{
			die(self::msg('php_version_error', PHP_VERSION));
		}
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
	
	protected static function returnIfExists($value, $default = '')
	{
		return $value? $value : $default;
	}
	
	function __destruct()
	{
		if(self::$debugMode) $this->debug();
	}
}

CleanCodeClass::checkPhpVersion();
?>
<?php
class CleanCodeClass
{
	protected static $debug = false;
	
	protected static $messages = array(
			'directory_not_found' => 'Directory %s is not found!',
			'php_version_error' => 'CleanCode Framework requires PHP 5.3 or more. (Actually: %s)',
			'password_incorrect' => 'Incorrect password!',
			'password_confirm_error' => 'Password confirmation doesn\'t match Password',
			'login_ok' => 'Redirecting...',
			'login_failed' => 'Login failed!'
	);

	const PHP_REQUIRED_VERSION = '5.3';
	
	public static function debugMode()
	{
		static::$debug = true;
	}
	
	protected static function searchPos($array, $pos, $default = '')
	{
		return isset($array[$pos]) && $array[$pos]? $array[$pos] : $default;
	}
	
	protected static function msg($label, $var = '')
	{
		return sprintf(self::searchPos(self::$messages, $label, '???'), $var);
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
}

CleanCodeClass::checkPhpVersion();
?>
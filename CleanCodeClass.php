<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

class CleanCodeClass
{
	private static $aliases = array();
	
	protected static function searchPos($pos, $array, $default = '')
	{
		return isset($array[$pos])? $array[$pos] : $default;
	}
	
	protected static function parseVar($text)
	{
		$parts	= explode('_', $text);
		$result = '';
		
		foreach($parts as $part)
		{
			$result .= ucfirst($part);
		}
		
		return $result;
	}
	
	public static function addAlias($alias, $path)
	{
		self::$aliases[$alias] = $path;
	}
	
	protected static function convertPath($path)
	{
		if(strstr($path, ':'))
		{
			$parts = explode(':', $path);
			return self::searchPos($parts[0], self::$aliases) . $parts[1];
		}
		else
		{
			return $path;
		}
	}
}
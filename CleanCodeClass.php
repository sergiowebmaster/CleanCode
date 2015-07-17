<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

class CleanCodeClass
{
	protected static function searchPos($pos, $array, $default = '')
	{
		return isset($array[$pos])? $array[$pos] : $default;
	}
	
	protected static function toCamelCase($uri)
	{
		$parts	= explode('_', $uri);
		$result = '';
		
		foreach($parts as $part)
		{
			$result .= ucfirst($part);
		}
		
		return $result;
	}
}
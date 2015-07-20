<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

class CleanCodeClass
{
	const VERSION = 'beta';
	
	protected static function searchIn($array, $index, $default = '')
	{
		return isset($array[$index])? $array[$index] : $default;
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
}
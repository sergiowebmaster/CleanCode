<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

class CleanCodeClass
{
	const VERSION = 'beta';
	
	protected static function searchIn($array, $index, $default = '')
	{
		return $index? isset($array[$index])? $array[$index] : $default : $array;
	}
	
	protected static function getRandomString($size)
	{
		$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$string = '';
		
		for ($i = 0; $i < $size; $i++)
		{
			$c = rand(0, strlen($characters));
			$string .= $characters[$c - 1];
		}
		
		return $string;
	}
	
	protected static function parseVar($text)
	{
		$parts	= preg_split('/[^a-z0-9]/i', $text, 0, PREG_SPLIT_NO_EMPTY);
		$result = '';
		
		foreach($parts as $part)
		{
			$result .= ucfirst($part);
		}
		
		return $result;
	}
}
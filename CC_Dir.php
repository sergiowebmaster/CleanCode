<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class CC_Dir extends CleanCodeClass
{
	private static $aliases = array();
	
	private $path = '';
	
	function __construct($path)
	{
		$this->path = self::getPath($path);
	}
	
	public static function addAlias($alias, $path)
	{
		self::$aliases[$alias] = $path;
	}
	
	public static function getPath($alias)
	{
		if(strstr($alias, ':'))
		{
			$parts = explode(':', $alias);
			return self::searchIn(self::$aliases, $parts[0]) . $parts[1];
		}
		else
		{
			return $alias;
		}
	}
}
?>
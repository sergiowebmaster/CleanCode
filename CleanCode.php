<?php
require_once 'CleanCodeClass.php';

class CleanCode extends CleanCodeClass
{
	const VERSION = 'alpha';
	
	/*
	 * Get the autoload instance.
	 * @access public
	 * @return void
	 */
	public static function getAutoload()
	{
		return self::$autoload;
	}
	
	public static function setCoreMessages($messages)
	{
		foreach ($messages as $key => $means)
		{
			self::$messages[$key] = addslashes($means);
		}
	}
}
?>
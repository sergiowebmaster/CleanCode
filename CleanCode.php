<?php
require_once 'CleanCodeClass.php';

class CleanCode extends CleanCodeClass
{
	const VERSION = 'alpha';
	
	/*
	 * Start an autoload instance.
	 * @access public
	 * @return void
	 */
	public static function useAutoload()
	{
		$autoload = new CleanCodeAutoload();
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
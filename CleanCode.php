<?php
require_once 'CleanCodeClass.php';

class CleanCode extends CleanCodeClass
{
	public static function checkCompatibility()
	{
		if(!class_exists('PDO'))
		{
			echo 'CleanCode Framework requires PDO.';
			die();
		}
	}
}
?>
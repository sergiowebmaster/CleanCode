<?php
require_once 'CleanCodeClass.php';

class CC_Autoload extends CleanCodeClass
{
	public static function init()
	{
		spl_autoload_register(array('CC_Autoload', 'loadController'));
	}
	
	private static function loadClass($alias)
	{
		$filename = CC_Dir::getPath($alias.'.php');
		if(file_exists($filename)) require_once $filename;
	}
	
	private static function loadController($className)
	{
		self::loadClass('controllers:'.$className);
	}
	
	private static function loadModel($className)
	{
		self::loadClass('models:'.$className);
	}
}
?>
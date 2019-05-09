<?php
class CleanCodeLanguage extends CleanCodeClass
{
	private static $directory = '';
	
	public static $path = 'language/';
	public static $list = array();
	
	function __construct($lang)
	{
		self::$directory = self::$path . $lang . '/';
	}
	
	public function load($fileName)
	{
		$src = self::$directory . $fileName . '.php';
		if(file_exists($src)) include $src;
	}
	
	public function translate($item)
	{
		return self::searchPos(self::$list, $item, $item);
	}
}
?>
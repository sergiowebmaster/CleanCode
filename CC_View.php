<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';
require_once 'CC_Dir.php';

class CC_View extends CleanCodeClass
{
	private static $ext = '.phtml';
	
	private static $data = array(
			'projectName' => '(Project)',
			'lang' => 'pt-br',
			'baseHref' => '',
			'title' => '',
			'css' => array(),
			'js' => array(),
			'meta' => array(),
			'view' => ''
	);
	
	private static $template = '';
	
	function __construct($data)
	{
		foreach ($data as $var => $value)
		{
			self::$data[$var] = $value;
		}
	}
	
	public static function setProjectName($name)
	{
		self::$data['projectName'] = $name;
	}
	
	public static function setBaseHref($url)
	{
		self::$data['baseHref'] = $url;
	}
	
	public static function setTemplate($folderName)
	{
		$data = explode(':', $folderName);
		
		if(!isset($data[1])) $data[1] = 'template';
		
		$themePath = CC_Dir::getPath('themes:' . $data[0].'/');
		CC_Dir::addAlias('theme', $themePath);
		self::$template = CC_Dir::getPath('theme:' . $data[1] . self::$ext);
	}
	
	public function show()
	{
		if(self::$template && file_exists(self::$template))
		{
			extract(self::$data);
			include self::$template;
		}
	}
	
	public static function formatText($plainText)
	{
		return nl2br(stripslashes($plainText));
	}
}
?>
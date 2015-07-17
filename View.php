<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class View extends CleanCodeClass
{
	private $ext = '.phtml';
	
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
		self::$template = self::convertPath('templates:' . $folderName . '/template.phtml');
	}
	
	public function show()
	{
		extract(self::$data);
		
		if(self::$template && file_exists(self::$template))
		{
			include self::$template;
		}
		else if($view && file_exists($view))
		{
			include $view;
		}
		else
		{
			echo 'View não encontrada!';
		}
	}
	
	public static function formatText($plainText)
	{
		return nl2br(stripslashes($plainText));
	}
}
?>
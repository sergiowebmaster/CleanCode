<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';
require_once 'CC_Dir.php';

class CC_View extends CleanCodeClass
{
	private static $ext = '.phtml';
	private static $format = 'html';
	
	private static $head = array(
			'projectName' => '(Project)',
			'lang' => 'pt-br',
			'baseHref' => '',
			'title' => '',
			'css' => array(),
			'js' => array(),
			'meta' => array()
	);
	
	private static $template = '';
	
	private $data = array();
	
	function __construct($data)
	{
		foreach ($data as $var => $value)
		{
			if(isset(self::$head[$var]))
			{
				self::$head[$var] = $value;
			}
			else
			{
				$this->data[$var] = $value;
			}
		}
	}
	
	public static function setFormat($format)
	{
		self::$format = strtolower($format);
	}
	
	public static function setProjectName($name)
	{
		self::$head['projectName'] = $name;
	}
	
	public static function setBaseHref($url)
	{
		self::$head['baseHref'] = $url;
	}
	
	public static function setTemplate($folderName)
	{
		$data = explode(':', $folderName);
		
		if(!isset($data[1])) $data[1] = 'template';
		
		$themePath = CC_Dir::getPath('themes:' . $data[0].'/');
		CC_Dir::addAlias('theme', $themePath);
		self::$template = CC_Dir::getPath('theme:' . $data[1] . self::$ext);
	}
	
	public static function setHtml($path)
	{
		self::$template = addslashes($path);
	}
	
	private function showInHTML()
	{
		if(self::$template && file_exists(self::$template))
		{
			$vars = array_merge(self::$head, $this->data);
			extract($vars);
			include self::$template;
		}
	}
	
	private function parseXML($data)
	{
		$xml = '';
		
		foreach ($data as $tag => $value)
		{
			$tag = preg_replace('/(\w)/', '$1', $tag);
			
			$xml .= '<'.$tag.'>';
			$xml .= is_array($value)? $this->parseXML($value) : utf8_encode($value);
			$xml .= '</'.$tag.'>';
		}
		
		return $xml;
	}
	
	public function show()
	{
		switch (self::$format)
		{
			case 'html':
				$this->showInHTML();
				break;
				
			case 'json':
				header('Content-Type: application/json');
				echo json_encode($this->data);
				break;
				
			case 'xml':
				header('Content-Type: application/xml');
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<xml>';
				echo $this->parseXML($this->data);
				echo '</xml>';
				break;
		}
	}
	
	public static function formatText($plainText)
	{
		return nl2br(stripslashes($plainText));
	}
}
?>
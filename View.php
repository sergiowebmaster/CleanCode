<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class View extends CleanCodeClass
{
	private static $data = array(
			'projectName' => '(Project)',
			'lang' => 'pt-br',
			'baseHref' => '',
			'title' => '',
			'meta' => array(),
			'css' => array(),
			'js' => array(),
			'html' => array());
	
	private static $aliases = array();
	
	private $vars = array();
	private $template = '';
	
	public static function setProjectName($name)
	{
		self::$data['projectName'] = $name;
	}
	
	private function convertPath($path)
	{
		if(strstr($path, ':'))
		{
			$parts = explode(':', $path);
			return self::searchPos($parts[0], self::$aliases) . $parts[1];
		}
		else
		{
			return $path;
		}
	}
	
	public static function setAlias($alias, $path)
	{
		self::$aliases[$alias] = $path;
	}
	
	public static function setBaseHref($url)
	{
		self::$data['baseHref'] = $url;
	}
	
	public function setTitle($title)
	{
		self::$data['title'] = $title;
	}
	
	public function addVar($var, $value)
	{
		$this->vars[$var] = $value;
	}
	
	public function addVars($arrayData)
	{
		foreach ($arrayData as $var => $value)
		{
			self::addVar($var, $value);
		}
	}
	
	public function addCSS($href, $params = '')
	{
		$href = $this->convertPath($href);
		self::$data['css'][$href] = $params;
	}
	
	public function addJS($src, $params = '')
	{
		$src = $this->convertPath($src);
		self::$data['js'][$src] = $params;
	}
	
	public function addHTML($path, $name = 'content')
	{
		$path = $this->convertPath($path);
		
		if(file_exists($path))
		{
			self::$data['html'][$name] = $path;
		}
		else
		{
			echo $path . ' não encontrado.';
		}
	}
	
	public function setTemplate($folder)
	{
		$path = self::convertPath('templates:'.$folder.'/');
		self::setAlias('template', $path);
		$this->template = $path . $folder . '.phtml';
	}
	
	public function show()
	{
		if($this->template && file_exists($this->template))
		{
			extract($this->vars);
			extract(self::$data);
			
			include $this->template;
		}
		else
		{
			echo json_encode($this->vars);
		}
	}
	
	public static function formatText($plainText)
	{
		return nl2br(stripslashes($plainText));
	}
}
?>
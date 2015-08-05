<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class CC_Controller extends CleanCodeClass
{
	private static $uri = '';
	private static $uriVetor = array();
	
	protected static $global = array();
	protected static $view = array();
	
	private static $rules = array();
	
	protected function addUriRule($index, $callback)
	{
		self::$rules[$index] = $callback;
	}
	
	protected function formatUri($uri)
	{
		return preg_replace('/(\/$)|[^a-z0-9-_\/]{1,}/', '', $uri);
	}

	public function defineUri()
	{
		$uri = $this->formatUri(self::get('uri'));
		self::$uriVetor = explode('/', $uri);
	}
	
	protected static function get($var = '', $default = '')
	{
		return self::searchIn($_GET, $var, $default);
	}
	
	protected static function post($var = '', $default = '')
	{
		return self::searchIn($_POST, $var, $default);
	}
	
	protected static function files($var = '', $default = array())
	{
		return self::searchIn($_FILES, $var, $default);
	}
	
	protected static function session($name, $default = '')
	{
		return self::searchIn($_SESSION, $name, $default);
	}
	
	protected function setSession($name, $value)
	{
		$_SESSION[$name] = addslashes($value);
	}
	
	protected function unsetSession($name)
	{
		unset($_SESSION[$name]);
	}
	
	protected function redirect($path)
	{
		header('Location:'.$path);
	}
	
	public function route($routeMask, $args)
	{
		echo 'Nenhuma rota definida!';
	}
	
	public function selectRoute()
	{
		$controller = $this;
		$mask = array();
		$args = array();
		
		foreach (self::$uriVetor as $i => $uri)
		{
			$class = 'Controller' . self::parseVar($uri);
			
			if(isset(self::$rules[$i]) && is_callable(array($controller, self::$rules[$i]), false))
			{
				$method = self::$rules[$i];
				
				if($controller->$method($uri))
				{
					$mask[] = '*';
					$args[] = $uri;
				}
				else
				{
					$mask[] = $uri;
				}
			}
			else if(class_exists($class))
			{
				$controller = new $class();
				$mask[]	= $uri;
			}
			else if ($uri)
			{
				$mask[] = $uri;
			}
		}
		
		$controller->route(join('/', $mask), $args);
		
		CC_SQL::closeConnection();
		
		define('URI', join('/', self::$uriVetor));
		
		$view = new CC_View(self::$view);
		$view->show();
	}
}
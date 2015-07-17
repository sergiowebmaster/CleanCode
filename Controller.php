<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';
require_once 'DAO.php';
require_once 'View.php';

abstract class Controller extends CleanCodeClass
{
	protected static $uri		= array();
	protected static $routes	= array();
	protected static $vars	= array();
	protected static $view;
	
	protected function initApplication()
	{
		self::$view = new View();
	}
	
	protected static function get($var, $default = '')
	{
		return self::searchPos($var, $_GET, $default);
	}
	
	public function defineUri()
	{
		$uri = preg_replace('/(\/$)|[^a-z0-9-_\/]{1,}/', '', self::get('uri'));
		self::$uri = explode('/', $uri);
		define('URI', $uri);
	}
	
	protected function setRoutes($routes)
	{
		self::$routes = $routes;
	}
	
	protected function addDbRoutes($collection, $field)
	{
		foreach ($collection as $line)
		{
			if(isset($line[$field]))
			{
				self::$routes[] = strtolower($line[$field]);
			}
			else
			{
				echo 'Campo "'.$field.'" não encontrado.';
				break;
			}
		}
	}
	
	public abstract function selectRoute($routeMask, $args);
	
	public function route()
	{
		$controller = $this;
		$mask = array();
		$args = array();
		
		foreach (self::$uri as $uri)
		{
			$class = self::toCamelCase($uri);
			
			if(class_exists($class))
			{
				$controller = new $class();
				$mask[]	= $uri;
			}
			else if(in_array($uri, self::$routes))
			{
				$mask[] = '*';
				$args[] = $uri;
			}
			else if($uri)
			{
				$mask[] = $uri;
			}
		}
		
		$controller->selectRoute(join('/', $mask), $args);
		SQL::closeConnection();
		self::$view->show();
	}
}
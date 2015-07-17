<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'Controller.php';

class FrontController extends Controller
{
	protected static $uriData	= array();
	
	function __construct($defaultController)
	{
		$this->defineUri();
		$this->selectController(new $defaultController());
	}

	private function defineUri()
	{
		$uri = preg_replace('/(\/$)|[^a-z0-9-_\/]{1,}/', '', self::get('uri'));
		self::$uriData = explode('/', $uri);
		define('URI', $uri);
	}
	
	private function selectController(Controller $controller)
	{
		$mask = array();
		$args = array();
		
		foreach (self::$uriData as $uri)
		{
			$class = self::parseVar($uri);
			
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
		
		$controller->route(join('/', $mask), $args);
		SQL::closeConnection();
		
		$view = new View(self::$data);
		$view->show();
	}
}
<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class CC_Controller extends CleanCodeClass
{
	/*
	 * @access private
	 * @var String
	 */
	private static $uri = '';
	
	/*
	 * @var array
	 */
	private static $uriVetor = array();

	/*
	 * @access protected
	 * @var array
	 */
	protected static $global = array();

	/*
	 * @access protected
	 * @var array
	 */
	protected static $view = array();

	/*
	 * @access protected
	 * @var array
	 */
	protected static $rules = array();
	
	/*
	 * Associate a function for validate and check a part of URI.
	 * @access protected
	 * @param int $index Index of URI
	 * @param String $callback Name of method for check the URI
	 * @return void
	 */
	protected function addUriRule($index, $callback)
	{
		static::$rules[$index] = $callback;
	}
	
	/*
	 * Format a URI.
	 * @access protected
	 * @param String $uri URI for to format
	 * @return String 
	 */
	protected function formatUri($uri)
	{
		return preg_replace('/(\/$)|[^a-z0-9-_\/]{1,}/', '', $uri);
	}
	
	/*
	 * Get and define the URI for controllers.
	 * @access public
	 * @return void
	 */
	public function defineUri()
	{
		$uri = $this->formatUri(self::get('uri'));
		self::$uriVetor = explode('/', $uri);
	}
	
	/* Return a $_GET data, if have.
	 * @access protected
	 * @param String $index Index of the $_GET.
	 * @param String $default Default value if don't find the index in $_GET.
	 * @return String
	 */
	protected static function get($index = '', $default = '')
	{
		return self::searchIn($_GET, $index, $default);
	}
	
	/*
	 * @access protected
	 * @param String $index Index of the $_POST.
	 * @param String $default Default value if don't find the index in $_POST.
	 * @return String
	 */
	protected static function post($index = '', $default = '')
	{
		return self::searchIn($_POST, $index, $default);
	}
	
	/*
	 * @access protected
	 * @param String $index Index of the $_FILES.
	 * @param String $default Default value if don't find the index in $_FILES.
	 * @return String
	 */
	protected static function files($index = '', $default = array())
	{
		return self::searchIn($_FILES, $index, $default);
	}

	/*
	 * @access protected
	 * @param String $name Index of the $_SESSION.
	 * @param String $default Default value if don't find the index in $_SESSION.
	 * @return String
	 */
	protected static function session($name, $default = '')
	{
		return self::searchIn($_SESSION, $name, $default);
	}
	
	/*
	 * @access protected
	 * @param String $name Index of $_SESSION.
	 * @param String $value Value of session.
	 * @return void
	 */
	protected function setSession($name, $value)
	{
		$_SESSION[$name] = addslashes($value);
	}

	/*
	 * Destroy the session.
	 * @access protected
	 * @param String $name Index of $_SESSION.
	 * @return void
	 */
	protected function unsetSession($name)
	{
		unset($_SESSION[$name]);
	}
	
	/*
	 * Redirect to a path.
	 * @param String $path Destination path.
	 * @access protected
	 * @return void
	 */
	protected function redirect($path)
	{
		header('Location:'.$path);
	}
	
	/*
	 * Select the internal route, based in route mask.
	 * @param String $routeMask Route mask with * where is variable.
	 * @param array $args Values of *.
	 * @return void
	 */
	public function route($routeMask, $args)
	{
		echo 'Nenhuma rota definida!';
	}
	
	public function checkRule($index, $uri)
	{
		if(isset(static::$rules[$index]))
		{
			$method = static::$rules[$index];
			return $this->$method($uri);
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * Defines the way of controllers and methods, based in URI.
	 * @return void
	 */
	public function selectRoute()
	{
		$controller = $this;
		$mask = array();
		$args = array();
		
		foreach (self::$uriVetor as $uri)
		{
			$class = 'Controller' . self::parseVar($uri);
			
			if(method_exists($controller, $uri))
			{
				$mask[] = $uri;
			}
			else if(class_exists($class))
			{
				$controller = new $class();
				$mask[] = $uri;
			}
			else if ($uri)
			{
				$mask[] = '*';
				$args[] = $uri;
			}
		}
		
		$controller->route(join('/', $mask), $args);
		
		CC_SQL::closeConnection();
		
		define('URI', join('/', self::$uriVetor));
		
		$view = new CC_View(self::$view);
		$view->show();
	}
}
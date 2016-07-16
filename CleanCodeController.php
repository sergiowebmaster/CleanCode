<?php
session_start();

require_once 'CleanCodeClass.php';
require_once 'CleanCodeView.php';

abstract class CleanCodeController extends CleanCodeClass
{
	/*
	 * Array with the parts of requested URI.
	 * @var array
	 */
	private static $uri = array();
	
	/*
	 * Array data for the view.
	 * @var array
	 */
	protected static $data = array();
	
	/*
	 * Get the requested URI in the server.
	 * @access protected
	 * @return String
	 */
	protected static function getRequestUri()
	{
		return $_SERVER['REQUEST_URI'];
	}
	
	/*
	 * Get the hostname of the server.
	 * @access protected
	 * @return String
	 */
	protected static function getHostname()
	{
		return $_SERVER['HTTP_HOST'];
	}
	
	/*
	 * Read the URI requested by the user.
	 * @access protected
	 * @return void
	 */
	public function readUri()
	{
		define('URI', preg_replace('/(\/$)|(^[^a-z0-9-_\/]{1,}$)/', '', self::get('uri')));
		self::$uri = explode('/', URI);
	}
	
	/*
	 * Clear the URI info.
	 * @access protected
	 * @return void
	 */
	protected function clearUri()
	{
		self::$uri = array();
	}
	
	/*
	 * Redirect for a new path.
	 * @access protected
	 * @param String $path The address for to redirect.
	 * @return void
	 */
	protected function redirect($path)
	{
		header('Location:' . $path);
	}

	/*
	 * Back to the previows directory.
	 * @access protected
	 * @return void
	 */
	protected function back()
	{
		$this->redirect('./');
	}

	/*
	 * Reload the page.
	 * @access protected
	 * @return void
	 */
	protected function refresh()
	{
		$this->redirect(self::getRequestUri());
	}
	
	/*
	 * Get the value of a GET variable.
	 * @access protected
	 * @param String $var The name of the GET variable.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function get($var = '', $default = '')
	{
		return $var? self::searchPos($_GET, $var, $default) : $_GET;
	}

	/*
	 * Get the value of a POST variable.
	 * @access protected
	 * @param String $var The name of the POST variable.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function post($var = '', $default = '')
	{
		return $var? self::searchPos($_POST, $var, $default) : $_POST;
	}

	/*
	 * Get the value of a FILES variable.
	 * @access protected
	 * @param String $var The name of the FILES variable.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function files($var = '', $default = '')
	{
		return $var? self::searchPos($_FILES, $var, $default) : $_FILES;
	}

	/*
	 * Get the value of a session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function get_session($name, $default = '')
	{
		return self::searchPos($_SESSION, $name, $default);
	}

	/*
	 * Set the value of a session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @param String $value The value of the session.
	 * @return void
	 */
	protected function set_session($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	/*
	 * Destroy a session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @return void
	 */
	protected function unset_session($name)
	{
		unset($_SESSION[$name]);
	}

	/*
	 * Check a condition and clear the URI, if this condition isn't true.
	 * @access protected
	 * @param String $condition The condition for to continue the routing.
	 * @return void
	 */
	protected function check($condition)
	{
		if(!$condition) self::$uri = array();
	}

	/*
	 * Check a session and clear the URI, if the session is empty or not found.
	 * @access protected
	 * @param String $sessionName The name of the session.
	 * @return void
	 */
	protected function checkSession($sessionName)
	{
		$this->check($this->get_session($sessionName));
	}

	/*
	 * Shift the next part of the URI.
	 * @access protected
	 * @return String
	 */
	protected function cropSlug()
	{
		return self::$uri? array_shift(self::$uri) : '';
	}
}
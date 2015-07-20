<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class Controller extends CleanCodeClass
{
	protected static $data = array();
	protected static $routes = array();
	
	public function route($routeMask, $args)
	{
		echo 'Nenhuma rota definida!';
	}
	
	protected static function get($var, $default = '')
	{
		return self::searchIn($_GET, $var, $default);
	}
	
	protected static function post($var, $default = '')
	{
		return self::searchIn($_POST, $var, $default);
	}
}
<?php
require_once 'CleanCodeClass.php';
require_once 'CleanCodeDir.php';

class CleanCodeAutoload extends CleanCodeClass
{
	/*
	 * Contains the paths of classes.
	 * @var array
	 */
	private $paths = array('controllers', 'models');
	
	function __construct()
	{
		$this->addPath(__DIR__ . '/');
		spl_autoload_register(array($this, 'loadClass'));
	}
	
	/*
	 * Start the autoload and register controllers and models folders.
	 * @access public
	 * @return void
	 */
	public static function init()
	{
		$instance = new self();
	}
	
	/*
	 * Search the class in folders and instance if is found.
	 * @access private
	 * @param String $className Name of class.
	 * @return void
	 */
	private function loadClass($className)
	{
		for ($i=0; $i < count($this->paths); $i++)
		{
			$path = CleanCodeDir::translate($this->paths[$i]) . $className . '.php';
			
			if(file_exists($path))
			{
				include $path;
				break;
			}
		}
	}
	
	/*
	 * Add the path into internal list for seach classes.
	 * @access public
	 * @param String $path The path with classes for including.
	 * @return void
	 */
	public function addPath($path)
	{
		if(is_dir($path))
		{
			$this->paths[] = $path;
		}
		else
		{
			die('Diretório ' . $path . ' não encontrado!');
		}
	}
	
	/*
	 * Add the path and internal folders in pathes list for autoload.
	 * @access public
	 * @param String $path The root path of the library.
	 * @return void
	 */
	public function addLibrary($path)
	{
		$GLOBALS['instance'] = $this;
		
		$dir = new CleanCodeDir($path);
		$dir->scan(function($item)
		{
			$GLOBALS['instance']->addPath($item . '/');
		},
		true);
	}
}
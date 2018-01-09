<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class CleanCodeDir extends CleanCodeClass
{
	private static $aliases = array(
			'controllers' => 'controllers/',
			'models' => 'models/',
			'jquery' => 'https://code.jquery.com/jquery-3.2.1.slim.min.js',
			'bootstrap' => 'http://getbootstrap.com/dist/'
	);
	
	private $path = '';
	private $subFolders = array();
	
	public static function format($string)
	{
		return self::format_url($string, false);
	}
	
	public static function addAlias($alias, $path)
	{
		self::$aliases[$alias] = $path;
	}
	
	public static function searchAliasPath($alias)
	{
		return self::searchPos(self::$aliases, $alias, $alias);
	}
	
	public static function translate($alias)
	{
		if (preg_match('/^(http:|https:)/', $alias))
		{
			return $alias;
		}
		else
		{
			$parts = explode(':', $alias);
			return count($parts) == 2? self::searchAliasPath($parts[0]) . $parts[1] : $alias;
		}
	}
	
	public static function img($filename)
	{
		return self::translate("img:$filename");
	}
	
	public static function css($filename)
	{
		return self::translate("css:$filename");
	}
	
	public static function js($filename)
	{
		return self::translate("js:$filename");
	}
	
	function __construct($path)
	{
		$this->path = $path;
	}
	
	public function addSubFolders($folder)
	{
		$this->subFolders = func_get_args();
	}
	
	private function createSubFolders()
	{
		$ok = true;
		
		foreach ($this->subFolders as $folder)
		{
			$dir = new self($this->getPath() . $folder);
			
			if(!$dir->create()) $ok = false;
		}
		
		return $ok;
	}
	
	private function recursive_scan($dir, $funct, $only_dir)
	{
		if(is_dir($dir))
		{
			$ret = true;
		
			foreach(scandir($dir) as $content)
			{
				if(!preg_match('/^(\.{1,})$/', $content))
				{
					$path = $dir . '/' . $content;
					
					if(is_dir($path))
					{
						$this->recursive_scan($path, $funct, $only_dir);
					}
					else if($only_dir == false)
					{
						$ret = $funct($path, false);
					}
				}
			}
		
			return $ret && $funct($dir, true);
		}
		else return false;
	}
	
	public function scan($funct, $only_dir = false)
	{
		return $this->recursive_scan($this->path, $funct, $only_dir);
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function setPath($new_path)
	{
		if(rename($this->path, $new_path)) $this->path = $new_path;
	}
	
	public function rename_folder($oldFolder, $newFolder)
	{
		return $oldFolder == $newFolder || rename($this->path . '/' . $oldFolder, $this->path . '/' . $newFolder);
	}
	
	public function create($chmod = 0777)
	{
		return mkdir($this->path, $chmod, true) && (count($this->subFolders) == 0 || $this->createSubFolders());
	}
	
	public function delete()
	{
		return $this->scan(function($item, $isDir)
		{
			return $isDir? rmdir($item) : unlink($item);
		});
	}
}
?>
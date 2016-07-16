<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class CleanCodeDir extends CleanCodeClass
{
	private static $aliases = array(
			'controllers' => 'controllers/',
			'models' => 'models/'
	);
	
	private $path = '';
	private $sub_folders = array();
	
	public static function format($string)
	{
		return self::format_url($string, false);
	}
	
	public static function addAlias($alias, $path)
	{
		self::$aliases[$alias] = self::format($path);
	}
	
	public static function translate($alias)
	{
		if (strstr(':', $alias))
		{
			$parts = explode(':', $alias); echo $parts[0] . '<br>';
			return self::searchPos(self::$aliases, $parts[0]) . $parts[1];
		}
		else if (isset(self::$aliases[$alias]))
		{
			return self::$aliases[$alias];
		}
		else
		{
			return $alias;
		}
	}
	
	function __construct($path)
	{
		$this->path = $path;
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
		return mkdir($this->path, $chmod, true);
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
<?php
class CC_Language
{
	private $lang = '';
	private $path = '';
	
	function __construct($lang)
	{
		$path = CC_Dir::getPath('lang:'.$lang.'/');
		
		if(is_dir($path))
		{
			$this->lang = $lang;
			$this->path = $path . '/';
		}
	}
	
	public function getPath()
	{
		return $this->path;
	}
	
	public function load($prefix)
	{
		$path = $this->path . $prefix . '_lang.php';
		
		if(file_exists($path))
		{
			require_once $path;
			
			foreach ($lang as $var => $value)
			{
				define('LANG_' . strtoupper($var), $value);
			}
		}
	}
}
?>
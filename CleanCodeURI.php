<?php
class CleanCodeURI extends CleanCodeClass
{
	private $uri = '';
	private $slugs = array();
	private $index = 0;
	
	function __construct($uri)
	{
		$this->set($uri);
		$this->defineSlugs();
	}
	
	public static function parseArray($array)
	{
	    return join('/', $array);
	}
	
	public static function format($uri)
	{
	    return preg_replace('/\/{1,}$/', '', $uri);
	}
	
	private function set($uri)
	{
	    $this->uri = self::format($uri);
	}
	
	private function defineSlugs()
	{
	    $this->slugs = explode('/', $this->uri);
	}
	
	public function getSlugs()
	{
	    return $this->slugs;
	}
	
	public function getSlug($index, $default = '')
	{
	    return self::searchPos($this->slugs, $index, $default);
	}
	
	public function nextSlug($default = '')
	{
		return $this->getSlug($this->index++, $default);
	}
	
	public function getSize()
	{
		return count($this->slugs);
	}
	
	public function getBack()
	{
	    return self::parseArray(array_slice($this->slugs, 0, -1));
	}
	
	public function toString()
	{
	    return $this->uri;
	}
}
?>
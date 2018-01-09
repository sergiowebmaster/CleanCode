<?php
class CleanCodeURI extends CleanCodeClass
{
	private $uri = '';
	private $slugs = array();
	private $last = array();
	private $pageNumber = 1;
	
	function __construct($uri)
	{
		$this->uri = preg_replace('/(\/$)|(^[^a-z0-9-_\/]{1,}$)/', '', $uri);
		$this->slugs = explode('/', $this->uri);
	}
	
	private function implodeSlugs($slugs)
	{
		return implode('/', $slugs);
	}
	
	public function nextSlug($default = '')
	{
		if(count($this->slugs))
		{
			$this->last[] = array_shift($this->slugs);
			return end($this->last);
		}
		else return $default;
	}
	
	public function getPageNumber($default = 1)
	{
		return is_numeric(end($this->slugs))? array_pop($this->slugs) : $default;
	}
	
	public function getLastSlugs()
	{
		return $this->last;
	}
	
	public function getLast()
	{
		return $this->implodeSlugs($this->last);
	}
	
	public function getBack()
	{
		return preg_replace('/(.*)(\/[\W\w]{1,}$)/', '$1', $this->uri);
	}
	
	public function getSize()
	{
		return count($this->getLastSlugs());
	}
	
	public function toString()
	{
		return $this->uri;
	}
}
?>
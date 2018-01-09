<?php
require_once 'CleanCodeDefaultDAO.php';

class CleanCodeDaoUri extends CleanCodeDefaultDAO
{
	public function getUri()
	{
		return $this->get_column('uri');
	}
	
	public function setUri($uri)
	{
		$this->set_uri_column('uri', $uri);
	}
	
	protected function formatUri($string)
	{
		$this->setUri(CleanCodeDir::format($string));
	}
	
	public static function checkURI($uri)
	{
		$obj = new static();
		$obj->setUri($uri);
		
		return $obj->loadFromDB();
	}
}
?>
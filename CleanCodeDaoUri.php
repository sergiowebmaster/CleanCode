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
		$this->set_uri_column($uri);
	}
	
	public function loadByUri($uri)
	{
		$this->setUri($uri);
		return $this->loadFromDB();
	}
}
?>
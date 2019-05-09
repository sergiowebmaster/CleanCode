<?php
require_once 'CleanCodeModelDAO.php';

class CleanCodeDefaultDAO extends CleanCodeModelDAO
{
	public function getID()
	{
		return $this->get_column('id', 0);
	}
	
	public function setID($id)
	{
		$this->set_column('id', $id, self::NUM);
	}
}
?>
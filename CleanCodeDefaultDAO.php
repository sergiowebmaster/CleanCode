<?php
require_once 'CleanCodeDAO.php';

class CleanCodeDefaultDAO extends CleanCodeDAO
{
	public static function listByID($desc = false)
	{
		return self::fetchAllBy('id', $desc);
	}
	
	public function getID()
	{
		return $this->get_column('id', 0);
	}
	
	public function setID($id)
	{
		$this->set_column('id', $id, self::NUM);
		$this->memorize();
	}
	
	public function filterByID()
	{
		return $this->fetchBy('id');
	}
	
	public function getLastByID()
	{
		return $this->fetchLastBy('id');
	}
	
	protected function insertIntoDB()
	{
		if(parent::insertIntoDB())
		{
			$this->setID(self::$sql->lastInsertId());
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>
<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'DAO.php';

class User extends DAO
{
	protected static $table = 'users';

	public function setPassword($password, $field = 'password')
	{
		if($this->validate($password, self::PWD))
		{
			$this->data[$field] = md5($password);
		}
		else
		{
			$this->setErrorByField($field);
		}
	}
}
?>
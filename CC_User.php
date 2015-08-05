<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CC_DAO.php';

class CC_User extends CC_DAO
{
	protected static $table = 'users';

	protected function setPwdField($password, $fieldName = 'password')
	{
		if($this->validate($password, self::PWD, 6, 10))
		{
			$this->data[$fieldName] = md5($password);
		}
		else
		{
			$this->setError('Preencha corretamente o campo "'.$fieldName.'"!');
		}
	}
}
?>
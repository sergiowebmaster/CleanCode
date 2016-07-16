<?php
require_once 'CleanCodeDAO.php';

class CleanCodeUser extends CleanCodeDAO
{
	protected static $table = 'users';
	
	public function getLogin()
	{
		return $this->get_column('login');
	}
	
	public function setLogin($login)
	{
		$this->set_column('login', $login, self::LOGIN, 3, 20);
	}
	
	protected function getPassword()
	{
		return $this->get_column('password');
	}
	
	protected function setPassword($password)
	{
		$this->set_column('password', $password, self::PWD);
	}
	
	public function changePassword($currentPassword, $newPassword, $confirm)
	{
		if($currentPassword != $this->getPassword())
		{
			$this->setError('Senha atual incorreta!');
			return false;
		}
		else if ($newPassword != $confirm)
		{
			$this->setError('A nova senha e a confirmação devem ser iguais!');
			return false;
		}
		else
		{
			$this->setPassword($newPassword);
			return $this->update();
		}
	}
	
	public function authenticate($login, $password)
	{
		$this->setLogin($login);
		$this->setPassword($password);
		
		return $this->execute($this->loadFromDB(), 'Fazendo login...', 'Dados de acesso incorretos.');
	}
}
?>
<?php
require_once 'CleanCodeDefaultDAO.php';

class CleanCodeUser extends CleanCodeDefaultDAO
{
	protected static $table = 'users';
	
	public static $loginSuccess = 'Autenticando...';
	public static $loginError = 'Dados de acesso incorretos!';
	public static $confirmError = 'A confirmação não confere!';
	public static $currentPwdError = 'Senha atual incorreta!';
	
	public $sessionName = 'user';
	
	public function getSession()
	{
		return $this->getID();
	}
	
	protected function setSession($sessionValue)
	{
		$this->setID($sessionValue);
	}
	
	private function searchInSession($session)
	{
		return self::searchPos($session, $this->sessionName, 0);
	}
	
	public function setBySession($session)
	{
		$this->setSession($this->searchInSession($session));
	}
	
	public function getEmail()
	{
		return $this->get_column('email');
	}
	
	public function setEmail($email)
	{
		$this->set_column('email', $email, self::EMAIL);
	}
	
	protected function getPassword()
	{
		return $this->get_column('password');
	}
	
	protected function set_pwd_column($name, $password)
	{
		$this->set_column($name, $password, self::PWD);
	}
	
	public function setPassword($password)
	{
		$this->set_pwd_column('password', $password);
	}
	
	protected function checkCurrentPassword($password)
	{
		return $this->execute(md5($password) == $this->getPassword(), '', self::$currentPwdError);
	}
	
	public function confirmPassword($password, $confirm)
	{
		if ($password == $confirm)
		{
			$this->setPassword($password);
			return true;
		}
		else
		{
			$this->setError(self::$confirmError);
			return false;
		}
	}
	
	public function changePassword($currentPassword, $newPassword, $confirm)
	{
		return $this->checkCurrentPassword($currentPassword) && $this->confirmPassword($newPassword, $confirm) && $this->execute($this->update(), 'Senha alterada com sucesso!');
	}
	
	public function loadForAuth()
	{
		return $this->execute($this->loadFromDB(), self::$loginSuccess, self::$loginError);
	}
}
?>
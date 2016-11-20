<?php
require_once 'CleanCodeDefaultDAO.php';

class CleanCodeUser extends CleanCodeDefaultDAO
{
	protected static $table = 'users';
	
	private static $pwdField = 'password';
	
	public static function setPwdField($fieldName)
	{
		self::$pwdField = $fieldName;
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
		return $this->get_column(self::$pwdField);
	}
	
	public function setPassword($password)
	{
		$this->set_column(self::$pwdField, $password, self::PWD);
	}
	
	public function confirmPassword($password, $confirm)
	{
		if ($password != $confirm)
		{
			$this->setError('A nova senha e a confirmação devem ser iguais!');
		}
		else
		{
			$this->setPassword($password);
		}
	}
	
	public function changePassword($currentPassword, $newPassword, $confirm)
	{
		if(md5($currentPassword) != $this->getPassword())
		{
			$this->setError('Senha atual incorreta!');
			return false;
		}
		else if ($this->confirmPassword($newPassword, $confirm))
		{
			return $this->update();
		}
	}
}
?>
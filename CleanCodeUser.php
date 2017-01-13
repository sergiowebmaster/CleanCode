<?php
require_once 'CleanCodeDefaultDAO.php';

class CleanCodeUser extends CleanCodeDefaultDAO
{
	protected static $table = 'users';
	
	public static $means = array(
			'Redirecting...',
			'Login failed!',
			'Incorrect password!',
			'Password confirmation doesn\'t match Password',
			'The current password is incorrect!'
	);
	
	private static $pwdField = 'password';
	
	public $sessionName = 'user';
	
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
	
	protected function checkCurrentPassword($password)
	{
		return $this->execute(md5($password) == $this->getPassword(), '', 'Senha atual incorreta!');
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
			$this->setError('A confirmação não confere!');
			return false;
		}
	}
	
	public function changePassword($currentPassword, $newPassword, $confirm)
	{
		return $this->checkCurrentPassword($currentPassword) && $this->confirmPassword($newPassword, $confirm) && $this->execute($this->update(), 'Senha alterada com sucesso!');
	}
	
	public function auth()
	{
		return $this->execute($this->loadFromDB(), self::getMeans(0), self::getMeans(1));
	}
}
?>
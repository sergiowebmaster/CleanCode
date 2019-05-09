<?php
require_once 'CleanCodeModelDAO.php';

class CleanCodeUser extends CleanCodeModelDAO
{
    public $sessionName = '';
    
	protected function set_password_column($name, $password)
	{
		$this->set_column($name, $password, self::PASSWORD);
	}
}
?>
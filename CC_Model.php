<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class CC_Model extends CleanCodeClass
{
	private $error = '';
	
	protected $action = '';
	
	const ALL	 	= '/\w{min,max}/i';
	const NAME	 	= '/^([aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,})+( [aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,}){0,}$/i';
	const FULLNAME = '/^([aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,})+( [aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,}){1,}$/i';
	const LOGIN 	= '/^\w{min,max}$/';
	const URI 	 	= '/^[a-z0-9\-\/_]{min,max}$/';
	const URL	 	= '/^(http|https)+(:\/\/)+(www\.|)+([a-z0-9\.]{5,})$/';
	const EMAIL	 	= '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
	const DATE  	= '/^\d{2}\/\d{2}\/\d{4}$/';
	const PWD	 	= '/^[a-z0-9]{min,max}$/';
	const NUM	 	= '/^\d{min,max}$/';
	const FILE	 	= '/^([\w_\-]{min,max})+(\.\w{2,5})$/';
	
	public function getError()
	{
		return $this->error;
	}
	
	protected function setError($errorMessage)
	{
		if($this->error == '')
		{
			$this->error = $errorMessage;
		}
	}
	
	protected static function validate($value, $regex, $min = 1, $max = '')
	{
		return preg_match(str_replace('{min,max}', '{'.$min.','.$max.'}', $regex), addslashes($value));
	}
	
	public function load($data)
	{
		foreach ($data as $field => $value)
		{
			$method = 'set' . self::parseVar($field);
			
			if(method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
	}
	
	public function loadByForm()
	{
		$this->load($_POST);
		$this->load($_FILES);
	}
	
	protected function selectAction($action)
	{
		echo 'Nenhuma implementação para "'.$action.'"!';
	}
	
	protected function setAction($action)
	{
		$this->action = addslashes($action);
	}
	
	public function doAction()
	{
		if($this->error == '')
		{
			$this->selectAction($this->action);
		}
	}
}
?>
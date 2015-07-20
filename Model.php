<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeClass.php';

class Model extends CleanCodeClass
{
	private $error = '';
	
	protected $action = '';
	
	const ALL	 	= '/\w{min,max}/i';
	const NAME	 	= '/^([aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,})+( [aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,}){0,}$/i';
	const FULLNAME = '/^([aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,})+( [aáàâãbcçdeéêfghiíjklmnoóôõöpqrstuúüvwxyz]{2,}){1,}$/i';
	const LOGIN 	= '/^\w{1,}$/';
	const URI 	 	= '/^[a-z0-9\-\/_]{1,}$/';
	const URL	 	= '/^(http|https)+(:\/\/)+(www\.|)+([a-z0-9\.]{5,})$/';
	const EMAIL	 	= '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
	const DATE  	= '/^\d{2}\/\d{2}\/\d{4}$/';
	const PWD	 	= '/^[a-z0-9#@!?\.]{6,10}$/';
	const NUM	 	= '/^\d{min,max}$/';
	const FILE	 	= '/^([\w_\-]{1,})+(\.\w{2,5})$/';
	
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
	
	protected static function validate($value, $regex, $min = 0, $max = '')
	{
		return preg_match(str_replace('{min,max}', '{'.$min.','.$max.'}', $regex), $value);
	}
	
	protected function setAction($action)
	{
		$this->action = addslashes($action);
	}
	
	public function load($data)
	{
		foreach ($data as $field => $value)
		{
			$setter = 'set'.ucfirst($field);
			
			if(method_exists($this, $setter))
			{
				$this->$setter(addslashes($value));
			}
		}
	}
	
	public function getData()
	{
		$data = array();
		
		foreach ($this as $field => $value)
		{
			$data[$field] = $value;
		}
		
		return $data;
	}
	
	protected function selectAction($action)
	{
		echo 'Nenhuma implementação para "'.$action.'"!';
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
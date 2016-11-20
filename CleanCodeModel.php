<?php
require_once 'CleanCodeClass.php';

class CleanCodeModel extends CleanCodeClass
{
	const ALL	 	= '/^[\w\W]{min,max}$/i';
	const HTML		= '/[<>]{0,}/';
	const NAME	 	= '/^[a-záàâãçéèêëíìóôõöúüÁÀÂÃÇÉÈÊËÍÌÓÒÔÕÖÚÙÜ ]{min,max}$/i';
	const LOGIN 	= '/^[a-z0-9\-_\.]{min,max}$/';
	const URI 	 	= '/^[a-z0-9\-\/_]{min,max}$/i';
	const URL	 	= '/^(http|https)+(:\/\/)+(www\.|)+([a-z0-9\.]{5,})$/';
	const EMAIL	 	= '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
	const DATE  	= '/^\d{4}\-\d{1,2}\-\d{1,2}$/';
	const TIME		= '/^\d{1,2}\:\d{1,2}/';
	const FULLDATE = '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}$/';
	const PWD	 	= '/^[\w\@\$\#]{min,max}$/';
	const NUM	 	= '/^\d{min,max}$/';
	const DOUBLE	= '/^[\d\.]{1,}$/';
	const FILE	 	= '/^([\w_\-\.]{min,max})+(\.\w{2,5})$/';
	const CPF		= '/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/';
	const TEL_BR	= '/^\(\d{2}\) \d{4,5}\-\d{4}$/';
	const GENDER	= '/^[FM]$/i';
	const IP		= '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
	const BOOLEAN	= '/^[0-1]$/';
	
	protected $error = '';
	protected $success = '';
	
	public static function formatRegex($regex, $min, $max = '')
	{
		return str_replace('{min,max}', '{'.$min.','.$max.'}', $regex);
	}
	
	protected function parseURI($string)
	{
		$string = htmlentities($string);
		$invalid = array('/\s/', '/acute;/', '/cedil;/', '/grave;/', '/circ;/', '/tilde;/', '/[^\-\_\w]/');
		return preg_replace($invalid, array('_', ''), strtolower($string));
	}

	protected function validate($value, $regex, $min = 1, $max = '')
	{
		return $min === 0 || preg_match(self::formatRegex($regex, $min, $max), addslashes($value));
	}
	
	protected function formatData($value, $regex)
	{
		switch ($regex)
		{
			case self::PWD:
				return md5($value);
				break;
			
			case self::LOGIN:
			case self::EMAIL:
				return strtolower($value);
				break;
				
			case self::NAME:
				return ucwords(strtolower($value));
				break;
				
			default: return $value;
		}
	}
	
	public function check()
	{
		return $this->error == '';
	}
	
	public function getSuccess()
	{
		return $this->success;
	}
	
	public function setSuccess($message)
	{
		if($this->success == '' && $this->check()) $this->success = $message;
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	public function setError($message)
	{
		if($this->check()) $this->error = $message;
	}
	
	protected function setErrorByField($fieldError)
	{
		$this->setError('Invalid ' . $fieldError . '!');
	}
	
	public function load($data)
	{
		if($data && is_array($data))
		{
			foreach ($data as $field => $value)
			{
				$method = 'set' . self::toCamelCase($field);
					
				if(method_exists($this, $method))
				{
					$this->$method($value);
				}
			}
		}
	}
	
	protected function execute($operation, $successMessage, $errorMessage)
	{
		if($operation)
		{
			$this->setSuccess($successMessage);
			return true;
		}
		else
		{
			$this->setError($errorMessage);
			return false;
		}
	}
}
?>
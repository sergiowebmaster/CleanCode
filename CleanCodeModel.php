<?php
require_once 'CleanCodeClass.php';

class CleanCodeModel extends CleanCodeClass
{
	const ALL	 	= '/^[\w\W]{min,max}$/i';
	const HTML		= '/[<>\w]{min,max}/';
	const NAME	 	= '/^[a-záàâãçéèêëíìóôõöúüÁÀÂÃÇÉÈÊËÍÌÓÒÔÕÖÚÙÜ ]{min,max}$/i';
	const LOGIN 	= '/^[a-z0-9\-_\.]{min,max}$/';
	const URI 	 	= '/^[a-z0-9\-\/_]{min,max}$/i';
	const URL	 	= '/^(http|https)+(:\/\/)+(www\.|)+([\w\.\/\?\=\-\_]{min,max})$/';
	const EMAIL	 	= '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i';
	const DATE  	= '/^\d{4}\-\d{1,2}\-\d{1,2}$/';
	const TIME		= '/^\d{1,2}\:\d{1,2}/';
	const FULLDATE = '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}$/';
	const PWD	 	= '/^[\w\@\$\#]{min,max}$/';
	const NUM	 	= '/^\d{min,max}$/';
	const DOUBLE	= '/^[\d\.]{1,}$/';
	const FILE	 	= '/^([\w_\-\.]{min,max})+(\.\w{2,5})$/';
	const CPF		= '/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/';
	const CNPJ		= '/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/';
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
	
	protected function formatData($value, $regex)
	{
		switch ($regex)
		{
			case self::NUM:
				return (int) $value;
				break;
				
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
				
			case self::HTML:
				return preg_replace('/^<\?.*\?>$/', '', $value);
				break;
		
			case self::BOOLEAN:
				return $value? 1:0;
				break;
				
			default:
				return strip_tags($value);
		}
	}
	
	protected function validateByRegex($value, $regex, $min = 1, $max = '')
	{
		return ($min === 0 && strlen($value) == 0) || preg_match(self::formatRegex($regex, $min, $max), addslashes($value));
	}
	
	protected function validateRange($number, $min, $max)
	{
		return $number >= $min && $number <= $max;
	}

	protected function validate($value, $regex, $min = 1, $max = '')
	{
		switch ($regex)
		{
			case self::DATE:
				$dt = explode('-', $value);
				return strlen($dt[0]) == 4 && $this->validateRange($dt[1], 1, 12) && $this->validateRange($dt[2], 1, 31) && $this->validateByRegex($value, $regex, $min, $max);
				break;
				
			default:
				return $this->validateByRegex($value, $regex, $min, $max);
		}
	}
	
	public function check()
	{
		return $this->error == '';
	}
	
	public function getSuccess()
	{
		return $this->error? '' : $this->success;
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
		$this->setError(self::msg('validation_field_error', $fieldError));
	}
	
	protected function checkError()
	{
		return $this->getError() == '';
	}
	
	protected static function generateSetter($field)
	{
		return 'set' . self::toCamelCase($field);
	}
	
	protected function checkMethod($field, $value)
	{
		$method = self::generateSetter($field);
			
		if(method_exists($this, $method))
		{
			$this->$method($value);
		}
	}
	
	public function load($data)
	{
		if($data && is_array($data))
		{
			foreach ($data as $field => $value)
			{
				$this->checkMethod($field, $value);
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
	
	protected function prepareFile($name, $tmpName, $type, $size)
	{
		print_r(func_get_args());
	}
	
	protected function uploadFiles($files)
	{
		CleanCodeFile::prepareMultiple($files, function($name, $tmpName, $type, $size)
		{
			static::prepareFile($name, $tmpName, $type, $size);
		});
	}
}
?>
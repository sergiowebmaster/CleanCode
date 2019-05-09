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
	const FULLDATE  = '/^\d{4}\-\d{2}\-\d{2} \d{2}\:\d{2}/';
	const PASSWORD	= '/^[\w\@\$\#]{min,max}$/';
	const NUM	 	= '/^\d{min,max}$/';
	const DOUBLE	= '/^[\d\.]{min,max}$/';
	const FILE	 	= '/^([\w_\-\.]{min,max})+(\.\w{2,5})$/';
	const CPF		= '/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/';
	const CNPJ		= '/^\d{2}\.\d{3}\.\d{3}\/\d{4}\-\d{2}$/';
	const TEL_BR	= '/^\(\d{2}\) \d{4,5}\-\d{4}$/';
	const GENDER	= '/^[FM]$/i';
	const IPv4		= '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/';
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
	
	private function formatValueByRegex($value, $regex)
	{
	    switch ($regex)
	    {
	        case self::NUM:
	            return (int) $value;
	            break;
	            
	        case self::PASSWORD:
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
	            
	        default:
	            return $value;
	    }
	}
	
	protected function formatValue($value, $regex)
	{
		return $this->formatValueByRegex($regex == self::HTML? $value : strip_tags($value), $regex);
	}
	
	protected function validateData($value, $regex, $min = 1, $max = '')
	{
		return preg_match(self::formatRegex($regex, $min, $max), $value);
	}
	
	protected function validateRange($number, $min, $max)
	{
		return $number >= $min && $number <= $max;
	}
	
	public function checkErrors()
	{
		return $this->error == '';
	}
	
	public function getError()
	{
		return $this->error;
	}
	
	public function setError($message)
	{
		$this->error = $message;
	}
	
	protected function setErrorByField($name)
	{
	    $this->setError("Invalid $name!");
	}
	
	public function getSuccess()
	{
	    return $this->success;
	}
	
	public function setSuccess($message)
	{
	    $this->success = $message;
	}
	
	protected function execute($operation, $successMessage, $errorMessage)
	{
	    if($operation)
	    {
	        $this->setSuccess($successMessage);
	        return true;
	    }
	    else if($this->checkErrors())
	    {
	        $this->setError($errorMessage);
	        return false;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	public static function parseFloat($number)
	{
	    return str_replace(array('.', ','), array('', '.'), $number);
	}
	
	public static function parseMoney($number)
	{
	    return number_format($number, 2, ',', '.');
	}
	
	public static function parseNumber($string)
	{
	    return preg_replace('/\D/', '', $string);
	}
}
?>
<?php
class CleanCodeClass
{
    private static $debugMode = false;
    
	protected static function searchPos($array, $pos, $default = '')
	{
		return isset($array[$pos]) && $array[$pos]? $array[$pos] : $default;
	}
	
	protected static function format_url($string, $is_file)
	{
		$search = array('/á|à|ã|â|ä|Á|À|Ã|Â|Ä/', '/é|è|ê|ẽ|ë|É|È|Ê|Ẽ|Ë/', '/í|ì|ĩ|î|ï|Í|Ì|Î|Ĩ|Ï/', '/ó|ò|ô|õ|ö|Ó|Ò|Õ|Ô|Ö/', '/ú|ù|û|ũ|ü|Ú|Ù|Ũ|Û|Ü/', '/ç/', '/\s/', '/[^\w\_\-\/'.($is_file? '\.' : '').']/', '/\/{2,}/');
		$replace = array('a', 'e', 'i', 'o', 'u', 'c', '_', '', '/');
		
		return preg_replace($search, $replace, strtolower($string));
	}
	
	protected static function toCamelCase($uri)
	{
	    $parts	= explode('_', self::format_url($uri, false));
	    $result = array_shift($parts);
	    
	    foreach($parts as $part)
	    {
	        $result .= ucfirst($part);
	    }
	    
	    return $result;
	}
	
	protected static function generateHash($size)
	{
	    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	    $string = '';
	    
	    for ($i = 0; $i < $size; $i++)
	    {
	        $c = rand(0, strlen($characters) - 1);
	        $string .= $characters[$c];
	    }
	    
	    return $string;
	}
	
	protected static function fill_zero($number, $qty, $left = true)
	{
	    return str_pad($number, $qty, 0, $left? STR_PAD_LEFT : STR_PAD_RIGHT);
	}
	
	protected static function num($string)
	{
	    return preg_replace('/\D/', '', $string);
	}
	
	protected static function toMoney($float)
	{
	    return number_format($float, 2, ',', '.');
	}
	
	protected function getIncludePath()
	{
	    return ini_get('include_path');
	}
	
	protected function setIncludePath($path)
	{
	    ini_set('include_path', $path);
	}
	
	protected function activeDebugMode()
	{
	    self::$debugMode = true;
	}
	
	protected function debug()
	{
	    echo 'Debug ' . get_called_class();
	}
	
	function __destruct()
	{
		if(self::$debugMode) $this->debug();
	}
}
?>
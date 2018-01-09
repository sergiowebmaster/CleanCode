<?php
class CleanCodeHelper extends CleanCodeClass
{
	public static function generateHash($size)
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
	
	public static function fill_zero($number, $qty, $left = true)
	{
		return str_pad($number, $qty, 0, $left? STR_PAD_LEFT : STR_PAD_RIGHT);
	}
	
	public static function num($string)
	{
		return preg_replace('/\D/', '', $string);
	}
	
	public static function toMoney($float)
	{
		return number_format($float, 2, ',', '.');
	}
}
?>
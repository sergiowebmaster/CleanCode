<?php
require_once('CleanCodeClass.php');

class CleanCodeInputMarker extends CleanCodeClass
{
	private $needle = '';
	
	function __construct($needle)
	{
		$this->needle = $needle;
	}
	
	private function compareValue($value)
	{
		return $value == $this->needle;
	}
	
	public function parseAttribute($attr, $value)
	{
		return $attr . '="' . $value . '"';
	}
	
	public function parseClass($className)
	{
		return $this->parseAttribute('class', $className);
	}
	
	private function parseAttrValue($attr)
	{
		return $this->parseAttribute($attr, $attr);
	}
	
	public function verify($condition, $return)
	{
		return $condition? ' ' . $return : '';
	}
	
	public function compare($value, $return)
	{
		return $this->verify($this->compareValue($value), $return);
	}
	
	public function verifyAttr($attr, $value)
	{
		return $this->compare($value, $this->parseAttrValue($attr));
	}
	
	public function check($value)
	{
		return $this->verifyAttr('checked', $value);
	}
	
	public function select($value)
	{
		return $this->verifyAttr('selected', $value);
	}
	
	public function disable($value)
	{
		return $this->verifyAttr('disabled', $value);
	}
	
	public function readonly($value)
	{
		return $this->verifyAttr('readonly', $value);
	}
}
?>
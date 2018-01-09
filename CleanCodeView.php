<?php
require_once 'CleanCodeClass.php';

abstract class CleanCodeView extends CleanCodeClass
{
	public static $data = array();
	
	protected function setContentType($type)
	{
		header("Content-Type: $type");
	}
	
	public function clearData()
	{
		self::$data = array();
	}
	
	public abstract function render();
}
?>
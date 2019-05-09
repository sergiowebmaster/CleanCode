<?php
require_once 'CleanCodeClass.php';

abstract class CleanCodeView extends CleanCodeClass
{
	public $data = array();
	
	public function addData($array)
	{
	    foreach ($array as $var => $value)
	    {
	        $this->data[$var] = $value;
	    }
	}
	
	protected function setContentType($type)
	{
	    header("Content-Type: {$type}");
	}
	
	public function debugData()
	{
	    print_r($this->data);
	}
	
	public abstract function show();
}
?>
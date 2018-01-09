<?php
require_once 'CleanCodeView.php';

class CleanCodeJsonView extends CleanCodeView
{
	function __construct()
	{
		$this->setContentType('text/plain');
	}
	
	public function render()
	{
		echo json_encode(self::$data);
	}
}
?>
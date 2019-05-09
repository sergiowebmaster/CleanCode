<?php
require_once 'CleanCodeView.php';

class CleanCodeJsonView extends CleanCodeView
{
	function __construct()
	{
		$this->setContentType('text/plain');
	}
	
	public function show()
	{
		echo json_encode($this->data);
	}
}
?>
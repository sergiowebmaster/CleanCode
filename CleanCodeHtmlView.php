<?php
require_once 'CleanCodeView.php';

class CleanCodeHtmlView extends CleanCodeView
{
	private $filename = '';
	
	function __construct($filename)
	{
		$this->setContentType('text/html');
		$this->filename = $filename;
	}
	
	public function show()
	{
	    extract($this->data);
	    include $this->filename;
	}
}
?>
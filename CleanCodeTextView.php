<?php
require_once 'CleanCodeView.php';

class CleanCodeTextView extends CleanCodeView
{
	protected $text = '';
	
	function __construct($type, $text)
	{
		$this->setContentType("text/$type");
		$this->text = $text;
	}
	
	public function show()
	{
		echo $this->text;
	}
}
?>
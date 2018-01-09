<?php
class CleanCodeHtmlView extends CleanCodeView
{
	private $filename = '';
	
	function __construct($filename)
	{
		$this->setContentType('text/html');
		$this->setFilename($filename);
	}
	
	protected function setFilename($filename)
	{
		$this->filename = CleanCodeDir::translate($filename);
	}
	
	public function render()
	{
		extract(self::$data);
		include $this->filename;
	}
}
?>
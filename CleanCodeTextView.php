<?php
class CleanCodeTextView extends CleanCodeView
{
	protected $text = '';
	
	function __construct($text)
	{
		$this->text = $text;
	}
	
	public function render()
	{
		echo count(self::$data) > 0? str_replace(array_keys(self::$data), self::$data, $this->text) : $this->text;
	}
}
?>
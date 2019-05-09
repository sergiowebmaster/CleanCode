<?php
require_once 'CleanCodeView.php';

class CleanCodeXmlView extends CleanCodeView
{
	public $version = '1.0';
	
	function __construct()
	{
		$this->setContentType('text/xml');
	}
	
	private function parseTag($tag, $valor)
	{
		return "<$tag>$valor</$tag>";
	}
	
	private function convertData($array)
	{
		$retorno = '';
	
		foreach ($array as $tag => $valor)
		{
			$retorno .= $this->parseTag($tag, is_array($valor)? $this->convertData($valor) : $valor);
		}
		
		return $retorno;
	}
	
	public function show()
	{
		echo '<?xml version="' . $this->version . '" encoding="' . self::$encoding . '"?>';
		echo $this->convertData($this->data);
	}
}
?>
<?php
class CleanCodeXmlView extends CleanCodeView
{
	public $version = '1.0';
	public $encoding = 'UTF-8';
	
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
	
	public function render()
	{
		echo '<?xml version="'.$this->version.'" encoding="'.$this->encoding.'"?>';
		echo $this->convertData(self::$data);
	}
}
?>
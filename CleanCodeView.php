<?php
require_once 'CleanCodeClass.php';

class CleanCodeView extends CleanCodeClass
{
	private $format = 'html';
	private $layout = '';
	
	public static $path = 'views/';
	
	private static $info = array(
			'lang' => 'pt-br',
			'baseHref' => '',
			'css' => array(),
			'js' => array(),
	);
	
	public $data = array();
	
	public static function formatDate($date, $formatTime = false)
	{
		$reg1 = $formatTime? ' (\d{2})\:(\d{2})\:(\d{2})' : '';
		$reg2 = $formatTime? ' $4:$5' : '';
		
		return preg_replace('/(\d{4})\-(\d{1,2})\-(\d{1,2})'.$reg1.'/', '$3/$2/$1'.$reg2, $date);
	}
	
	public static function tel($number)
	{
		return preg_replace('/^(\d{2})(\d{4,5})(\d{4})$/', '($1) $2-$3', $number);
	}
	
	public static function setBaseHref($url)
	{
		self::$info['baseHref'] = $url;
	}
	
	public static function addCSS($link)
	{
		self::$info['css'][] = $link;
	}
	
	public static function addJS($src)
	{
		self::$info['js'][] = $src;
	}
	
	public function toJSON()
	{
		echo json_encode($this->data);
	}
	
	public function render($html)
	{
		extract(self::$info);
		extract($this->data);
		include self::$path . $html;
	}
}
?>
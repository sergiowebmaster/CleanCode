<?php
require_once 'CleanCodeClass.php';

class CleanCodeView extends CleanCodeClass
{
	public static $opt = '';
	public static $path = 'views/';
	public static $global = array();
	
	private $layout = '';
	
	private static $info = array(
			'lang' => 'pt-br',
			'baseHref' => '',
			'robots' => '',
			'title' => '(Untitled)',
			'description' => '',
			'keywords' => '',
			'css' => array(),
			'js' => array(),
			'og' => array()
	);
	
	public $data = array();
	
	function __construct($layout)
	{
		$this->layout = $layout;
		$this->setRobots(true, true);
	}
	
	public static function formatDate($date)
	{
		return preg_replace('/(\d{4})\-(\d{1,2})\-(\d{1,2})/', '$3/$2/$1', $date);
	}
	
	public static function formatTime($time)
	{
		$data = explode(':', $time);
		return count($data) > 1? $data[0] . ':' . $data[1] : '';
	}
	
	public static function formatFullDate($timestamp)
	{
		$data = explode(' ', $timestamp);
		return count($data) > 1? self::formatDate($data[0]) . ' ' . self::formatTime($data[1]) : '';
	}
	
	public static function formatText($plainText, $filesPath, $pattern = array())
	{
		$pattern['/\n/'] = '<br />';
		$pattern['/([a-z]{1,}):([a-z0-9-_\.]{1,}.jpg)/i'] = '<img src="'.$filesPath.'$2" class="$1" />';
		
		return preg_replace(array_keys($pattern), $pattern, $plainText);
	}
	
	public static function formatSearchText($plainText, $filesPath, $keywords, $pattern = array())
	{
		if($keywords) $pattern['/('.$keywords.')/i'] = '<strong>$1</strong>';
		return self::formatText($plainText, $filesPath, $pattern);
	}
	
	public static function formatEmbedText($plainText)
	{
		$pattern['/(https:\/\/www.youtube.com\/watch\?v=)+(\w{1,})/i'] = '<iframe class="embed" src="https://www.youtube.com/embed/$2" frameborder="0" allowfullscreen></iframe>';
		return preg_replace(array_keys($pattern), $pattern, $plainText);
	}
	
	public static function tel_br($number)
	{
		return preg_replace('/^(\d{2})(\d{4,5})(\d{4})$/', '($1) $2-$3', $number);
	}
	
	public static function toMoney($double)
	{
		$parts = explode('.', $double);
		return self::searchPos($parts, 0, 0) . ',' . str_pad(self::searchPos($parts, 1, '00'), 2, 0);
	}
	
	public static function get($var, $array, $default = '')
	{
		return self::searchPos($array, $var, $default);
	}
	
	public static function addAttr($attr, $value, $wanted)
	{
		return $value == $wanted? $attr . '="' . $attr . '"' : '';
	}
	
	public static function disable($value)
	{
		return self::addAttr('disabled', $value, self::$opt);
	}
	
	public static function check($value)
	{
		return self::addAttr('checked', $value, self::$opt);
	}
	
	public static function select($value)
	{
		return self::addAttr('selected', $value, self::$opt);
	}
	
	public static function setLang($lang)
	{
		self::$info['lang'] = $lang;
	}
	
	public static function setBaseHref($url)
	{
		self::$info['baseHref'] = $url;
	}
	
	public function setRobots($index, $follow)
	{
		self::$info['robots'] = ($index? 'index' : 'noindex') . ',' . ($follow? 'follow' : 'nofollow');
	}
	
	public function setTitle($title)
	{
		self::$info['title'] = $title;
	}
	
	public function setDescription($description)
	{
		self::$info['description'] = $description;
	}
	
	private function set_og_url($url)
	{
		self::$info['og']['url'] = $url;
		self::setBaseHref($url);
	}
	
	public function set_og_locale($locale)
	{
		self::$info['og']['locale'] = $locale;
		self::setLang(str_replace('_', '-', strtolower($locale)));
	}
	
	public function set_og_type($type)
	{
		self::$info['og']['type'] = $type;
	}
	
	public function set_og_type_article()
	{
		$this->set_og_type('article');
	}
	
	public function set_og_type_website()
	{
		$this->set_og_type('website');
	}
	
	public function set_og_title($title)
	{
		self::$info['og']['title'] = $title;
		$this->setTitle($title);
	}
	
	public function set_og_description($description)
	{
		self::$info['og']['description'] = $description;
		$this->setDescription($description);
	}
	
	public function set_og_image_size($width, $height)
	{
		if($width) self::$info['og']['image:width'] = $width;
		if($height) self::$info['og']['image:height'] = $height;
	}
	
	public function set_og_image($src, $width = 0, $height = 0)
	{
		self::$info['og']['image'] = self::$info['baseHref'] . $src;
		$this->set_og_image_size($width, $height);
	}
	
	public function set_og_logo($src)
	{
		self::$data['logo'] = $src;
		$this->set_og_image($src);
	}
	
	public static function addCSS($link)
	{
		self::$info['css'][] = $link;
	}
	
	public static function addJS($src)
	{
		self::$info['js'][] = $src;
	}
	
	private function toJSON()
	{
		header('Content-Type: text/plain');
		echo json_encode($this->data);
	}
	
	private function renderHTML()
	{
		extract($this->data);
		extract(self::$global);
		extract(self::$info);
		include self::$path . $this->layout;
	}
	
	public function show()
	{
		$filename = self::$path . $this->layout;
		$this->layout? $this->renderHTML() : $this->toJSON();
	}
}
?>
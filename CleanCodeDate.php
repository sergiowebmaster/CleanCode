<?php
require_once 'CleanCodeClass.php';

class CleanCodeDate extends CleanCodeClass
{
	private static $defaultFormat = 'Y-m-d';
	
	private $sizeData = 0;
	
	public $day = 0;
	public $month = 0;
	public $year = 0;
	
	public static function getToday()
	{
		$instance = new self();
		$instance->setDateDB(date(self::$defaultFormat));
		
		return $instance;
	}
	
	public static function parseForDB($date)
	{
		return preg_replace('/^(\d{2})\/(\d{2})\/(\d{4})/', '$3-$2-$1', $date);
	}
	
	public static function parseForDisplay($date)
	{
		return preg_replace('/^(\d{4})-(\d{2})-(\d{2})/', '$3/$2/$1', $date);
	}
	
	private function checkValue()
	{
		return $this->day || $this->month || $this->year;
	}
	
	private function getMkTime()
	{
		return mktime(0, 0, 0, $this->month, $this->day, $this->year);
	}
	
	private function getByFormat($format)
	{
		return $this->checkValue()? date($format, $this->getMkTime()) : '';
	}
	
	private function setDate($array)
	{
		$this->day = self::searchPos($array, 0, 0);
		$this->month = self::searchPos($array, 1, 0);
		$this->year = self::searchPos($array, 2, 0);
	}
	
	public function setDateDisplay($date)
	{
		$this->setDate(explode('/', $date));
	}
	
	public function setDateDB($date)
	{
		$this->setDate(array_reverse(explode('-', $date)));
	}
	
	public function toDisplay()
	{
		return $this->getByFormat('d/m/Y');
	}
	
	public function toDB()
	{
		return $this->getByFormat(self::$defaultFormat);
	}
	
	public function isToday()
	{
		return $this->toDB() == self::getToday()->toDB();
	}
	
	public function getWeekNumber($inYear = false)
	{
		return $this->getByFormat($inYear? 'W' : 'w');
	}
	
	public function sumDays($days)
	{
		$this->setDateDB(date(self::$defaultFormat, strtotime($this->toDB() . ' + ' . $days . ' days')));
	}
}
?>
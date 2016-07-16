<?php
require_once 'CleanCodeModel.php';
require_once 'CleanCodeSQL.php';

class CleanCodeDAO extends CleanCodeModel
{
	private $cache = array();
	
	protected static $table = '';
	
	protected $data = array();
	protected $where = array();
	protected $whereSignal = '=';
	
	public static function getTable($alias = '')
	{
		$table = static::$table;
		
		if($alias) $table .= ' ' . $alias;
		
		return $table;
	}
	
	protected static function createSQL($alias = '')
	{
		return CleanCodeSQL::create(self::getTable($alias));
	}
	
	public static function getInstance()
	{
		return new static();
	}
	
	protected static function formatDate($date)
	{
		return preg_replace('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', '$3-$2-$1', $date);
	}
	
	protected function get_column($name, $default = '')
	{
		return self::searchPos($this->data, $name, $default);
	}
	
	protected function set_column($name, $value, $regex, $min = 1, $max = '')
	{
		if($regex == self::HTML)
		{
			$this->data[$name] = preg_replace('/^<\?.*\?>$/', '', $value);
		}
		else if($this->validate($value, $regex, $min, $max))
		{
			$value = strip_tags($value);
			
			if($regex == self::PWD) $value = md5($value);
			
			$this->data[$name] = $value;
			$this->where[$name][] = $name . $this->whereSignal . '"' . $value . '"';
		}
		else if($min)
		{
			$this->setErrorByField($name);
		}
	}
	
	protected function getWhere()
	{
		$where = array();
		
		foreach ($this->where as $values)
		{
			$where[] = join(' OR ', $values);
		}
		
		return '(' . join(') AND (', $where) . ')';
	}
	
	protected function set_primary_key($value)
	{
		$this->set_column('id', $value, self::NUM);
	}
	
	protected function select()
	{
		return self::createSQL()->select('*');
	}
	
	protected function selectByData()
	{
		return $this->select()->where($this->getWhere());
	}
	
	protected static function fetchAllBy($orderBy)
	{
		return self::select()->fetchBy($orderBy);
	}
	
	public static function fetchAll()
	{
		return self::select()->fetchAll();
	}
	
	public function fetch()
	{
		return $this->getError()? array() : $this->selectByData()->fetch();
	}
	
	public function filter()
	{
		return $this->selectByData()->fetchAll();
	}
	
	protected function fetchBy($orderBy)
	{
		return $this->selectByData()->fetchBy($orderBy);
	}
	
	public function loadFromDB()
	{
		$this->data = $this->cache = $this->fetch();
		return $this->data? true : false;
	}
	
	protected function insert()
	{
		if($this->getError())
		{
			return false;
		}
		else if($id = self::createSQL()->insert($this->data)->returnID())
		{
			$this->set_primary_key($id);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function update()
	{
		return $this->getError()? false : self::createSQL()->update($this->data)->where($this->cache)->execute();
	}
	
	protected function delete()
	{
		return $this->getError()? false : self::createSQL()->delete()->where($this->getWhere())->execute();
	}
	
	public function toArray()
	{
		return $this->data;
	}
}
?>
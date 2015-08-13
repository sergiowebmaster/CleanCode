<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CC_Model.php';
require_once 'CC_SQL.php'; 

class CC_DAO extends CC_Model
{
	protected static $table = '';
	
	private $filter = array();
	
	protected $data = array();
	
	public $found = false;
	
	protected static function createSQL($alias = '')
	{
		return CC_SQL::useTable(static::$table . ($alias? ' '.$alias : ''));
	}
	
	public static function getTable($alias = '')
	{
		$table = static::$table;
		
		if($alias) $table .= ' '.$alias;
		
		return $table;
	}
	
	protected function get_field($field, $default = '')
	{
		return self::searchIn($this->data, $field, $default);
	}
	
	public function get()
	{
		return (object) $this->data;
	}
	
	protected function clear()
	{
		$this->data = array();
	}
	
	protected function isEmptyField($fieldName)
	{
		return !isset($this->data[$fieldName]) || $this->data[$fieldName] == '';
	}
	
	protected function validateAndSet($field, $value, $regex, $min = 0, $max = '', $html = false)
	{
		if($this->validate($value, $regex, $min, $max))
		{
			$this->data[$field] = $html? $value : strip_tags($value);
			return true;
		}
		else
		{
			$this->setError('Preencha corretamente o campo "'.$field.'"!');
			return false;
		}
	}
	
	private function checkData()
	{
		return count($this->data) && $this->getError() == '';
	}
	
	public function createFilter()
	{
		$this->filter = $this->data;
	}
	
	protected function moveToFilter($field)
	{
		if(isset($this->data[$field])) $this->filter[$field] = $this->data[$field];
	}
	
	protected function get_filter()
	{
		return $this->filter;
	}
	
	protected static function getSelect($fields = '*')
	{
		return self::createSQL()->select($fields);
	}
	
	public function selectAll($fields = '*')
	{
		return $this->getSelect($fields)->whereData($this->data)->fetchAll();
	}
	
	public function select($fields = '*')
	{
		return $this->checkData()? $this->getSelect()->whereData($this->data)->fetch() : null;
	}
	
	protected function insert()
	{
		if($this->checkData() && self::createSQL()->insert($this->data)->execute())
		{
			return CC_SQL::insertId();
		}
		else
		{
			return false;
		}
	}
	
	protected function delete()
	{
		return $this->checkData()? self::createSQL()->delete()->whereData($this->data)->execute() : false;
	}
	
	protected function update()
	{
		return $this->checkData()? self::createSQL()->update($this->data)->whereData($this->filter)->execute() : false;
	}
	
	public function loadByData()
	{
		if($data = $this->select())
		{
			$this->found = true;
			
			foreach ($this->select() as $field => $value)
			{
				$this->data[$field] = $value;
			}
		}
	}
	
	public static function countRows()
	{
		return self::createSQL()->selectCount();
	}
	
	public static function countPages($amountPerPage)
	{
		return ceil(self::countRows() / $amountPerPage);
	}
}
?>
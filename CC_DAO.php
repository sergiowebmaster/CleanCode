<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CC_Model.php';
require_once 'CC_SQL.php'; 

class CC_DAO extends CC_Model
{
	protected static $table = '';
	
	protected $data = array();
	
	protected static function createSQL($alias = '')
	{
		return CC_SQL::create(static::$table . ($alias? ' '.$alias : ''));
	}
	
	public static function getTable()
	{
		return static::$table;
	}
	
	protected function get_field($field, $default = '')
	{
		return self::searchIn($this->data, $field, $default);
	}
	
	public function get()
	{
		return (object) $this->data;
	}
	
	protected function validateAndSet($field, $value, $regex, $min = 0, $max = '')
	{
		if($this->validate($value, $regex, $min, $max))
		{
			$this->data[$field] = $value;
			return true;
		}
		else
		{
			$this->setError('Preencha corretamente o campo "'.$field.'"!');
			return false;
		}
	}
	
	protected static function getSelect($fields = '*')
	{
		return self::createSQL()->select($fields);
	}
	
	public function selectAll($fields = '*')
	{
		return $this->getError()? array() : $this->getSelect()->fetchAll();
	}
	
	public function select($fields = '*')
	{
		return $this->getError()? null : $this->getSelect()->whereData($this->data)->fetch();
	}
	
	protected function insert()
	{
		return count($this->data) && $this->getError() == ''? self::createSQL()->insert($this->data)->execute() : false;
	}
	
	protected function delete($filter)
	{
		return self::createSQL()->delete()->whereData($filter)->execute();
	}
	
	protected function update($data, $filter = array())
	{
		return $this->getError()? false : self::createSQL()->update($data)->whereData($filter)->execute();
	}
	
	public function loadByData()
	{
		foreach ($this->select() as $field => $value)
		{
			$this->data[$field] = $value;
		}
	}
	
	public static function countRows()
	{
		return self::createSQL()->getCount();
	}
	
	public static function countPages($amountPerPage)
	{
		return ceil(self::countRows() / $amountPerPage);
	}
}
?>
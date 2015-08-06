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
		return CC_SQL::useTable(static::$table . ($alias? ' '.$alias : ''));
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
		return $this->getSelect()->whereData($this->data)->fetch();
	}
	
	protected function insert()
	{
		return $this->checkData()? self::createSQL()->insert($this->data)->execute() : false;
	}
	
	protected function delete()
	{
		return $this->checkData()? self::createSQL()->delete()->whereData($this->data)->execute() : false;
	}
	
	protected function update()
	{
		$data = $this->data;
		$filter = count($data) > 1? array_shift($data) : array();
		
		return $this->checkData()? self::createSQL()->update($data)->whereData($filter)->execute() : false;
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
		return self::createSQL()->selectCount();
	}
	
	public static function countPages($amountPerPage)
	{
		return ceil(self::countRows() / $amountPerPage);
	}
}
?>
<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'Model.php';
require_once 'SQL.php';

class DAO extends Model
{
	protected static $table = '';
	
	protected $data = array();
	
	protected static function createSQL()
	{
		return SQL::create(static::$table);
	}
	
	public static function getTable()
	{
		return static::$table;
	}
	
	protected function get($field)
	{
		return self::searchPos($field, $this->data);
	}
	
	protected function set($field, $value, $regex, $required = true)
	{
		if((!$value && !$required) || $this->validate($value, $regex))
		{
			$this->data[$field] = $value;
		}
		else
		{
			$this->setError('Preencha corretamente o campo "'.$field.'"!');
		}
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	protected function getSelect()
	{
		return self::createSQL()->select();
	}
	
	public function selectAll($fields = '*')
	{
		return $this->getSelect()->fetchAll();
	}
	
	public function select($fields = '*')
	{
		return $this->getSelect()->whereData($this->data)->fetch();
	}
	
	protected function insert()
	{
		return $this->getError()? false : self::createSQL()->insert($this->data)->execute();
	}
}
?>
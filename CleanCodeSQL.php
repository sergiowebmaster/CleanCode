<?php
require_once 'CleanCodeClass.php';

class CleanCodeSQL extends CleanCodeClass
{
    protected static $pdo;
    
    protected $table = '';
    protected $sql = array();
	protected $statement;
	
	public static function openConnection($driver, $host, $database, $user, $password)
	{
	    try
	    {
	        self::$pdo = new PDO("$driver:host=$host;dbname=$database", $user, $password);
	    }
	    catch (Exception $e)
	    {
	        die('Erro ao conectar com o Banco de Dados.');
	    }
	}
	
	public static function closeConnection()
	{
	    self::$pdo = null;
	}
	
	public static function getLastID()
	{
	    return self::$pdo->lastInsertId();
	}
	
	function __construct($table)
	{
	    $this->table = $table;
	}
	
	private function mergeClauses($array)
	{
	    $query = array();
	    
	    foreach ($array as $clause)
	    {
	        $query[] = is_array($clause)? $this->mergeClauses($clause) : $clause;
	    }
	    
	    return join($query, ' ');
	}
	
	public function toString()
	{
	    return $this->mergeClauses($this->sql);
	}
	
	protected function add($sql, $index)
	{
	    $this->sql[$index][] = $sql;
	    return $this;
	}
	
	protected function getCurrentIndex()
	{
	    return key($this->sql);
	}
	
	public static function setNames($charset)
	{
	    return self::$pdo->query("SET NAMES $charset");
	}
	
	private function parseData($data)
	{
	    $fields = array();
	    
	    foreach ($data as $field => $value)
	    {
	        $fields[] = $field . '=' . (is_string($value)? "'$value'" : $value);
	    }
	    
	    return $fields;
	}
	
	private function joinData($data, $separator)
	{
	    return join($separator, $data);
	}
	
	private function joinFields($data, $separator)
	{
	    return $this->joinData(array_keys($data), $separator);
	}
	
	protected function addNot()
	{
	    return $this->add('NOT', $this->getCurrentIndex());
	}
	
	protected function andData($data)
	{
	    return $this->joinData($data, ' AND ');
	}
	
	protected function orData($data)
	{
	    return $this->joinData($data, ' OR ');
	}
	
	public function insert($data)
	{
	    return $this->add("INSERT INTO {$this->table} ({$this->joinFields($data, ',')}) VALUES ('{$this->joinData($data, "','")}')", 0);
	}
	
	public function select($fields = '*')
	{
	    return $this->add("SELECT $fields FROM {$this->table}", 0);
	}
	
	public function selectCount($field, $alias)
	{
	    return $this->select("COUNT($field) $alias");
	}
	
	protected function addJoin($type, $table, $on)
	{
	    return $this->add("$type JOIN $table ON $on", 0);
	}
	
	public function innerJoin($table, $on)
	{
	    return $this->addJoin('INNER', $table, $on);
	}
	
	public function leftJoin($table, $on)
	{
	    return $this->addJoin('LEFT', $table, $on);
	}
	
	public function rightJoin($table, $on)
	{
	    return $this->addJoin('RIGHT', $table, $on);
	}
	
	public function crossJoin($table, $on)
	{
	    return $this->addJoin('CROSS', $table, $on);
	}
	
	public function update($stringData)
	{
	    return $this->add("UPDATE {$this->table} SET $stringData", 0);
	}
	
	public function updateData($data)
	{
	    return $this->update($this->joinFields($this->parseData($data), ','));
	}
	
	public function delete()
	{
	    return $this->add("DELETE FROM {$this->table}", 0);
	}
	
	public function where($condition)
	{
	    return $this->add("WHERE $condition", 1);
	}
	
	public function whereData($data)
	{
	    return $this->where($data? $this->andData($this->parseData($data)) : 1);
	}
	
	public function whereAnd($arg1, $arg2)
	{
	    return $this->where($this->andData(func_get_args()));
	}
	
	public function whereOr($arg1, $arg2)
	{
	    return $this->where($this->orData(func_get_args()));
	}
	
	public function like($data)
	{
	    return $this->add("LIKE '$data'", 1);
	}
	
	public function groupBy($fields)
	{
	    return $this->add("GROUP BY $fields", 2);
	}
	
	public function orderBy($field)
	{
	    return $this->add("ORDER BY $field", 3);
	}
	
	public function desc()
	{
	    return $this->add('DESC', 3);
	}
	
	public function limit($max, $min = 0)
	{
	    return $this->add("LIMIT $min,$max", 4);
	}
	
	public function execute()
	{
	    return self::$pdo? self::$pdo->query($this->toString()) : null;
	}
	
	public function fetch()
	{
	    return (self::$pdo && $query = $this->execute())? $query->fetch(PDO::FETCH_ASSOC) : array();
	}
	
	public function fetchAll()
	{
	    return (self::$pdo && $query = $this->execute())? $query->fetchAll(PDO::FETCH_ASSOC) : array();
	}
	
	public static function getErrorInfo($index = 2)
	{
	    return self::searchPos(self::$pdo->errorInfo(), $index);
	}
}
?>
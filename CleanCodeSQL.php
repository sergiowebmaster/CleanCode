<?php
class CleanCodeSQL extends CleanCodeClass
{
	private static $dns = 'mysql';
	private static $host = 'localhost';
	private static $database = '';
	private static $user = 'root';
	private static $password = '';
	
	public static $lastError = '';
	
	private $pdo;
	
	private $table	 = '';
	private $alias  = '';
	private $sql	 = '';
	private $values = array();
	private $statement;
	
	public $error = '';
	
	public static function usePostgree()
	{
		self::$dns = 'pgsql';
	}
	
	public static function setDB($dbName, $host = 'localhost')
	{
		self::$host	 = $host;
		self::$database	 = $dbName;
	}
	
	public static function setUser($name, $password)
	{
		self::$user	 = $name;
		self::$password = $password;
	}
	
	protected function connect()
	{
		try
		{
			$this->pdo = new PDO(self::$dns . ':host=' . self::$host.';dbname=' . self::$database, self::$user, self::$password);
			$this->pdo->query('SET NAMES utf8');
		}
		catch (Exception $e)
		{
			die('Erro ao conectar com o Banco de Dados.');
		}
	}
	
	function __construct($sql)
	{
		$this->connect();
		$this->sql = $sql;
	}
	
	private function getTable()
	{
		return join(' ', array($this->table, $this->alias));
	}
	
	public function setTable($table, $alias = '')
	{
		$this->table = $table;
		$this->alias = $alias;
	}
	
	public function setAlias($alias)
	{
		$this->alias = $alias;
		return $this;
	}
	
	public function toString()
	{
		return $this->sql;
	}
	
	public function add($sql)
	{
		$this->sql .= ' ' . $sql;
		return $this;
	}
	
	private function formatFields($data, $glue)
	{
		$this->values = array_merge($this->values, $data);
		$prefix = $this->alias? $this->alias . '.' : $this->alias;
		$fields = array();
		
		foreach (array_keys($data) as $field)
		{
			$fields[] = $prefix . $field . '=:' . $field;
		}
		
		return join($glue, $fields);
	}
	
	private function formatWhere($data)
	{
		return '(' . $this->formatFields($data, ' AND ') . ')';
	}
	
	public static function union($queries)
	{
		return new self('(' . join(') UNION (', $queries) . ')');
	}
	
	public function select($fields = '*')
	{
		$this->sql = 'SELECT ' . $fields . ' FROM ' . $this->getTable();
		return $this;
	}
	
	private function join($type, $table, $on)
	{
		return $this->add($type . ' JOIN ' . $table . ' ON ' . $on);
	}
	
	public function innerJoin($table, $on)
	{
		return $this->join('INNER', $table, $on);
	}
	
	public function leftJoin($table, $on)
	{
		return $this->join('LEFT', $table, $on);
	}
	
	public function rightJoin($table, $on)
	{
		return $this->join('RIGHT', $table, $on);
	}
	
	public function insert($data)
	{
		$fields = array_keys($data);
	
		$this->sql = 'INSERT INTO ' . $this->getTable() . ' (' . join(', ', $fields).') VALUES (:'.join(', :', $fields) . ')';
		$this->values = $data;
	
		return $this;
	}
	
	public function update($data)
	{
		$this->sql = 'UPDATE ' . $this->getTable() . ' SET ' . $this->formatFields($data, ', ');
		return $this;
	}
	
	public function delete()
	{
		$this->sql = 'DELETE FROM ' . $this->getTable();
		return $this;
	}
	
	private function defineCondition($data)
	{
		return is_string($data)? $data : $this->formatWhere($data);
	}
	
	public function where($condition = '')
	{
		return $this->add('WHERE')->add($condition? $this->defineCondition($condition) : '1');
	}
	
	public function whereAnd($condition = '')
	{
		return $this->add('AND')->add($this->defineCondition($condition));
	}
	
	public function whereOr($condition = '')
	{
		return $this->add('OR')->add($this->defineCondition($condition));
	}
	
	public function like($field, $keyword)
	{
		return $this->add($field . ' LIKE "%' . $keyword . '%"');
	}
	
	public function andLike($field, $keyword)
	{
		return $this->whereAnd()->like($field, $keyword);
	}
	
	public function orLike($field, $keyword)
	{
		return $this->whereOr()->like($field, $keyword);
	}
	
	public function groupBy($fields)
	{
		return $this->add('GROUP BY ' . $fields);
	}
	
	public function orderBy($fields)
	{
		return $this->add('ORDER BY ' . $fields);
	}
	
	public function desc()
	{
		return $this->add('DESC');
	}
	
	public function limit($quantity, $init = 0)
	{
		return $this->add('LIMIT ' . $init . ',' . $quantity);
	}
	
	public function execute()
	{
		$this->statement = $this->pdo->prepare($this->toString());
		$execute = $this->statement->execute($this->values);
	
		$errorInfo = $this->statement->errorInfo();
		self::$lastError = $errorInfo[2]? $errorInfo[2] : '';
	
		return $execute;
	}
	
	protected function debug()
	{
		echo $this->toString();
		echo '<br>("' . join('", "', $this->values) . '")';
		echo '<br>';
	}
	
	public function returnID()
	{
		return $this->pdo->lastInsertId();
	} 
	
	public function fetch()
	{
		$this->execute();
		return $this->statement->fetch(PDO::FETCH_ASSOC);
	}
	
	public function fetchAll()
	{
		$this->execute();
		return $this->statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function fetchBy($fields)
	{
		return $this->orderBy($fields)->fetchAll();
	}
	
	public function getRowCount()
	{
		return $this->statement->rowCount();
	}
	
	public function count($data = array())
	{
		return $this->select('COUNT(*) n')->where($data)->fetch()['n'];
	}
	
	function __destruct()
	{
		$this->pdo = null;
	}
}
?>
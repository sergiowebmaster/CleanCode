<?php
class CleanCodeSQL extends CleanCodeClass
{
	private static $host = 'localhost';
	private static $database = '';
	private static $user = 'root';
	private static $password = '';
	
	private $pdo;
	
	private $table	 = '';
	private $alias = '';
	private $values = array();
	private $sql	 = array();
	private $statement;
	
	public $error = '';
	
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
	
	function __construct($table)
	{
		try
		{
			$this->pdo = new PDO('mysql:host=' . self::$host.';dbname=' . self::$database, self::$user, self::$password);
			$this->pdo->query('SET NAMES utf8');
		}
		catch (Exception $e)
		{
			die('Erro ao conectar com o Banco de Dados.');
		}
		
		$this->table = $table;
	}
	
	public static function create($table)
	{
		return new self($table);
	}
	
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}
	
	public function setDataAlias($alias)
	{
		$this->alias = $alias;
		return $this;
	}
	
	public function toString()
	{
		ksort($this->sql);
		return join(' ', $this->sql);
	}
	
	private function setData($data, $sep = ', ')
	{
		$fields = array();
		
		foreach ($data as $field => $value)
		{
			$this->values[] = $value;
			$fields[] = ($this->alias? $this->alias . '.' : '') . $field . ' = ?';
		}
	
		return join($sep, $fields);
	}
	
	public function select($fields = '*')
	{
		$this->sql = array('SELECT '.$fields.' FROM '.$this->table);
		return $this;
	}
	
	private function join($type, $table, $on)
	{
		if(isset($this->sql[0])) $this->sql[0] .= ' '.$type.' JOIN '.$table.' ON '.$on;
		return $this;
	}
	
	public function innerJoin($table, $on)
	{
		$this->join('INNER', $table, $on);
		return $this;
	}
	
	public function leftJoin($table, $on)
	{
		$this->join('LEFT', $table, $on);
		return $this;
	}
	
	public function rightJoin($table, $on)
	{
		$this->join('RIGHT', $table, $on);
		return $this;
	}
	
	public function insert($data)
	{
		$fields = array_keys($data);
	
		$this->sql = array('INSERT INTO '.$this->table.' ('.join(', ', $fields).') VALUES (:'.join(', :', $fields).')');
		$this->values = $data;
	
		return $this;
	}
	
	public function update($fields)
	{
		$this->sql = array('UPDATE '.$this->table.' SET ' . $this->setData($fields));
		return $this;
	}
	
	public function delete()
	{
		$this->sql = array('DELETE FROM '.$this->table);
		return $this;
	}
	
	public function where($condition)
	{
		if($condition) $this->sql[1] = 'WHERE ' . $condition;
		return $this;
	}
	
	public function groupBy($fields)
	{
		$this->sql[2] = 'GROUP BY '.$fields;
		return $this;
	}
	
	public function orderBy($fields)
	{
		$this->sql[3] = 'ORDER BY '.$fields;
		return $this;
	}
	
	public function limit($quantity, $init = 0)
	{
		$this->sql[4] = 'LIMIT '.$init.','.$quantity;
		return $this;
	}
	
	public function execute()
	{
		$queryString = $this->toString();
		
		if(self::$debug)
		{
			echo $queryString, '<br>';
			print_r($this->values);
			echo '<br>';
		}
		
		$this->statement = $this->pdo->prepare($queryString);
		$execute = $this->statement->execute($this->values);
	
		$errorInfo = $this->pdo->errorInfo();
		$this->error = $errorInfo[2];
	
		return $execute;
	}
	
	public function returnID()
	{
		$this->execute();
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
	
	public function getNumRows()
	{
		$this->fetchAll();
		return $this->statement->rowCount();
	}
	
	public function count($data = array())
	{
		return $this->select('COUNT(*) n')->where($data)->fetch()['n'];
	}
	
	public function paginate($pageNumber, $amountPerPage)
	{
		$init = ($pageNumber - 1) * $amountPerPage;
		return $this->limit($amountPerPage, $init)->fetchAll();
	}
	
	function __destruct()
	{
		$this->pdo = null;
	}
}
?>
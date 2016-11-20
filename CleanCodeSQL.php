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
	
	private $table	 = array('', '');
	private $alias = '';
	private $values = array();
	private $sql	 = array();
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
	
	function __construct($table, $alias = '')
	{
		$this->setTable($table, $alias);
		$this->connect();
	}
	
	public function getTable()
	{
		return join(' ', $this->table);
	}
	
	public function setTable($table, $alias = '')
	{
		$this->table = array($table, $alias);
		return $this;
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
	
	public function addSQL($data)
	{
		$query = array();
		
		foreach ($data as $clause => $params)
		{
			$query[] = $clause;
			$query[] = is_array($params)? $this->addSQL($params) : $params;
		}
		
		return join(' ', $query);
	}
	
	public function toString()
	{
		ksort($this->sql);
		return join(' ', $this->sql);
	}
	
	public function select($fields = '*')
	{
		$this->sql = array('SELECT '.$fields.' FROM '.$this->getTable());
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
	
		$this->sql = array('INSERT INTO '.$this->getTable().' ('.join(', ', $fields).') VALUES (:'.join(', :', $fields).')');
		$this->values = $data;
	
		return $this;
	}
	
	public function update($fields)
	{
		$this->sql = array('UPDATE '.$this->getTable().' SET ' . $this->setData($fields));
		return $this;
	}
	
	public function delete()
	{
		$this->sql = array('DELETE FROM '.$this->getTable());
		return $this;
	}
	
	private function recursiveFilter($data, $signal, $sep = ' AND ')
	{
		$fields = array();
		
		foreach ($data as $index => $value)
		{
			if(is_array($value))
			{
				$fields[] = '(' . $this->recursiveFilter($value, $signal, ' OR ') . ')';
			}
			else
			{
				$alias = $this->alias? $this->alias . '.' : '';
				$fields[] = $alias . $index . ' ' . $signal . " '$value'";
			}
		}
		
		return join($sep, $fields);
	}
	
	public function where($condition, $signal = '=')
	{
		if($condition) $this->sql[1] = 'WHERE ' . (is_array($condition)? $this->recursiveFilter($condition, $signal) : $condition);
		return $this;
	}
	
	private function like($field, $keyword)
	{
		return $field . ' LIKE "%' . $keyword . '%"';
	}
	
	public function whereLike($field, $keyword)
	{
		return $this->where($this->like($field, $keyword));
	}
	
	public function whereLikeData($arrayData)
	{
		$fields = array();
		
		foreach ($arrayData as $field => $keyword)
		{
			$fields[] = $this->like($field, $keyword);
		}
		
		return $this->where(join(' OR ', $fields));
	}
	
	public function groupBy($fields)
	{
		$this->sql[2] = 'GROUP BY '.$fields;
		return $this;
	}
	
	public function orderBy($fields, $desc = false)
	{
		if($desc) $fields .= ' DESC';
		$this->sql[3] = 'ORDER BY ' . $fields;
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
		
		if (self::$debug)
		{
			echo $queryString;
			echo '<br>("' . join('", "', $this->values) . '")';
			echo '<br>';
		}
		
		$this->statement = $this->pdo->prepare($queryString);
		$execute = $this->statement->execute($this->values);
	
		$errorInfo = $this->statement->errorInfo();
		self::$lastError = $errorInfo[2]? $errorInfo[2] : '';
	
		return $execute;
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
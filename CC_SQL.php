<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

class CC_SQL
{
	private static $connection = array(
		'host' => 'localhost',
		'db' => '',
		'username' => 'root',
		'passwd' => '');
	
	private static $debug = false;
	
	protected static $pdo;
	
	private $table = '';
	private $values = array();
	private $sql = '';
	private $executed;
	
	public $error = '';
	
	public static function connect()
	{
		extract(self::$connection);
		
		try
		{
			self::$pdo = new PDO('mysql:host='.$host.';dbname='.$db, $username, $passwd);
			self::$pdo->query('SET NAMES utf8');
		}
		catch (Exception $e)
		{
			die('Erro ao conectar com o Banco de Dados.');
		}
	}
	
	public static function debugQueries()
	{
		self::$debug = true;
	}
	
	public static function setDB($db, $host = 'localhost')
	{
		self::$connection['host']	= $host;
		self::$connection['db']		= $db;
		
		if(self::$pdo) self::connect();
	}
	
	public static  function setUser($username, $passwd)
	{
		self::$connection['username']	= $username;
		self::$connection['passwd']		= $passwd;
		
		if(self::$pdo) self::connect();
	}
	
	public static function useTable($table)
	{
		if(!self::$pdo) die('PDO não conectado!');
		
		$query = new self();
		return $query->setTable($table);
	}
	
	function __construct($sql = '')
	{
		$this->sql = $sql;
	}
	
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}
	
	private function setData($data, $sep = ', ')
	{
		$fields = array();
	
		foreach ($data as $field => $value)
		{
			$this->values[] = $value;
			$fields[] = $field.' = ?';
		}
	
		return join($sep, $fields);
	}
	
	public function select($fields = '*')
	{
		$this->sql = 'SELECT '.$fields.' FROM '.$this->table;
		return $this;
	}
	
	private function join($type, $table, $on)
	{
		$this->sql .= ' '.$type.' JOIN '.$table.' ON '.$on;
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
		$this->values = $data;
	
		$this->sql = 'INSERT INTO '.$this->table.' ('.join(', ', $fields).') VALUES (:'.join(', :', $fields).')';
	
		return $this;
	}
	
	public function update($fields)
	{
		$this->sql = 'UPDATE '.$this->table.' SET ' . $this->setData($fields);
		return $this;
	}
	
	public function delete()
	{
		$this->sql = 'DELETE FROM '.$this->table;
		return $this;
	}
	
	public function where($condition)
	{
		$this->sql .= ' WHERE '.self::scape($condition);
		return $this;
	}
	
	public function whereData($data, $separator = 'AND')
	{
		return $data? $this->where($this->setData($data, ' '.$separator.' ')) : $this;
	}
	
	public function limit($quantity, $init = 0)
	{
		$this->sql .= ' LIMIT '.$init.','.$quantity;
		return $this;
	}
	
	public function orderBy($fields)
	{
		$this->sql .= ' ORDER BY '.$fields;
		return $this;
	}
	
	public function groupBy($fields)
	{
		$this->sql .= ' GROUP BY '.$fields;
		return $this;
	}
	
	public function union()
	{
		$this->sql .= ' UNION ';
		return $this;
	}
	
	public function set($query)
	{
		$this->sql = $query;
	}
	
	public static function query($sql)
	{
		$obj = new self('');
		$obj->set(self::scape($sql));
		
		return $obj;
	}
	
	public function execute()
	{
		$this->executed = self::$pdo->prepare($this->sql);
		$execute = $this->executed->execute($this->values);
		
		foreach ($this->values as $field => $value)
		{
			if(is_numeric($field)) break;
			else
			{
				$this->executed->bindParam(':'.$field, $value);
			}
		}
		
		if(self::$debug) echo "<!-- " . $this->sql . " --->\n";
	
		$errorInfo = self::$pdo->errorInfo();
		$this->error = $errorInfo[2];
	
		return $execute;
	}
	
	public static function insertId()
	{
		return self::$pdo->lastInsertId();
	}
	
	public function fetch()
	{
		$this->execute();
		return $this->executed->fetch(PDO::FETCH_OBJ);
	}
	
	public function fetchAll()
	{
		$this->execute();
		return $this->executed->fetchAll(PDO::FETCH_OBJ);
	}
	
	public function paginate($pageNumber, $amountPerPage)
	{
		$init = ($pageNumber - 1) * $amountPerPage;
		return $this->limit($amountPerPage, $init)->fetchAll();
	}
	
	public function selectCount($data = array(), $separator = 'AND')
	{
		$result = $this->select('count(*) n')->whereData($data, $separator)->fetch();
		return $result->n;
	}
	
	public static function scape($sql)
	{
		return preg_replace('/\-{2,}/', '', $sql);
	}
	
	public static function closeConnection()
	{
		self::$pdo = null;
	}
	
	public function toString()
	{
		return $this->sql;
	}
}
?>
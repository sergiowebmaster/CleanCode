<?php
class CleanCodeSQL extends CleanCodeClass
{
	protected $table = '';
	protected $statement = '';
	protected $values = array();
	protected $result;
	protected $pdo;
	
	function __construct($dns, $host, $database, $user, $password)
	{
		try
		{
			$this->pdo = new PDO("$dns:host=$host;dbname=$database", $user, $password);
			$this->setNames('utf8');
		}
		catch (Exception $e)
		{
			die('Erro ao conectar com o Banco de Dados.');
		}
	}
	
	public function setTable($table)
	{
		$this->table = $table;
		return $this;
	}
	
	public function prepare($string)
	{
		$this->statement = $string;
		return $this;
	}
	
	public function add($sql)
	{
		$this->statement .= ' ' . $sql;
		return $this;
	}
	
	protected function addValue($value)
	{
		return $this->add(is_string($value)? "'$value'" : $value);
	}
	
	protected function addField($field, $signal, $value)
	{
		return $this->add($field)->add($signal)->addValue($value);
	}
	
	protected function addData($data, $glue)
	{
		$fields = array();
		
		foreach ($data as $field => $value)
		{
			$fields[] = "$field=:$field";
			$this->values[$field] = $value;
		}
		
		return $this->add(join($glue, $fields));
	}
	
	public function toString()
	{
		return $this->statement;
	}
	
	public function sub($sql)
	{
		return $this->add("($sql)");
	}
	
	public function select($fields = '*')
	{
		return $this->prepare("SELECT $fields FROM $this->table");
	}
	
	public function selectCount()
	{
		return $this->select('COUNT(*) n');
	}
	
	public function count($condition)
	{
		return $this->selectCount()->where($condition)->fetch();
	}
	
	public function countAll()
	{
		return $this->count('1');
	}
	
	private function join($type, $table, $on)
	{
		return $this->add("$type JOIN $table ON $on");
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
	
	protected function joinInsertData($data)
	{
		$fields = array_keys($data);
		$this->values = $data;
		
		return $this->sub(join(', ', $fields))->add('VALUES')->sub(':' . join(', :', $fields));
	}
	
	public function insert($data)
	{
		return $this->prepare('INSERT INTO')->add($this->table)->joinInsertData($data);
	}
	
	public function update($data)
	{
		return $this->prepare('UPDATE')->add($this->table)->add('SET')->addData($data, ',');
	}
	
	public function delete()
	{
		return $this->prepare('DELETE FROM')->add($this->table);
	}
	
	public function where($condition)
	{
		return $this->add('WHERE')->add($condition);
	}
	
	public function andWhere($condition)
	{
		return $this->add('AND')->add($condition);
	}
	
	public function orWhere($condition)
	{
		return $this->add('OR')->add($condition);
	}
	
	protected function andData($data)
	{
		foreach ($data as $field => $value)
		{
			$this->andWhere($field)->add('=')->addValue($value);
		}
		
		return $this;
	}
	
	protected function orData($data)
	{
		foreach ($data as $field => $value)
		{
			$this->orWhere($field)->add('=')->addValue($value);
		}
		
		return $this;
	}
	
	public function whereData($data)
	{
		return $this->where(1)->andData($data);
	}
	
	public function like($keyword)
	{
		return $this->add('LIKE')->add('"'.$keyword.'"');
	}
	
	public function likeBegin($keyword)
	{
		return $this->like("$keyword%");
	}
	
	public function likeEnd($keyword)
	{
		return $this->like("%$keyword");
	}
	
	public function likeBoth($keyword)
	{
		return $this->like("%$keyword%");
	}
	
	public function groupBy($fields)
	{
		return $this->add('GROUP BY')->add($fields);
	}
	
	public function orderBy($fields)
	{
		return $this->add('ORDER BY')->add($fields);
	}
	
	public function addOrderBy($fields, $desc)
	{
		return $desc? $this->orderBy($fields)->desc() : $this->orderBy($fields);
	}
	
	public function orderByRand()
	{
		return $this->orderBy('RAND()');
	}
	
	public function desc()
	{
		return $this->add('DESC');
	}
	
	public function limit($quantity, $init = 0)
	{
		return $this->add('LIMIT')->add("$init,$quantity");
	}
	
	public function paginate($qtyPerPage, $pageNumber)
	{
		return $this->limit($qtyPerPage, ($pageNumber - 1) * $qtyPerPage);
	}
	
	public function execute()
	{
		$this->result = $this->pdo->prepare($this->statement);
		return $this->result->execute($this->values);
	}
	
	public function getLastError()
	{
		$errorInfo = $this->result->errorInfo();
		return $errorInfo[2]? $errorInfo[2] : '';
	}
	
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
	} 
	
	public function fetch()
	{
		$this->execute();
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}
	
	public function fetchAll()
	{
		$this->execute();
		return $this->result->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function fetchBy($fields, $desc = false)
	{
		return $this->addOrderBy($fields, $desc)->fetchAll();
	}
	
	public function getRowCount()
	{
		return $this->result->rowCount();
	}
	
	public function setNames($charset)
	{
		$this->pdo->query("SET NAMES $charset");
	}
	
	public function close()
	{
		$this->pdo = null;
	}
	
	protected function debug()
	{
		echo $this->toString();
		echo '<br>("' . join('", "', $this->values) . '")';
		echo '<br>';
	}
}
?>
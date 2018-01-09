<?php
require_once 'CleanCodeModel.php';
require_once 'CleanCodeSQL.php';

class CleanCodeDAO extends CleanCodeModel
{
	private $cache = array();
	
	protected static $table = '';
	protected static $qtyByPage = 1;
	protected static $filesPath = 'uploads/';
	protected static $db;
	
	protected $data = array();
	protected $uploads = array();
	protected $alias = '';
	
	public static function getFilesPath()
	{
		return static::$filesPath;
	}
	
	public static function getFullPath($filename)
	{
		return self::getFilesPath() . $filename;
	}
	
	public static function getTable()
	{
		return static::$table;
	}
	
	public static function getTableAlias($alias)
	{
		return self::getTable() . ' ' . $alias;
	}
	
	public static function openConnection(CleanCodeSQL $db)
	{
		self::$db = $db;
	}
	
	protected static function getConnection()
	{
		return self::$db->setTable(self::getTable());
	}
	
	public static function closeConnection()
	{
		self::$db->close();
	}
	
	public static function setQtyByPage($qty)
	{
		static::$qtyByPage = $qty;
	}
	
	public static function getInstance()
	{
		return new static();
	}
	
	public function toArray()
	{
		return $this->data;
	}
	
	public function getFields($separator = ', ')
	{
		return join($separator, array_keys($this->data));
	}
	
	protected function get_column($name, $default = '')
	{
		return self::searchPos($this->data, $name, $default);
	}
	
	protected function set_column($name, $value, $regex, $min = 1, $max = '')
	{
		if($this->validate($value, $regex, $min, $max))
		{
			$this->data[$name] = $this->formatData($value, $regex);
		}
		else
		{
			$this->setErrorByField($name);
		}
	}
	
	protected function set_uri_column($name, $uri, $min = 0)
	{
		$this->set_column($name, $uri, self::URI, $min);
	}
	
	protected function set_file_column($name, CleanCodeFile $file, $required)
	{
		if($file->getTmpName() || $required)
		{
			$this->set_column($name, $file->getName(), self::FILE);
			$this->uploads[] = $file;
		}
	}
	
	protected function set_date_column($name, $date)
	{
		$this->set_column($name, CleanCodeDate::parseForDB($date), self::DATE);
	}
	
	protected function set_primary_key($pk)
	{
		// Implement
	}
	
	public function getIP()
	{
		return $this->get_column('ip');
	}
	
	public function setIP($ip)
	{
		$this->set_column('ip', $ip, self::IP);
	}
	
	public function memorize()
	{
		$this->cache = $this->data;
	}
	
	protected static function select()
	{
		return self::getConnection()->select();
	}
	
	protected static function selectJoin($alias, $fields)
	{
		return self::$db->setTable(self::getTableAlias($alias))->select($fields);
	}
	
	protected function selectByData()
	{
		return self::select()->whereData($this->data);
	}
	
	public function fetch()
	{
		return $this->getError()? array() : $this->selectByData()->fetch();
	}
	
	public static function fetchAll()
	{
		return self::select()->fetchAll();
	}
	
	protected static function fetchAllBy($orderBy, $desc = false)
	{
		return self::select()->fetchBy($orderBy, $desc);
	}
	
	public static function fetchNumRows()
	{
		return self::$db->count();
	}
	
	public static function fetchNumPages()
	{
		return ceil(self::fetchNumRows() / static::$qtyByPage);
	}
	
	protected function fetchBy($orderBy, $desc = false)
	{
		return $this->selectByData()->fetchBy($orderBy, $desc);
	}
	
	private static function fetchOneBy($orderBy, $desc)
	{
		return self::select()->orderBy($orderBy, $desc)->limit(1)->fetch();
	}
	
	protected static function fetchNextBy($orderBy)
	{
		return self::fetchOneBy($orderBy, false);
	}
	
	protected static function fetchLastBy($orderBy)
	{
		return self::fetchOneBy($orderBy, true);
	}
	
	public function toFilter()
	{
		return $this->selectByData()->fetchAll();
	}
	
	public function filterOne()
	{
		return $this->selectByData()->fetch();
	}
	
	public function loadFromDB()
	{
		$this->data = $this->fetch();
		return $this->data? true : false;
	}
	
	private function sendFiles()
	{
		$send = true;
		
		foreach ($this->uploads as $file)
		{
			if(!$file->send())
			{
				$this->setError($file->getError());
				$send = false;
			}
		}
		
		return $send;
	}
	
	protected function execute($operation, $successMessage, $errorMessage = '')
	{
		return parent::execute($operation, $successMessage, $errorMessage? $errorMessage : self::$db->getLastError());
	}
	
	protected function insertIntoDB()
	{
		return $this->checkError() && self::getConnection()->insert($this->data)->execute();
	}
	
	protected function insertWithFiles()
	{
		return $this->checkError() && $this->sendFiles() && $this->insertIntoDB();
	}
	
	public function insert()
	{
		return $this->execute($this->insertWithFiles(), 'Insert successfuly!');
	}
	
	protected function updateFromDB()
	{
		return $this->checkError() && self::getConnection()->update($this->data)->whereData($this->cache)->execute();
	}
	
	protected function updateWithFiles()
	{
		return $this->checkError() && $this->sendFiles() && $this->updateFromDB();
	}
	
	public function update()
	{
		return $this->execute($this->updateWithFiles(), 'Update successfuly!');
	}
	
	protected function deleteFromDB()
	{
		return $this->checkError() && self::getConnection()->delete()->whereData($this->data)->execute();
	}
	
	protected function deleteWithFiles($file1)
	{
		foreach (func_get_args() as $filename)
		{
			$file = new CleanCodeFile('*');
			$file->setPath(self::getFilesPath());
			$file->setName($filename);
			$file->delete();
			
			$this->setError($file->getError());
		}
		
		return $this->checkError() && $this->deleteFromDB();
	}
	
	protected function deleteWithFolder($folderPath)
	{
		$dir = new CleanCodeDir($folderPath);
		return $dir->delete() && $this->deleteFromDB();
	}
	
	public function delete()
	{
		return $this->execute($this->deleteFromDB(), 'Delete successfuly!');
	}
	
	public function search()
	{
		return self::select()->where($this->data)->fetchAll();
	}
	
	public function toggle()
	{
		return $this->insertIntoDB() || $this->deleteFromDB();
	}
	
	public function saveFile(CleanCodeFile $file)
	{
		$this->insertIntoDB();
	}
}
?>
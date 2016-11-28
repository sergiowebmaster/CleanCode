<?php
require_once 'CleanCodeModel.php';
require_once 'CleanCodeSQL.php';

class CleanCodeDAO extends CleanCodeModel
{
	private $cache = array();
	
	protected static $table = '';
	protected static $qtyByPage = 1;
	protected static $uploadsPath = '';
	
	protected $data = array();
	protected $where = array();
	protected $whereSignal = '=';
	protected $uploads = array();
	
	public static function getTable($alias = '')
	{
		$table = static::$table;
		
		if($alias) $table .= ' ' . $alias;
		
		return $table;
	}
	
	public static function setQtyByPage($qty)
	{
		static::$qtyByPage = $qty;
	}
	
	protected static function createSQL($alias = '')
	{
		return new CleanCodeSQL(static::$table, $alias);
	}
	
	public static function getInstance()
	{
		return new static();
	}
	
	protected static function formatDate($date)
	{
		return preg_replace('/(\d{1,2})\/(\d{1,2})\/(\d{4})/', '$3-$2-$1', $date);
	}
	
	protected function get_column($name, $default = '')
	{
		return self::searchPos($this->data, $name, $default);
	}
	
	public function memorize()
	{
		$this->cache = $this->data;
	}
	
	protected function set_column($name, $value, $regex, $min = 1, $max = '')
	{
		$this->data[$name] = $value = $this->formatData($value, $regex);
		
		if(!$this->validate($value, $regex, $min, $max))
		{
			$this->setErrorByField($name);
		}
	}
	
	protected function set_file_column($columnName, CleanCodeFile $file, $required)
	{
		if($file->getTmpName() || $required)
		{
			$this->set_column($columnName, $file->getName(), self::FILE, 5);
			$this->uploads[] = $file;
		}
	}
	
	protected function set_date_column($columnName, $value)
	{
		$this->set_column($columnName, $this->formatDate($value), self::DATE);
	}
	
	protected function set_primary_key($pk)
	{
		// Implement
	}
	
	protected function set_uri_column($uri)
	{
		$this->set_column('uri', $uri, self::URI);
	}
	
	public function getIP()
	{
		return $this->get_column('ip');
	}
	
	public function setIP()
	{
		$this->set_column('ip', $_SERVER['REMOTE_ADDR'], self::IP);
	}
	
	protected static function select($fields = '*')
	{
		return self::createSQL()->select($fields);
	}
	
	protected function selectByData()
	{
		return $this->select()->where($this->data);
	}
	
	public static function fetchAll()
	{
		return self::select()->fetchAll();
	}
	
	protected static function fetchAllBy($orderBy)
	{
		return self::select()->fetchBy($orderBy);
	}
	
	public static function fetchNumRows()
	{
		return self::createSQL()->count();
	}
	
	public static function fetchNumPages()
	{
		return ceil(self::fetchNumRows() / static::$qtyByPage);
	}
	
	protected function fetchBy($orderBy)
	{
		return $this->selectByData()->fetchBy($orderBy);
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
	
	public function fetch()
	{
		$this->memorize();
		return $this->getError()? array() : $this->selectByData()->fetch();
	}
	
	public function filter()
	{
		return $this->selectByData()->fetchAll();
	}
	
	protected static function paginate($pageNumber, $orderBy, $desc = true)
	{
		$filter = new self();
		$filter::$table = self::getTable();
		
		return $filter->paginateByData($pageNumber, $orderBy, $desc);
	}
	
	protected function paginateByData($pageNumber, $orderBy, $desc = true)
	{
		$init = ($pageNumber - 1) * static::$qtyByPage;
		return $this->selectByData()->orderBy($orderBy, $desc)->limit(static::$qtyByPage, $init)->fetchAll();
	}
	
	public function loadFromDB()
	{
		$this->data = $this->fetch();
		return $this->data? true : false;
	}
	
	public function loadByPK($pk)
	{
		$this->set_primary_key($pk);
		$this->memorize();
		
		return $this->loadFromDB();
	}
	
	public function loadByUri($uri)
	{
		$this->set_uri_column($uri);
		$this->memorize();
		
		return $this->loadFromDB();
	}
	
	protected function beforeInsert($pk)
	{
		$this->set_primary_key($pk);
	}
	
	private function sendFiles()
	{
		$send = true;
		
		foreach ($this->uploads as $file)
		{
			if(!$file->send())
			{
				$this->setError(self::msg('upload_error'));
				$send = false;
				break;
			}
		}
		
		return $send;
	}
	
	protected function deleteFile($fileName)
	{
		$file = new CleanCodeFile('*');
		$file->setPath(static::$uploadsPath);
		$file->setName($fileName);
		
		return $file->delete();
	}
	
	protected function execute($operation, $successMessage, $errorMessage = '')
	{
		return parent::execute($operation, $successMessage, $errorMessage? $errorMessage : CleanCodeSQL::$lastError);
	}
	
	private function insertIntoDB()
	{
		if($this->getError())
		{
			return false;
		}
		else
		{
			$query = self::createSQL()->insert($this->data);
			
			if($query->execute())
			{
				$this->beforeInsert($query->returnID());
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	protected function insert()
	{
		return $this->sendFiles() && $this->insertIntoDB();
	}
	
	private function updateFromDB()
	{
		return $this->getError()? false : self::createSQL()->update($this->data)->where($this->cache)->execute();
	}
	
	protected function update()
	{
		return $this->sendFiles() && $this->updateFromDB();
	}
	
	protected function delete()
	{
		return $this->getError()? false : self::createSQL()->delete()->where($this->data)->execute();
	}
	
	public function toArray()
	{
		return $this->data;
	}
	
	public function search()
	{
		return self::select()->whereLikeData($this->data)->fetchAll();
	}
	
	public function toggle()
	{
		if($this->insertIntoDB())
		{
			return true;
		}
		else
		{
			$this->delete();
			return false;
		}
	}
	
	protected function saveFile($name, $tmpName, $type, $size)
	{
		print_r(func_get_args());
	}
	
	protected function uploadFiles($files)
	{
		CleanCodeFile::prepareMultiple($files, function($name, $tmpName, $type, $size)
		{
			static::saveFile($name, $tmpName, $type, $size);
		});
	}
}
?>
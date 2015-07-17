<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'Model.php';

class File extends Model
{
	private static $path = '';
	
	private $folder = '';
	private $size = 0;
	
	protected $name = '';
	protected $tmp  = '';
	
	public static function setPath($path)
	{
		static::$path = $path;
	}
	
	protected function getFullPath()
	{
		return static::$path . $this->folder . $this->name;
	}
	
	public function setFolder($folder)
	{
		$this->folder = $folder;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
		$this->setField('name', $name, self::ALL);
	}
	
	public function getTmp()
	{
		return $this->tmp;
	}
	
	protected function setTmp($tmp)
	{
		$this->setField('tmp', $tmp, self::ALL);
	}
	
	public function loadData($files)
	{
		if(isset($files['name']))
		{
			$this->setName($files['name']);
			$this->setTmp($files['tmp_name']);
			$this->size = $files['size'];
		}
	}
	
	protected function upload()
	{
		return copy($this->tmp, $this->getFullPath());
	}
	
	public function delete()
	{
		return unlink($this->getFullPath());
	}
	
	public function send()
	{
		return $this->getError()? false : $this->upload();
	}
	
	public function sendIfHave()
	{
		return $this->name? $this->send() : false;
	}
}
?>
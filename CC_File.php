<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CC_Model.php';

class CC_File extends CC_Model
{
	protected static $path = '';
	
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
		if($this->validate($name, self::ALL))
		{
			$this->name = $name;
		}
	}
	
	public function getTmp()
	{
		return $this->tmp;
	}
	
	public function setTmp($tmp)
	{
		if($this->validate($tmp, self::ALL))
		{
			$this->tmp = $tmp;
		}
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
	
	public function generateName($size, $ext)
	{
		$filename = '';
		
		while($filename == '' || file_exists($filename))
		{
			$filename = self::getRandomString($size) . '.' . $ext;
		}
		
		$this->setName($filename);
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
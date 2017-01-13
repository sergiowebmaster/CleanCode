<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CleanCodeModel.php';
require_once 'CleanCodeDir.php';

class CleanCodeFile extends CleanCodeModel
{
	protected static $path = '';
	
	private $filter = array();
	private $folder = '';
	
	protected $name = '';
	protected $tmp_name  = '';
	protected $type = '';
	protected $size = 0;
	
	public $ext	= '';
	public $oldName = '';
	
	protected function getFolderPath()
	{
		return self::format(static::$path . '/' . $this->folder . '/');
	}
	
	public static function setPath($path)
	{
		static::$path = CleanCodeDir::translate($path);
	}
	
	public static function prepareMultiple($files, $callback)
	{
		for($i=0; $i < count($files['name']); $i++)
		{	
			$callback($files['name'][$i], $files['tmp_name'][$i], $files['type'][$i], $files['size'][$i]);
		}
	}
	
	public function getFullPath()
	{
		return $this->getFolderPath() . $this->name;
	}
	
	public static function format($string)
	{
		return self::format_url($string, true);
	}
	
	function __construct($extensions)
	{
		$this->filter = explode('|', $extensions);
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
		$name = self::format($name);
		
		if($this->validate($name, self::FILE))
		{
			$parts = explode('.', $name);
			$this->name = $name;
			$this->ext = end($parts);
		}
		else
		{
			$this->setErrorByField('name');
		}
	}
	
	public function getTmpName()
	{
		return $this->tmp_name;
	}
	
	public function setTmpName($tmpName)
	{
		if($tmpName) $this->tmp_name = $tmpName;
		else $this->setErrorByField('tmp_name');
	}
	
	public function getSize()
	{
		return $this->size;
	}
	
	public function setSize($size)
	{
		if($size && is_numeric($size)) $this->size = $size;
		else $this->setErrorByField('size');
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	private function setExt($type)
	{
		switch ($type)
		{
			case 'image/jpeg':
				$this->ext = 'jpg';
				break;
				
			default:
				$info = explode('/', $type);
				$this->ext = end($info);
		}
	}
	
	public function setType($type)
	{
		if($type)
		{
			$this->type = $type;
			$this->setExt($type);
		}
		else $this->setErrorByField('type');
	}
	
	public function set($name, $tmpName, $type, $size)
	{
		$this->setName($name);
		$this->setTmpName($tmpName);
		$this->setType($type);
		$this->setSize($size);
	}
	
	protected function checkType()
	{
		if(in_array($this->ext, $this->filter) || $this->filter == array('*'))
		{
			return true;
		}
		else
		{
			$this->setErrorByField('extension');
			return false;
		}
	}
	
	public function createRandomName($length)
	{
		$this->setName(CleanCodeHelper::generateHash($length) . '.' . $this->ext);
	}
	
	protected function upload()
	{
		return copy($this->tmp_name, $this->getFullPath());
	}
	
	public function delete()
	{
		return $this->name && file_exists($this->getFullPath()) && unlink($this->getFullPath());
	}
	
	private function deleteOld()
	{
		$old = new self('*');
		$old->setFolder($this->folder);
		$old->setName($this->oldName);
		
		return $old->delete();
	}
	
	public function send()
	{
		if($this->name == '' || $this->getError())
		{
			return false;
		}
		else if($this->checkType() && $this->upload())
		{
			return $this->oldName == '' || $this->oldName == $this->name || $this->deleteOld();
		}
		else
		{
			$this->setError('Upload failed!');
			return false;
		}
	}
	
	public function getContent($vars = array())
	{
		$content = file_get_contents($this->getFullPath());
		return $vars && is_array($vars)? str_replace(array_keys($vars), $vars, $content) : $content;
	}
}
?>
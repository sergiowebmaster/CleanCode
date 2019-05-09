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
	protected $handle;
	
	public $ext	= '';
	public $oldName = '';
	
	protected function getFolderPath()
	{
		return static::$path . $this->folder;
	}
	
	public function getFullPath()
	{
		return $this->getFolderPath() . $this->name;
	}
	
	public static function setPath($path)
	{
		static::$path = CleanCodeDir::get($path);
	}
	
	public static function prepareArray($files)
	{
		$array = array();
		
		foreach ($files as $item => $data)
		{
			for($i=0; $i < count($data); $i++)
			{
				$array[$i][$item] = $data[$i];
			}
		}
		
		return $array;
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
		
		if($this->validateData($name, self::FILE))
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
		$this->setName(self::generateHash($length) . '.' . $this->ext);
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
		else if($this->checkType())
		{
			return $this->upload();
		}
		else
		{
			$this->setError('Upload failed!');
			return false;
		}
	}
	
	protected function fopen($mode)
	{
	    $this->handle = file_exists($this->getFullPath())? fopen($this->getFullPath(), $mode) : false;
	}
	
	protected function write($string)
	{
	    fwrite($this->handle, $string);
	}
	
	protected function close()
	{
	    fclose($this->handle);
	}
	
	public function writeLine($string)
	{
	    $this->fopen('a');
	    $this->write($string);
	    $this->close();
	}
	
	public function getContent()
	{
	    $this->fopen('r');
		$content = '';
		
		if($this->handle)
		{
    		while ($line = fgets($this->handle))
    		{
    			$content .= $line;
    		}
		}
		
		return $content;
	}
	
	public function getBase64()
	{
	    return base64_encode($this->getContent());
	}
	
	public function getDynamicContent($vars)
	{
	    return str_replace(array_keys($vars), $vars, $this->getContent());
	}
	
	public function setContent($data)
	{
		if(!@file_put_contents($this->getFullPath(), $data))
		{
			$this->setError('Failed to update ' . $this->getFullPath());
		}
	}
}
?>
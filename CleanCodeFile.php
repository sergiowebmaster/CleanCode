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
	private $trash= '';
	
	protected $name = '';
	protected $tmp_name  = '';
	protected $ext	= '';
	protected $type = '';
	protected $size = 0;
	
	public $old_name = '';
	
	protected function getFolderPath()
	{
		return self::format(static::$path . '/' . $this->folder . '/');
	}
	
	public static function setPath($path)
	{
		static::$path = CleanCodeDir::getAliasPath($path);
	}
	
	public function getFullPath()
	{
		return $this->getFolderPath() . $this->name;
	}
	
	public static function format($string)
	{
		return self::format_url($string, true);
	}
	
	public static function create_multiple($files, $callback, $extensions)
	{
		$return = array();
		
		for ($i = 0; $i < count($files['name']); $i++)
		{
			$file = new static($extensions);
			$file->setName($files['name'][$i]);
			$file->setTmpName($files['tmp_name'][$i]);
			$file->setType($files['type'][$i]);
			$file->setSize($files['size'][$i]);
			$file->send_if_not_exists();
			
			$return[] = $callback($file->getName(), $file->getError());
		}
		
		return $return;
	}
	
	function __construct($extensions)
	{
		$this->filter = explode('|', $extensions);
	}
	
	protected function setErrorByField($fieldError)
	{
		$this->setError('Invalid ' . $fieldError . '! ');
	}
	
	public function setFolder($folder)
	{
		$this->folder = $folder;
	}
	
	public function getName()
	{
		return $this->name? $this->name : $this->old_name;
	}
	
	public function setName($name)
	{
		$name = self::format($name);
		
		if($this->validate($name, self::FILE))
		{
			$parts = explode('.', $name);
			$this->name = $name;
			$this->ext = $parts[1];
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
		if(is_numeric($size) && $size) $this->size = $size;
		else $this->setErrorByField('size');
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function setType($type)
	{
		if($type) $this->type = $type;
		else $this->setErrorByField('type');
	}
	
	public function generate_name($size)
	{
		$info = explode('/', $this->type);
		
		if(count($info) == 2)
		{
			$filename = '';
			$this->ext = $info[1];
			
			while($filename == '' || file_exists($filename))
			{
				$filename = CC_Helper::getRandomString($size) . '.' . $info[1];
			}
			
			$this->name = $filename;
		}
	}
	
	protected function upload()
	{
		if(copy($this->tmp_name, $this->getFullPath()))
		{
			return true;
		}
		else
		{
			$this->setError('Erro ao fazer upload do arquivo!');
			return false;
		}
	}
	
	public function replace($oldName)
	{
		if($oldName != $this->name)
		{
			$this->trash = $this->getFolderPath() . $oldName;
		}
	}
	
	private function unlink_file()
	{
		if(file_exists($this->trash) && @unlink($this->trash))
		{
			return true;
		}
		else if($this->name)
		{
			$this->setError('Falha ao excluir o arquivo!');
			return false;
		}
	}
	
	public function delete()
	{
		$this->trash = $this->getFullPath();
		return $this->unlink_file();
	}
	
	public function send()
	{
		if($this->getError() || $this->size == 0 || $this->tmp_name == '' || file_exists($this->getFullPath()))
		{
			return false;
		}
		else if($this->upload())
		{
			if($this->trash) $this->unlink_file();
			return true;
		}
		else return false;
	}
	
	public function send_if_not_exists()
	{
		return file_exists($this->getFullPath()) || $this->send();
	}
}
?>
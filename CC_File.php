<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CC_Model.php';

class CC_File extends CC_Model
{
	protected static $path = '';
	
	private $folder = '';
	private $trash= '';
	
	protected $name = '';
	protected $tmp  = '';
	protected $ext	= '';
	protected $type = '';
	protected $size = 0;
	
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
			$this->ext = array_pop(explode('.', $name));
		}
	}
	
	public function load($data)
	{
		parent::load($data, true);
	}
	
	public function getTmpName()
	{
		return $this->tmp;
	}
	
	public function setTmpName($tmp)
	{
		$this->tmp = $tmp;
	}
	
	public function getSize()
	{
		return $this->size;
	}
	
	public function setSize($size)
	{
		if(is_numeric($size))
		{
			$this->size = $size;
		}
	}
	
	public function getType()
	{
		return $this->type;
	}
	
	public function setType($type)
	{
		$this->type = addslashes($type);
	}
	
	public function generateName($size)
	{
		$info = explode('/', $this->type);
		
		if(count($info) == 2)
		{
			$filename = '';
			$this->ext = $info[1];
			
			while($filename == '' || file_exists($filename))
			{
				$filename = self::getRandomString($size) . '.' . $info[1];
			}
			
			$this->name = $filename;
		}
	}
	
	protected function upload()
	{
		if(copy($this->tmp, $this->getFullPath()))
		{
			return true;
		}
		else
		{
			$this->setError('Não foi possível fazer upload do arquivo!');
			return false;
		}
	}
	
	public function replace($name)
	{
		$this->trash = static::$path . $this->folder . $name;
	}
	
	private function unlinkFile()
	{
		if(@unlink($this->trash))
		{
			return true;
		}
		else
		{
			$this->setError('Falha ao excluir o arquivo!');
			return false;
		}
	}
	
	public function delete()
	{
		$this->trash = $this->getFullPath();
		return $this->unlinkFile();
	}
	
	public function send()
	{
		if($this->trash) $this->unlinkFile();
		return $this->upload();
	}
	
	public function sendIfHave()
	{
		return $this->name? $this->send() : false;
	}
	
	public function download()
	{
		$filename = $this->getFullPath();
		
		if(file_exists($filename))
		{
			header('Content-Type: ' . $this->mimeType);
			header('Content-Length: ' . filesize($filename));
			header('Content-Disposition: attachment; filename='.basename($filename));
			readfile($arquivo);
			
			return true;
		}
		else
		{
			$this->setError('Arquivo "'.$filename.'" não encontrado!');
			return false;
		}
	}
}
?>
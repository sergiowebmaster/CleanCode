<?php
require_once 'CleanCodeFile.php';

class CleanCodeImage extends CleanCodeFile
{
	private static $defaultMaxDim = 1600;
	
	private $height = 0;
	private $width = 0;
	private $maxDim = 0;
	private $thumbs = array();
	
	function __construct($extensions = 'jpg|gif|png')
	{
		parent::__construct($extensions);
	}
	
	public function addThumb($maxDim, $folder)
	{
		$thumb = new self();
		$thumb->setPath($this->getFolderPath());
		$thumb->setMaxDim($maxDim);
		$thumb->setFolder($folder);
		
		$this->thumbs[$folder] = $thumb;
	}
	
	public function sendThumbs()
	{
		$sends = 0;
		
		foreach ($this->thumbs as $folder => $thumb)
		{
			$thumb->oldName = $this->oldName;
			$thumb->setName($this->name);
			$thumb->setTmpName($this->tmp_name);
			$thumb->setSize($this->size);
			$thumb->setType($this->type);
			
			if($thumb->send()) $sends++;
		}
		
		return $sends == count($this->thumbs);
	}
	
	public function deleteThumbs()
	{
		$sends = 0;
		
		foreach ($this->thumbs as $folder => $thumb)
		{
			$thumb->setName($this->name);
			
			if($thumb->delete()) $sends++;
		}
		
		return $sends == count($this->thumbs);
	}
	
	public static function setDefaultMaxDim($maxDim)
	{
		self::$defaultMaxDim = $maxDim;
	}
	
	private function getMaxDim()
	{
		return $this->maxDim? $this->maxDim : self::$defaultMaxDim;
	}
	
	public function setMaxDim($maxDimension)
	{
		$this->maxDim = $maxDimension;
	}
	
	private function resize($image)
	{
		if($this->width < $this->height)
		{
			$ratio	 = $this->width / $this->height;
			$height	 = $this->getMaxDim();
			$width	 = round($this->getMaxDim() * $ratio);
		}
		else
		{
			$ratio	 = $this->height / $this->width;
			$height	 = round($this->getMaxDim() * $ratio);
			$width	 = $this->getMaxDim();
		}
			
		$newImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
		
		return $newImage;
	}
	
	private function create()
	{
		$ext = $this->ext == 'jpg'? 'jpeg' : $this->ext;
		$create = 'imagecreatefrom' . $ext;
		$output = 'image'.$ext;
		
		if(is_callable($create) && is_callable($output))
		{
			$image = $create($this->tmp_name);
			$newImage = $this->resize($image);
			
			return $output($newImage, $this->getFullPath());
		}
		else
		{
			$this->setError('Tipo de imagem inválido!');
			return false;
		}
	}
	
	protected function upload()
	{
		list($this->width, $this->height) = getimagesize($this->tmp_name);
			
		if(($this->width > $this->getMaxDim() || $this->height > $this->getMaxDim()))
		{
			return $this->create();
		}
		else
		{
			return parent::upload();
		}
	}
	
	public function send()
	{
		return count($this->thumbs)? $this->sendThumbs() : parent::send();
	}
	
	public function delete()
	{
		return count($this->thumbs)? $this->deleteThumbs() : parent::delete();
	}
}
?>
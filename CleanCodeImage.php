<?php
require_once 'CleanCodeFile.php';

class CleanCodeImage extends CleanCodeFile
{
	private static $defaultMaxDim = 1600;
	
	private $height = 0;
	private $width = 0;
	private $maxDim = 0;
	private $images = array();
	
	function __construct($extensions = 'jpg|gif|png')
	{
		parent::__construct($extensions);
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
}
?>
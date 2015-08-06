<?php
require_once 'CC_File.php';

class CC_Image extends CC_File
{
	private $height  = 0;
	private $width	 = 0;
	private $maxDim  = 1600;
	
	public function setMaxDim($maxDimension)
	{
		$this->maxDim = $maxDimension;
	}
	
	private function resize()
	{
		if($this->width < $this->height)
		{
			$ratio	 = $this->width / $this->height;
			$height	 = $this->maxDim;
			$width	 = round($this->maxDim * $ratio);
		}
		else
		{
			$ratio	 = $this->height / $this->width;
			$height	 = round($this->maxDim * $ratio);
			$width	 = $this->maxDim;
		}
		
		$newImage = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($this->tmp);
			
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
			
		return imagejpeg($newImage, $this->getFullPath());
	}
	
	public function generateName($size, $ext = 'jpg')
	{
		parent::generateName($size, $ext);
	}
	
	protected function upload()
	{
		list($this->width, $this->height) = getimagesize($this->tmp);
		
		if($this->width > $this->maxDim || $this->height > $this->maxDim)
		{
			return $this->resize();
		}
		else
		{
			return parent::upload();
		}
	}
}
?>
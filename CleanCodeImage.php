<?php
require_once 'CleanCodeFile.php';

class CleanCodeImage extends CleanCodeFile
{
	private $width = 0;
	private $height = 0;
	private $maxWidth = 1600;
	private $maxHeigh = 1600;
	private $dst_x = 0;
	private $dst_y = 0;
	private $src_x = 0;
	private $src_y = 0;
	private $thumbs = array();
	private $cropParameters = array();
	
	function __construct($extensions = 'jpg|jpeg|gif|png')
	{
		parent::__construct($extensions);
	}
	
	public function setMaxWidth($pixels)
	{
	    if($pixels) $this->maxWidth = $pixels;
	}
	
	public function setMaxHeight($pixels)
	{
	    if($pixels) $this->maxHeigh = $pixels;
	}
	
	public function setMaxDim($pixels)
	{
	    $this->setMaxWidth($pixels);
	    $this->setMaxHeight($pixels);
	}
	
	public function crop($width, $height, $x = 0, $y = 0)
	{
	    if($width > 0 && $height > 0)
	    {
	        $this->cropParameters = array('x' => 0, 'y' => 0, 'width' => $width, 'height' => $height);
	    }
	}
	
	private function createThumb($folder, $maxWidth, $maxHeight, $cropWidth = 0, $cropHeight = 0, $cropX = 0, $cropY = 0)
	{
	    $thumb = new self();
	    $thumb->setPath($this->getFolderPath());
	    $thumb->setMaxWidth($maxWidth);
	    $thumb->setMaxHeight($maxHeight);
	    $thumb->setFolder($folder);
	    
	    return $thumb;
	}
	
	private function createCroppedThumb($folder, $maxWidth, $maxHeight, $cropWidth, $cropHeight, $cropX, $cropY)
	{
	    $thumb = $this->createThumb($folder, $maxWidth, $maxHeight);
	    $thumb->crop($cropWidth, $cropHeight, $cropX, $cropY);
	    
	    return $thumb;
	}
	
	public function addThumb($folder, $maxWidth, $maxHeight)
	{
	    $this->thumbs[$folder] = $this->createThumb($folder, $maxWidth, $maxHeight);
	}
	
	public function addCroppedThumb($folder, $maxWidth, $maxHeight, $cropWidth, $cropHeight, $cropX = 0, $cropY = 0)
	{
	    $this->thumbs[$folder] = $this->createCroppedThumb($folder, $maxWidth, $maxHeight, $cropWidth, $cropHeight, $cropX, $cropY);
	}
	
	public function sendThumbs()
	{
		$sends = 0;
		
		foreach ($this->thumbs as $thumb)
		{
			$thumb->oldName = $this->oldName;
			$thumb->setName($this->name);
			$thumb->setTmpName($this->tmp_name);
			$thumb->setSize($this->size);
			$thumb->setType($this->type);
			
			if($thumb->send()) $sends++;
		}
		
		return $sends == count($this->thumbs) && parent::send();
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
	
	private function setTransparency($newImage, $imageSource)
	{
	    $transparencyIndex = imagecolortransparent($imageSource);
	    $transparencyColor = array('red' => 255, 'green' => 255, 'blue' => 255);
	    
	    if($transparencyIndex >= 0)
	    {
	        $transparencyColor = imagecolorsforindex($imageSource, $transparencyIndex);
	    }
	    
	    $transparencyIndex = imagecolorallocate($newImage, $transparencyColor['red'], $transparencyColor['green'], $transparencyColor['blue']);
	    imagefill($newImage, 0, 0, $transparencyIndex);
	    imagecolortransparent($newImage, $transparencyIndex);
	}
	
	private function copyAndResampled($newImage, $imageSource, $width, $height)
	{
	    imagecopyresampled($newImage, $imageSource, $this->dst_x, $this->dst_y, $this->src_x, $this->src_y, $width, $height, $this->width, $this->height);
	}
	
	private function copyAndResampledTransparency($newImage, $imageSource, $width, $height)
	{
	    $this->setTransparency($newImage, $imageSource);
	    $this->copyAndResampled($newImage, $imageSource, $width, $height);
	}
	
	private function resize($imageSource, $transparency)
	{
	    $width	 = $this->width;
	    $height	 = $this->height;
	    
	    if ($width > $this->maxWidth)
	    {
	        $height = $height * ($this->maxWidth / $width);
	        $width = $this->maxWidth;
	    }
	    
	    if ($height > $this->maxHeigh)
	    {
	        $width = $width * ($this->maxHeigh / $height);
	        $height = $this->maxHeigh;
	    }
		
		$newImage = imagecreatetruecolor($width, $height);
		$transparency? $this->copyAndResampledTransparency($newImage, $imageSource, $width, $height) : $this->copyAndResampled($newImage, $imageSource, $width, $height);
		
		return $this->cropParameters? imagecrop($newImage, $this->cropParameters) : $newImage;
	}
	
	private function createJPEG()
	{
	    return imagejpeg($this->resize(imagecreatefromjpeg($this->tmp_name), false), $this->getFullPath());
	}
	
	private function createPNG()
	{
	    return imagepng($this->resize(imagecreatefrompng($this->tmp_name), true), $this->getFullPath());
	}
	
	private function createGIF()
	{
	    return imagegif($this->resize(imagecreatefromgif($this->tmp_name), true), $this->getFullPath());
	}
	
	private function createBMP()
	{
	    return imagebmp($this->resize(imagecreatefrombmp($this->tmp_name), false), $this->getFullPath());
	}
	
	private function create()
	{
		switch ($this->ext)
		{
		    case 'jpg':
		    case 'jpeg':
		        return $this->createJPEG();
		        break;
		        
		    case 'png':
		        return $this->createPNG();
		        break;
		        
		    case 'gif':
		        return $this->createGIF();
		        break;
		        
		    case 'bmp':
		        return $this->createBMP();
		        break;
		        
		    default:
		        return false;
		}
	}
	
	private function loadDimensions()
	{
	    list($this->width, $this->height) = getimagesize($this->tmp_name);
	}
	
	private function analyseDimensions()
	{
	    return ($this->width > $this->maxWidth || $this->height > $this->maxHeigh)? $this->create() : parent::upload();
	}
	
	protected function upload()
	{
		$this->loadDimensions();
		return $this->analyseDimensions();
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
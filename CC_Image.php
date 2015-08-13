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
		$ext = $this->ext == 'jpg'? 'jpeg' : $this->ext;
		$create = 'imagecreatefrom' . $ext;
		$output = 'image'.$ext;
		
		if(is_callable($create) && is_callable($output))
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
			$image = $create($this->tmp);
				
			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);
				
			return $output($newImage, $this->getFullPath());
		}
		else
		{
			return false;
		}
	}
	
	protected function upload()
	{
		if($this->tmp)
		{
			list($this->width, $this->height) = getimagesize($this->tmp);
			
			if(($this->width > $this->maxDim || $this->height > $this->maxDim) && $this->resize())
			{
				return true;
			}
			else
			{
				return parent::upload();
			}
		}
		else
		{
			$this->setError('O caminho temporário da imagem não foi encontrado!');
			return false;
		}
	}
}
?>
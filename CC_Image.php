<?php
require_once 'CC_File.php';

class CC_Image extends CC_File
{
	private $altura  = 0;
	private $largura = 0;
	private $maxDim  = 1600;
	private $thumbs  = array();
	
	public function setMaxDim($dimensaoMaxima)
	{
		$this->maxDim = $dimensaoMaxima;
	}
	
	public function addThumb($src, $maxDim)
	{
		$this->thumbs[] = array('src' => $src, 'maxDim' => $maxDim);
	}
	
	private function enviarRedimensionando($src, $maxDim)
	{
		if($this->largura < $this->altura)
		{
			$razao	 = $this->largura/$this->altura;
			$altura	 = $maxDim;
			$largura = round($maxDim * $razao);
		}
		else
		{
			$razao	 = $this->altura/$this->largura;
			$altura	 = round($maxDim * $razao);
			$largura = $maxDim;
		}
		
		$imagem_nova = imagecreatetruecolor($largura, $altura);
		$imagem 	 = imagecreatefromjpeg($this->tmp);
			
		imagecopyresampled($imagem_nova, $imagem, 0, 0, 0, 0, $largura, $altura, $this->largura, $this->altura);
			
		return imagejpeg($imagem_nova, self::getCaminho() . $src);
	}
	
	private function criarThumbs()
	{
		foreach ($this->thumbs as $thumb)
		{
			$this->enviarRedimensionando($thumb['src'], $thumb['maxDim']);
		}
	}
	
	protected function upload()
	{
		list($this->largura, $this->altura) = getimagesize($this->tmp);
		
		if($this->largura > $this->maxDim || $this->altura > $this->maxDim)
		{
			$enviou = $this->enviarRedimensionando($this->getNome(), $this->maxDim);
		}
		else
		{
			$enviou = parent::upload();
		}
		
		if($enviou)
		{
			$this->criarThumbs();
		}
		
		return $enviou;
	}
	
	private function excluirThumbs()
	{
		foreach ($this->thumbs as $thumb)
		{
			$arquivo = self::getCaminho() . $thumb['src'];
			
			if(file_exists($arquivo))
			{
				unlink($arquivo);
			}
		}
	}
	
	public function delete()
	{
		if(parent::delete())
		{
			$this->excluirThumbs();
			return true;
		}
		else {return false;}
	}
}
?>
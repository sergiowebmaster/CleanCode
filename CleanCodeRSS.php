<?php
require_once 'CleanCodeModel.php';

class CleanCodeRSS extends CleanCodeModel
{
	private $feeds = array();
	
	public function addFeeds($name, $url, $limit = null)
	{
		if($xml = @simplexml_load_file($url))
		{
			$i=1;
		
			if($xml->channel->item)
			{
				foreach($xml->channel->item as $item)
				{
					$this->feeds[$name][] = $item;
					
					if($limit && $i == $limit) {break;}
					else {$i++;}
				}
			}
		}
		else
		{
			$this->setError('Os feeds de "'.$url.'" não puderam ser carregados.');
		}
	}
	
	public function getFeeds()
	{
		return $this->feeds;
	}
}
?>
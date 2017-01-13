<?php
class CleanCodeDaoText extends CleanCodeDaoUri
{
	protected static $table = 'texts';
	
	public function getTitle()
	{
		return $this->get_column('title');
	}
	
	public function setTitle($title)
	{
		$this->set_column('title', $title, self::ALL, 1, 200);
		$this->setUri(self::parseURI($title));
	}
	
	public function getDescription()
	{
		return $this->get_column('description');
	}
	
	public function setDescription($description)
	{
		$this->set_column('description', $description, self::ALL, 1, 140);
	}
	
	public function getKeywords()
	{
		return $this->get_column('keywords');
	}
	
	public function setKeywords($keywords)
	{
		$this->set_column('keywords', $keywords, self::ALL, 1, 200);
	}
	
	public function getContent()
	{
		return $this->get_column('content');
	}
	
	public function setContent($content)
	{
		$this->set_column('content', $content, self::HTML, 1);
	}
}
?>
<?php
class CleanCodeEmail extends CleanCodeModel
{
	private $contentType = 'text/plain';
	
	protected $from_name = '';
	protected $from_email = '';
	protected $to_name = '';
	protected $to_email = '';
	protected $subject = '';
	protected $message = '';
	
	public $charset = 'UTF-8';
	
	public function useHTML()
	{
		$this->contentType = 'text/html';
	}
	
	public function getFromEmail()
	{
		return $this->from_email;
	}
	
	public function setFromEmail($email, $required = true)
	{
		if($this->validateData($email, self::EMAIL))
		{
			$this->from_email = $email;
		}
		else if($required)
		{
			$this->setErrorByField('from_email');
		}
	}
	
	public function getFromName()
	{
		return $this->from_name;
	}
	
	public function setFromName($name)
	{
		if($this->validateData($name, self::ALL, 1))
		{
			$this->from_name = $name;
		}
		else
		{
			$this->setErrorByField('from_name');
		}
	}
	
	public function from($email, $name)
	{
		$this->setFromEmail($email);
		$this->setFromName($name);
	}
	
	public function getToEmail()
	{
		return $this->to_email;
	}
	
	public function setToEmail($email)
	{
		if($this->validateData($email, self::EMAIL))
		{
			$this->to_email = $email;
		}
		else
		{
			$this->setErrorByField('to_email');
		}
	}
	
	public function getToName()
	{
		return $this->to_name;
	}
	
	public function setToName($name)
	{
		if($this->validateData($name, self::ALL, 1))
		{
			$this->to_name = $name;
		}
		else
		{
			$this->setErrorByField('to_name');
		}
	}
	
	public function to($email, $name)
	{
		$this->setToEmail($email);
		$this->setToName($name);
	}
	
	public function getSubject()
	{
		return $this->subject;
	}
	
	public function setSubject($subject, $required = true)
	{
		if($subject) $this->subject = $subject;
		else if($required) $this->setErrorByField('subject');
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	public function setMessage($message)
	{
		if($message) $this->message = $message;
		else $this->setErrorByField('message');
	}
	
	public function send()
	{
		$break = (PATH_SEPARATOR ==":")? "\r\n" : "\n";
		
		$headers  = "MIME-Version: 1.1" . $break;
		$headers .= "Content-type: " . $this->contentType . "; charset=" . $this->charset . $break;
		$headers .= "From: " . $this->from_name . ' <' . $this->from_email . '>' . $break;
		$headers .= "Return-Path: " . $this->from_email . $break;
		
		return $this->execute(
				$this->error == '' && mail($this->to_email	, $this->subject, $this->message, $headers, "-r" . $this->from_email),
				'E-mail enviado com sucesso!', 'Erro ao enviar e-mail.');
	}
}
?>
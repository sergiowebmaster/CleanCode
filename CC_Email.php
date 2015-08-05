<?php
/*
 *@author Sérgio Eduardo Pinheiro Gomes <sergioeduardo1981@gmail.com>
 */

require_once 'CC_Model.php';

class CC_Email extends CC_Model
{
	protected $to		= '';
	protected $name	= '';
	protected $email	= '';
	protected $subject	= '';
	protected $message	= '';
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
		if($this->validate($name, self::ALL))
		{
			$this->name = $name;
		}
		else
		{
			$this->setError('Preencha o nome do destinatário');
		}
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	
	public function setEmail($email)
	{
		if($this->validate($email, self::EMAIL))
		{
			$this->email = $email;
		}
		else
		{
			$this->setError('Informe o e-mail do destinatário!');
		}
	}
	
	public function getSubject()
	{
		return $this->subject;
	}
	
	public function setSubject($subject)
	{
		if($this->validate($subject, self::ALL))
		{
			$this->subject = $subject;
		}
		else
		{
			$this->setError('Informe o assunto!');
		}
	}
	
	public function getMessage()
	{
		return $this->message;
	}
	
	public function setMessage($message)
	{
		if($this->validate($message, self::ALL))
		{
			$this->message = $message;
		}
		else
		{
			$this->setError('Digite a mensagem!');
		}
	}
	
	protected function send()
	{
		$headers = 'From: '.$this->name.' <'.$this->email.'>';
		return mail($this->to, $this->subject, $this->message, $headers);
	}
}
?>
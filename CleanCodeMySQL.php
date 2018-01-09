<?php
class CleanCodeMySQL extends CleanCodeSQL
{
	function __construct($host, $database, $user, $password)
	{
		parent::__construct('mysql', $host, $database, $user, $password);
	}
}
?>
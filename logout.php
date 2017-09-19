<?php
require 'inc/core.php';
@setcookie('sid', '', time() - 3600, '/', $_SERVER['HTTP_HOST']);

if(isset($_GET['all']))
	{
		@mysql_query("UPDATE `users` SET `sid` = '' WHERE `sid` = '".mysql_real_escape_string($_COOKIE['sid'])."'");
	}
redirect('/');
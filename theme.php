<?php
$starttime = microtime();
require 'inc/core.php';
autOnly();

if(isset($_GET['theme']))
	{
		$theme = intval($_GET['theme']);
	}
else
	{
		$theme = 1;
	}

if(file_exists(R.'/style/css/bootstrap'.$theme.'.css'))
	{
		mysql_query("UPDATE `users` SET `theme` = ".$theme." WHERE `id` = ".$_INFO['id']);
		redirect('/?'.rand(1000,9999));
	}
else
	{
		redirect('/?err_unknown_theme');
	}
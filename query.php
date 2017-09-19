<?php
require_once 'inc/core.php';
autOnly();
ignore_user_abort(true);
set_time_limit(0);
if(isset($_POST['query']))
	{
		$query = str_replace('game.php?q=', '', $_POST['query']);
		
		$q = mysql_query("SELECT * FROM `accounts` WHERE `bot` = '1' ORDER BY `id` ASC");
		
		while($_ACC = mysql_fetch_assoc($q))
			{
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q='.$query);
				// прокси
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

				// всякая хуйня
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$a = curl_exec($ch);
			}
	}
else
	{
		redirect('/bots.php');
	}
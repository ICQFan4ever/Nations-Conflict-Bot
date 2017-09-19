<?php
require 'inc/core.php';

autOnly();

$q = mysql_query("SELECT * FROM `accounts` WHERE `bot` = '1'");

while($_ACC = mysql_fetch_assoc($q))
	{
		# просто главная
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php');
		// прокси
		curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
		// всякая хуйня
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
		curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 6);
		
		$a = curl_exec($ch);
		echo $a;
		
		preg_match("#\&id_building\=(\d+)\">Рынок</a>#u", $a, $res);
		$id_trade = isset($res[1]) ? (int)$res[1] : 0;
		
		echo $id_trade; // exit();
		flush();
		mysql_query("UPDATE `accounts` SET `trade_id` = ".$id_trade." WHERE `id` = ".$_ACC['id']) or die(mysql_error());
	}
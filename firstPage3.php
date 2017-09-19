<?php
require 'inc/core.php';

$q = mysql_query("SELECT * FROM `accounts` WHERE `refresh` = '1' LIMIT 58, 30");
// Определяем массив рандомно открываемых страниц

$randPages = array('settings', 'help', 'rating', 'forum', 'other', 'duty', 'extraction', 'union', 'build', 'command', 'diplomacy', 'calculator', 'support', 'mail', 'union_chat', 'archeology', 'achievement', 'support', 'seetings', 'settings&page=skin', 'forum&id_forum='.rand(1,4), 'form&id_forum='.rand(8,10), 'union_chat');
$c = count($randPages);

while($_ACC = mysql_fetch_assoc($q))
	{
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q='.$randPages[rand(0, $c - 1)]);
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
		echo $_ACC['name'].' - Success!<br />';
	}
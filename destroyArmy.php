<?php
$starttime = microtime();
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$id);
if(mysql_num_rows($q) == 1)
	{
		$_ACC = mysql_fetch_assoc($q);
		if($_ACC['id_user'] != $_INFO['id'])
			{
				$q2 = mysql_query("SELECT * FROM `access_list` WHERE `id_user` = ".$_INFO['id']." AND `acc_id` = ".$id);
				if(mysql_num_rows($q2) < 1)
					{
						redirect('/?no_access=1');
					}
			}
	}
else
	{
		redirect('/?not_found');
	}

// открываем войска и смотрим список страниц
// Открываем локацию
$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=command');
// прокси
curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
// всякая хуйня
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$a = curl_exec($ch);
preg_match_all("#p_units=(\d+)#u", $a, $arr);
if(!isset($arr[1]))
	{
		$max = 1;
	}
else
	{
		$max = max($arr[1]);
	}

// циклом открываем все страницы разом, НАХУЙ
$output = '';

for($i = 1; $i <= $max; $i++)
	{
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=command&p_units='.$i);
		// прокси
		curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
		// всякая хуйня
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
		curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$a = curl_exec($ch);
		$output .= $a;
	}

echo $output;
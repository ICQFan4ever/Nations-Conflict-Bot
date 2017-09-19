<?php
require 'inc/core.php';
$_DEBUG = true;
$array = array();

if(isset($_GET['id']))
	{
		$array[] = (int)$_GET['id'];
	}
else
	{
		$q = mysql_query("SELECT * FROM `accounts` WHERE `autologin` = 1 AND `bot` = 0");
		while($asd = mysql_fetch_assoc($q))
			{
				$array[] = $asd['id'];
			}
	}

foreach($array as $acc_id)
	{
		$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$acc_id);
		if(mysql_num_rows($q) == 1)
			{
				$_ACC = mysql_fetch_assoc($q);
				# работаем
				
				# зайдем на страницу логина и посмотрим, какие печеньки нам дадут
				
				$ch = curl_safe_init('http://wap.mgates.ru/login/');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_REFERER, 'http://wap.mgates.ru/');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				$a = curl_exec($ch);
				echo $_DEBUG ? $a : '';
				preg_match("#PHPSESSID\=([a-z0-9]{1,})\;#", $a, $arr1);
				preg_match("#time1\=(\d+)\;#", $a, $arr2);
				$phpsessid = trim($arr1[1]);
				$time1 = trim($arr2[1]);
				
				# Непосредственно авторизуемся
				
				$ch = curl_safe_init('http://wap.mgates.ru/login/');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_REFERER, 'http://wap.mgates.ru/login/');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$phpsessid.';time1='.$time1.';');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('login' => $_ACC['login'], 'password' => $_ACC['pass'], 'action' => 'login', 'enter' => 'Войти'));
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				$a = curl_exec($ch);
				echo $_DEBUG ? $a : '';
				preg_match("#encrypted\=([0-9a-z]{1,})\;#", $a, $arr1);
				preg_match("#tid\=(\d+)\;#", $a, $arr2);
				preg_match("#gid\=(\d+)\;#", $a, $arr3);
				preg_match("#id\=(\d+)\;#", $a, $arr4);
				preg_match("#pid\=(\d+)\;#", $a, $arr5);
				$encrypted = $arr1[1];
				$tid = $arr2[1];
				$gid = $arr3[1];
				$id = $arr4[1];
				$pid = $arr5[1];
				
				
				# Пытаемся запилиться в игру
				
				$ch = curl_safe_init('http://wap.mgates.ru/game/i20/play/');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_REFERER, 'http://wap.mgates.ru/');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$phpsessid.';time1='.$time1.';encrypted='.$encrypted.';tid='.$tid.';gid='.$gid.';id='.$id.';pid='.$pid.';');
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				$a = curl_exec($ch);
				echo $_DEBUG ? $a : '';
				
				# Получаем URL для входа
				preg_match("#http\:\/\/nations.mgates.ru\/\?sid\=([a-z0-9]{1,})#", $a, $arr1);
				# куки, как обычно
				preg_match("#PHPSESSID\=([a-z0-9]{1,})\;#", $a, $arr2);
				preg_match("#Set-Cookie\: enc\=([\-a-zA-Z0-9\%]{1,})#u", $a, $arr3);
				$phpsessid = trim($arr2[1]);
				$enc = trim($arr3[1]);
				
				
				# Все followlocation пропустили, заходим в игру =)
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?promo=1&');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_REFERER, $arr1[1]);
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$phpsessid.';enc='.$enc.';');
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				$a = curl_exec($ch);
				echo $_DEBUG ? $a : '';
				if(mysql_query("UPDATE `accounts` SET `phpsessid` = '".$phpsessid."', `enc` = '".$enc."' WHERE `id` = ".$_ACC['id']))
					{
						if(isset($_GET['redirect']) && !isset($_DEBUG))
							{
								redirect('/?update=1');
							}
						echo $_ACC['name'].' = success<br />
						<a href="/" style="font-size: 24px;">На главную</a>';
					}
				else
					{
						die(mysql_error());
					}
			}
		else
			{
				echo 'Not Found<br />';
			}
	}
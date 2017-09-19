<?php
require 'inc/core.php';

writeLog(0,0,'cron', 'Скрипт выполнения заданий успешно запущен');
# храм

$q = mysql_query("SELECT * FROM `task_cathedral` WHERE `status` = '1'");
while($task = mysql_fetch_assoc($q))
	{
		$time = parseTimeRange($task['range']);
		$now = date('i');
		$now = (int)$now;
		if(isset($time[$now]))
			{
				# получаем инфу об аккаунте
				$_ACC = mysql_fetch_assoc(mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$task['acc_id']));
				
				
				# Формируем CURL-запрос
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$task['id_cathedral']);
				// прокси
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				// пытаемся начать молитву
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('cnf' => '1', 'c' => 'Молиться!'));
				// всякая хуйня
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				$a = curl_exec($ch);
				echo $a;
				echo '<hr />';
				writeLog($_ACC['id_user'], $_ACC['id'], 'task_cathedral', 'Попытка запуска задания молитвы для аккаунта '.$_ACC['name']);
			}
	}


# мастерская

$q = mysql_query("SELECT * FROM `task_mast` WHERE `status` = '1'");
while($task = mysql_fetch_assoc($q))
	{
		$time = parseTimeRange($task['range']);
		//print_r($time);
		$now = date('i');
		$now = (int)$now;
		if(isset($time[$now]))
			{
				# получаем инфу об аккаунте
				$_ACC = mysql_fetch_assoc(mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$task['acc_id']));
				# Формируем CURL-запрос
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$task['id_mast'].'&selected_tab=start&unit_group=2&id_pattern='.$task['id_pattern'].'&no_build_info=1');
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('amount' => $task['amount'], 'make_order' => 'Заказать!')));
				
				curl_setopt($ch, CURLOPT_REFERER, 'http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$task['id_mast'].'&selected_tab=start&id_pattern='.$task['id_pattern'].'&unit_group=2');
				#die('http://nations.mgates.ru/conflict/game.php?q=union&a=2&id='.$task['id_building'].'&amp;ac='.$task['action'].'&cnf=1');
				$a = curl_exec($ch);
				echo $a;
				echo '<hr />';
				writeLog($_ACC['id_user'], $_ACC['id'], 'task_mast', 'Попытка запуска производства. Мастерская '.$task['id_mast'].', чертеж '.$task['id_pattern'].', кол-во '.$task['amount'].'');
			}
	}

# ресурсы
$q = mysql_query("SELECT * FROM `task_res` WHERE `status` = '1'");
while($task = mysql_fetch_assoc($q))
	{
		$time = parseTimeRange($task['range']);
		echo '<pre>';
		print_r($time);
		$now = date('i');
		$now = (int)$now;
		if(isset($time[$now]))
			{
				# получаем инфу об аккаунте
				$_ACC = mysql_fetch_assoc(mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$task['acc_id']));
				# curl
				
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=union&a=2&id='.$task['id_building'].'&ac='.$task['action'].'&cnf=1');
				curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
				curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
				curl_setopt($ch, CURLOPT_REFERER, 'http://nations.mgates.ru/conflict/game.php?q=union&a=2&id='.$task['id_building'].'&ac='.$task['action'].'&cnf=1');
				
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, array('res_'.$task['id_res'] => $task['amount'], ));
				#die('http://nations.mgates.ru/conflict/game.php?q=union&a=2&id='.$task['id_building'].'&amp;ac='.$task['action'].'&cnf=1');
				$a = curl_exec($ch);
				echo $a;
				echo '<hr />';
				writeLog($_ACC['id_user'], $_ACC['id'], 'task_res', 'Задание на ресурсы, действие: '.$task['action'] == 1 ? 'положить' : 'взять'.', ресурс: '.$task['id_res'].', кол-во: '.$task['amount']);
			}
	}
<?php
/*$starttime = microtime();
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$id);
if(mysql_num_rows($q) == 1)
	{
		$_ACC = mysql_fetch_assoc($q);
		if($_ACC['bot'] == 0)
			{
				if($_ACC['id_user'] != $_INFO['id'])
					{
						# проверка на расшаренный доступ
						$q2 = mysql_query("SELECT * FROM `access_list` WHERE `id_user` = ".$_INFO['id']." AND `acc_id` = ".$id);
						if(mysql_num_rows($q2) < 1)
							{
								redirect('/?no_access=1');
							}
					}
			}
	}

else
	{
		redirect('/?not_found');
	}

if(isset($_GET['rem_not']))
	{
		mysql_query("UPDATE `accounts` SET `notification` = '', `notification_url` = '' WHERE `id` = ".$_ACC['id']);
		$_ACC['notification'] = '';
		$_ACC['notification_url'] = '';
	}
//die(print_r($_ACC));

// убираем лишнее из query
$query = str_replace('?acc_id='.$_ACC['id'], '?' , $_SERVER['QUERY_STRING']);
$query = str_replace('&acc_id='.$_ACC['id'], '&' , $_SERVER['QUERY_STRING']);
$query = str_replace('&amp;acc_id='.$_ACC['id'], '&amp;' , $_SERVER['QUERY_STRING']);
$query = str_replace('&rem_not=1', '', $_SERVER['QUERY_STRING']);
*/
$ch = curl_safe_init('http://nations.mgates.ru/conflict/dig_direction.php?'.$_SERVER['QUERY_STRING']);

// прокси
//curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

// всякая хуйня
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
//curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
//curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);

$a = curl_exec($ch);

### сэйвим файл, для примера
$idd = (int)$_GET['id_unit'];

$name = $idd.'.gif';
$ff = fopen('imgs/'.$name, 'w');
flock($ff, LOCK_EX);
fwrite($ff, $a);
flock($ff, LOCK_UN);
fclose($ff);




header("Content-type: image/gif");
echo $a;
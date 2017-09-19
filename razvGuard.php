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
if(isset($_GET['force']))
{
		$query = 'SELECT * FROM `razv` WHERE `acc_id` = '.$_ACC['id'].' "ORDER BY `id` ASC';
}
else
{
		$query = 'SELECT * FROM `razv` WHERE `acc_id` = '.$_ACC['id'].' AND `guard` = \'0\' ORDER BY `id` ASC';
}

$q = mysql_query($query);
while($razv = mysql_fetch_assoc($q))
	{
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?acc_id=1&q=control&id_unit='.$razv['id_unit'].'&action_id=3');
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
		
		// echo $a;
		
		if(preg_match("#Такой боевой единицы нет#u", $a))
			{
				mysql_query("DELETE FROM `razv` WHERE `id` = ".$razv['id']);
			}
		else
			{
				mysql_query("UPDATE `razv` SET `guard` = '1' WHERE `id` = ".$razv['id']);
			}
	}
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

$x = isset($_GET['x']) ? (int)$_GET['x'] : 5;
$y = isset($_GET['y']) ? (int)$_GET['y'] : 5;

# Идем, идем
$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=map&x='.$x.'&y='.$y);
curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

// всякая хуйня
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// curl_setopt($ch, CURLOPT_TIMEOUT, 12);
								
$a = curl_exec($ch);


preg_match_all("#id\_location\=(\d+)\"><img src\=\"ci/(?:[a-z0-9]{1,})3l6d5v1\.jpg\"#u", $a, $arr);
echo '<pre>';
//print_r($arr[1]); die;


foreach($arr[1] as $id_loc)
	{
		$x = ceil($id_loc / 200);
		$y = $id_loc - ($x - 1) * 200;
		
		echo $x.':'.$y.'<br />';
	}

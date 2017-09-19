<?php
$starttime = microtime();
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

if(!empty($_ACC['notification']) && !empty($_ACC['notification_url']))
	{
		mysql_query("UPDATE `accounts` SET `notification` = '', `notification_url` = '' WHERE `id`=".$_ACC['id']);
		redirect($_ACC['notification_url']);
	}
else
	{
		redirect('/?no_notification');
	}
<?php
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

if($id == 26)
	{
		fatalError('Невозможно удалить тестовый аккаунт');
	}

$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$id." AND `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q) != 1)
	{
		$_ACC = mysql_fetch_assoc($q);
		if($_ACC['bot'] != 1)
			{
				redirect('/?err_no_access');
			}
	}
else
	{
		$_ACC = mysql_fetch_assoc($q);
	}

if(mysql_query("DELETE FROM `accounts` WHERE `id` = ".$id))
	{
		redirect('/?success_del_acc');
	}
else
	{
		fatalError(mysql_error());
	}
<?php
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$id." AND `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q) != 1)
	{
		redirect('/?err_no_access');
	}
else
	{
		$_ACC = mysql_fetch_assoc($q);
	}

$action = $_ACC['status'] == 0 ? 1 : 0;


if(mysql_query("UPDATE `accounts` SET `status` = '".$action."' WHERE `id` = ".$id))
	{
		redirect('/?success_status_change');
	}
else
	{
		fatalError(mysql_error());
	}
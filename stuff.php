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


setTitle('Скрипты');
getHeader();
?>

<a href="/razv.php?acc_id=<?=$_ACC['id']?>">Разведка</a><br />
Слив в яму: <a href="/catacomb.php?acc_id=<?=$_ACC['id']?>">[1;1]</a> / <a href="/catacomb.php?acc_id=<?=$_ACC['id']?>&hole=40000">[200;200]</a><br />


<?php
getFooter();
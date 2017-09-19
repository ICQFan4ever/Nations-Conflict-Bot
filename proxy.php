<?php
require_once 'inc/core.php';
autOnly();
setTitle('Прокси');
getHeader();

if(isset($_GET['take']))
	{
		$take = (int)$_GET['take'];
		if(mysql_query("UPDATE `proxy` SET `used` = 1 WHERE `id` = ".$take))
			{
				// 
			}
		else
			{
				die(mysql_error());
			}
	}

?>


<div class="alert alert-info" role="alert">Все свободные прокси из имеющейся базы. Удаляются автоматически при их использовании для аккаунта.</div>

<?php
$q = mysql_query("SELECT * FROM `proxy` AS ip1 WHERE `used` = 0 AND NOT EXISTS (SELECT 0 FROM `accounts` as ip2 WHERE `ip2`.`proxy_ip` = `ip1`.`ip`) ORDER BY `ip` DESC");
$c = mysql_num_rows($q);
?>Всего доступно: <?=$c?><br />

<?php
while($server = mysql_fetch_assoc($q))
	{
		?>
<pre>
IP: <?=$server['ip']?><br />
Порт: <?=$server['port']?><br />
Пользователь: <?=$server['login']?><br />
Пароль: <?=$server['pass']?><br />
<a href="/proxy.php?take=<?=$server['id']?>">Забрал!</a>
</pre>
		<?php
	}

getFooter();
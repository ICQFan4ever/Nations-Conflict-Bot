<?php
require 'inc/core.php';

autOnly();

if(isset($_GET['export']))
	{
		header('Content-disposition: attachment; filename=conf_log.txt');
		header('Content-type: text/plain');
		$q = mysql_query("SELECT * FROM `log` ORDER BY `time` DESC");
		while($inf = mysql_fetch_assoc($q))
			{
				echo date("d.m.Y H:i:s", $inf['time']).", тип ".$inf['type'].", пользователь №".$inf['id_user'].", аккаунт №".$inf['acc_id']."\r\n".$inf['text']."\r\n\r\n";
			}
		exit;
	}

setTitle('Лог');
getHeader();

if(isset($_GET['type']))
	{
		$type = mysql_real_escape_string($_GET['type']);
		$q = mysql_query("SELECT * FROM `log` WHERE `type` = '".$type."' ORDER BY `time` DESC LIMIT 100");
		$c = mysql_num_rows($q);
	}
else
	{
		$q = mysql_query("SELECT * FROM `log`ORDER BY `time` DESC LIMIT 100");
		$c = mysql_num_rows($q);
	}
?>
<button class="btn btn-default" onclick="window.location = '/log.php?export=1';">Экспорт в TXT</button><br />
<a href="/log.php">Все</a> / <a href="/log.php?type=task_cathedral">Храм</a> / <a href="/log.php?type=task_mast">Мастерская</a> / <a href="/log.php?type=task_res">Ресурсы</a> / <a href="/log.php?type=game">Игровые</a> / <a href="/log.php?type=cron">crontab</a><br /><br /> 
	
<?php
if(isset($_GET['search']))
	{
		$search = mysql_real_escape_string($_GET['search']);
		$q = mysql_query("SELECT * FROM `log` WHERE `text` LIKE '%".$search."%".(isset($type) ? " AND `type` = '".$type."'" : '')." ORDER BY `time` DESC LIMIT 100");
		while($inf = mysql_fetch_assoc($q))
			{
				echo '<b>'.date('d.m.Y H:i:s', $inf['time']).'</b>, тип <b>'.$inf['type'].'</b>, <b>'.$inf['id_user'].':'.$inf['acc_id'].'</b><br />'.$inf['text'].'<br /><br />'.PHP_EOL;
			}
		echo '<br /><br />';
	}
?>

<form action="/log.php" method="post">
<input type="text" name="search" placeholder="Поиск" class="form-control" />
<?=isset($type) ? '<input type="hidden" name="type" value="'.$type.'" />' : ''?>
<button class="btn btn-default">Искать</button>
</form><br /><br />

<?

while($inf = mysql_fetch_assoc($q))
	{
		echo '<b>'.date('d.m.Y H:i:s', $inf['time']).'</b>, тип <b>'.$inf['type'].'</b>, <b>'.$inf['id_user'].':'.$inf['acc_id'].'</b><br />'.$inf['text'].'<br /><br />'.PHP_EOL;
	}

getFooter();
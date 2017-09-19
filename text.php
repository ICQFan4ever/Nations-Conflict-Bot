<?php
$starttime = microtime();
require 'inc/core.php';
autOnly();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$q = mysql_query("SELECT * FROM `text` WHERE `id` = ".$id);
if(mysql_num_rows($q) != 1)
	{
		fatalError('Не найдено');
	}
$text = mysql_fetch_assoc($q);
	
setTitle('Текст');
getHeader();

echo $text['text'];


getFooter();
<?php
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$id);
if(mysql_num_rows($q) == 1)
	{
		$_ACC = mysql_fetch_assoc($q);
		if($_ACC['id_user'] != $_INFO['id'])
			{
				redirect('/?no_access=1');
			}
	}
else
	{
		redirect('/?not_found');
	}

if(isset($_POST['button']))
	{
		// print_r($_POST);
		// die();
		if(isset($_POST['refresh']))
			{
				if($_POST['refresh'])
					{
						$refresh = '1';
					}
				else
					{
						$refresh = '0';
					}
			}
		else
			{
				$refresh = '0';
			}
		
		if(isset($_POST['archeology']))
			{
				if($_POST['archeology'])
					{
						$archeology = '1';
					}
				else
					{
						$archeology = '0';
					}
			}
		else
			{
				$archeology = '0';
			}
		
		if(isset($_POST['autologin']))
			{
				if($_POST['autologin'])
					{
						$autologin = '1';
					}
				else
					{
						$autologin = '0';
					}
			}
		else
			{
				$autologin = '0';
			}
		
		if(mysql_query("UPDATE `accounts` SET `autologin` = '".$autologin."', `refresh` = '".$refresh."', `archeology` = '".$archeology."' WHERE `id` = '".$_ACC['id']."'"))
			{
				redirect('/?success_options');
			}
		else
			{
				fatalError(mysql_error());
			}
	}

setTitle('Опции');
getHeader();
?>

<form action="options.php?acc_id=<?=$_ACC['id']?>" method="post">
<div class="alert alert-info">&quot;Автологин&quot; будет выполнять повторный вход в аккаунт каждые 30 минут. Это обеспечивает высокий процент вероятности выполнения всех скриптов (онлайн, задания, слив в яму и прочее).<br />
Опция &quot;Онлайн&quot; включает накрутку онлайна, путем открытия произвольных страниц каждую минуту. Возможны потери до 5-10% (зависит от текущей нагрузки сервера и качества прокси)<br />
&quot;Арехология&quot; упрощает процесс раскопок: каждые 10 минут скрипт находит ваши арехологические экспедиции, пытается начать раскопки, если это невозможно - ориентируется на то, в каком направлении надо идти. Если отряд находится в засаде / на охране, скрипт снимет их оттуда. Если экспедиция уже движется на раскопки, скрипт ее остановит и отправит повторно, ориентируясь на новое направление. <b>Внимание</b>: скрипт работает исключительно с отрядами. Армии, которыми выбили карты, необходимо расформировать.</div>

<input type="checkbox" name="autologin" <?=$_ACC['autologin'] == 1 ? 'checked="checked" /' : '/'?>>&nbsp;Автовход<br />
<input type="checkbox" name="refresh" <?=$_ACC['refresh'] == 1 ? 'checked="checked" /' : '/'?>>&nbsp;Онлайн<br />
<input type="checkbox" name="archeology" <?=$_ACC['archeology'] == 1 ? 'checked="checked" /' : '/'?>>&nbsp;Археология<br />

<input type="submit" name="button" value="Сохранить" class="btn btn-success" /></form>

<?php
getFooter();
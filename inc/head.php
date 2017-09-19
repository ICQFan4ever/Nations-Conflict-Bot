<?php
if(defined('TITLE'))
	{
		$_title = TITLE;
	}
else
	{
		$_title = 'NC';
	}

if(AUT)
	{
		$theme = $_INFO['theme'];
	}
else
	{
		$theme = 1;
	}

?>
<!DOCTYPE html>
<html>
<head>
<title><?=$_title?></title>
<meta name="description" content="<?=$_descr?>" />
<meta name="keywords" content="<?=$_key?>" />
<meta name="revisit-after" content="1 day" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="/style/css/bootstrap<?=$theme?>.css" />
<link rel="stylesheet" type="text/css" href="/style/css/user.css" />
<script src="https://yastatic.net/jquery/2.1.1/jquery.min.js"></script>
<script src="/style/js/bootstrap.min.js"></script>
</head>

<body style="padding: 3px;">
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/"><?= AUT ? 'Nations Conflict' : 'Nation Conflict'?></a>
		</div>
		
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
			<?=AUT ? '
			<li class=""><a href="/">Все аккаунты</a></li>
			<li class=""><a href="/add.php">Добавить</a></li>
			<li class=""><a href="/tasks.php">Задания</a></li>
			<li class=""><a href="/access.php">Доступ</a></li>
			<!--<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Боты<span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li class=""><a href="/bots.php">Боты</a></li>
					<li class=""><a href="/buy.php">Слив арм</a></li>
				</ul>
			</li>-->
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Разное<span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li class=""><a href="/chat.php">Чат</a></li>
					<li class=""><a href="/archlog.php">Лог археологии</a></li>
					<li class=""><a href="/system.php">Загрузка сервера</a></li>
					<li class=""><a href="/proxyStatus.php">Состояние PROXY</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Выход<span class="caret"></span></a><ul class="dropdown-menu" role="menu"><li><a href="/logout.php">С этого устройства</a></li><li><a href="/logout.php?all=1">Со всех устройств</a></li></ul>
			</li>' : '<li class=""><a href="/login.php">Вход</a></li>
			<li class=""><a href="/about.php">Что это?</a></li>'?>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<div style="max-width: 90%; margin: auto;">
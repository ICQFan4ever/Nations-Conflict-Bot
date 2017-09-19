<?php
require 'inc/core.php';
autOnly();
if($_INFO['bot_access'] != '1')
{
	fatalError('Доступ запрещен');
}
setTitle('Глагне');
getHeader();



echo $_SERVER['REQUEST_URI'];

$q = mysql_query("SELECT * FROM `accounts` WHERE `bot` = 1 ORDER BY `id` ASC");
$c = mysql_num_rows($q);
?><h4>Боты <small>(<?=$c?>)</small></h4>
<a href="/autoLoginBots.php" onclick="return confirm('ВНИМАНИЕ!!! Занимает очень много времени и увеличивает нагрузку. Продолжить?');">Обновить все аккаунты</a><br />

<form action="/query.php" method="post">
Произвольный запрос:<br />
<input type="text" name="query" value="" placeholder="game.php?q=" class="form-control" /><input type="submit" class="btn btn-primary btn-small" name="button" value="Выполнить" /></form><br />

<?php
while($_ACC = mysql_fetch_assoc($q))
	{
		?>
		<div class="panel panel-default" id="acc<?=$_ACC['id']?>">
			<div class="panel-body">
				<a href="/game.php?acc_id=<?=$_ACC['id']?>" style="font-size: larger;"><?=$_ACC['name']?></a> (by <i>SystemBot</i>)
				
				<div class="btn-group">
					<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
						Действия <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
					
						<li><a href="/autoLogin.php?id=<?=$_ACC['id']?>&redirect=1">Повторный вход</a></li>
						<li><a href="/edit.php?acc_id=<?=$_ACC['id']?>">Редактировать</a></li>
						<li><a href="/parseGameInfo.php?acc_id=<?=$_ACC['id']?>" onclick="return confirm('Провоцирует высокую нагрузку. Запустить?');">Обновить информацию игры</a></li>
						<li><a href="/delete.php?id=<?=$_ACC['id']?>" onclick="return confirm('Подтвердите удаление');">Удалить</a>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

getFooter();
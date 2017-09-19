<?php
require 'inc/core.php';
autOnly();
setTitle('Глагне');
getHeader();

if(@$_GET['add'] == 1)
	{
		echo good('Аккаунт добавлен. Необходимо выполнить его авторизацию');
	}

if(@$_GET['update'] == 1)
	{
		echo good('Авторизация выполнена');
	}
$q = mysql_query("SELECT * FROM `accounts` WHERE `id_user` = ".$_INFO['id']." AND `bot` = 0 ORDER BY `id` ASC");
?><h4>Мои аккаунты</h4>
<?php
while($_ACC = mysql_fetch_assoc($q))
	{
		?>
		<div class="panel panel-default">
			<div class="panel-body">
				<a href="/game.php?acc_id=<?=$_ACC['id']?>" style="font-size: larger;"><?=$_ACC['name']?></a><br />
				
				<div class="btn-group">
					<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
						Действия <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
					
					<li><a href="/autoLogin.php?id=<?=$_ACC['id']?>&redirect=1">Повторный вход</a></li>
					<li><a href="/parseGameInfo.php?acc_id=<?=$_ACC['id']?>" onclick="return confirm('Провоцирует высокую нагрузку. Запустить?');">Обновить информацию игры</a></li>
					<li><a href="/options.php?acc_id=<?=$_ACC['id']?>">Включенные опции</a></li>
					<li><a href="/edit.php?acc_id=<?=$_ACC['id']?>">Изменить</a></li>
					<li><a href="/delete.php?acc_id=<?=$_ACC['id']?>" onclick="return confirm('Удалить?');">Удалить</a></li>
					
					</ul>
					
				</div>
				
				<div class="btn-group">
					<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
						Скрипты <span class="caret"></span>
					</button>
					
					<ul class="dropdown-menu" role="menu">
					<li><a href="/razv.php?acc_id=<?=$_ACC['id']?>">Разведка</a></li>
					<li><a href="/province.php?acc_id=<?=$_ACC['id']?>">Срез провинций</a></li>
					<li><a href="/catacomb.php?acc_id=<?=$_ACC['id']?>">Слив в яму [1;1]</a></li>
					<li><a href="/catacomb.php?acc_id=<?=$_ACC['id']?>&hole=40000">Слив в яму [200;200]</a></li>
					<li><a href="/destroy.php?acc_id=<?=$_ACC['id']?>">Расформировать отряды</a></li>
					</ul>
				</div>
				<br /><i style="font-size: x-small;">Слито отрядов: <?=$_ACC['unit_hole']?>; arm получено: <?=$_ACC['arm_hole']?>; ресурсов получено: <?=$_ACC['res_hole']?></i>
				<?=empty($_ACC['notification']) ? '' : '<br /><i style="font-size: x-small;">* Новое <a href="/followNotification.php?acc_id='.$_ACC['id'].'">уведомление</a></i>'?>
			</div>
		</div>
		<?php
	}

$q = mysql_query("SELECT * FROM `access_list` WHERE `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q) > 0)
	{
		echo '<h4>Общие аккаунты</h4>';
		while($__inf = mysql_fetch_assoc($q))
			{
				$q2 = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$__inf['acc_id']." AND `bot` = 0");
				$_ACC = mysql_fetch_assoc($q2);
				$q3 = mysql_query("SELECT * FROM `users` WHERE `id` = ".$_ACC['id_user']);
				$_USER = mysql_fetch_assoc($q3);
				?>
				<div class="panel panel-default">
					<div class="panel-body">
						<a href="/game.php?acc_id=<?=$_ACC['id']?>" style="font-size: larger;"><?=$_ACC['name']?></a> <i style="font-size: x-small">(<?=$_USER['login']?>)</i><br />
						<div class="btn-group">
							<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
								Действия <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
							
								<li><a href="/autoLogin.php?id=<?=$_ACC['id']?>&redirect=1">Повторный вход</a></li>
								<li><a href="/parseGameInfo.php?acc_id=<?=$_ACC['id']?>" onclick="return confirm('Провоцирует высокую нагрузку. Запустить?');">Обновить информацию игры</a></li>
							
							</ul>
						</div>
						
										<div class="btn-group">
											<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
												Скрипты <span class="caret"></span>
											</button>
											
											<ul class="dropdown-menu" role="menu">
											<li><a href="/razv.php?acc_id=<?=$_ACC['id']?>">Разведка</a></li>
											<li><a href="/catacomb.php?acc_id=<?=$_ACC['id']?>">Слив в яму [1;1]</a></li>
											<li><a href="/catacomb.php?acc_id=<?=$_ACC['id']?>&hole=40000">Слив в яму [200;200]</a></li>
											</ul>
										</div>
						<br /><i style="font-size: x-small;">Слито отрядов: <?=$_ACC['unit_hole']?>; arm получено: <?=$_ACC['arm_hole']?>; ресурсов получено: <?=$_ACC['res_hole']?></i>
						<?=empty($_ACC['notification']) ? '' : '<br /><i style="font-size: x-small;">* Новое <a href="/followNotification.php?acc_id='.$_ACC['id'].'">уведомление</a></i>'?>
					</div>
				</div>
				<?php
			}
	}
?>

<iframe src="/chat.php?widget=1" style="width: 100%; max-height: 300px; padding: 5px; border: none;">Err</iframe>

<?php

getFooter();
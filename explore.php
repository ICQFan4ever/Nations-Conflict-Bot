<?php
require 'inc/core.php';
autOnly();

if(isset($_GET['button']))
	{
		$x = $_GET['x'] >= 1 && $_GET['x'] <= 200 ? (int)$_GET['x'] : 0;
		$y = $_GET['y'] >= 1 && $_GET['y'] <= 200 ? (int)$_GET['y'] : 0;
		
		$q = mysql_query("SELECT * FROM `razv` WHERE `x` = ".$x." AND `y` = ".$y." LIMIT 1");
		
		if(mysql_num_rows($q) != 1)
			{
				$error[] = 'На этом квадрате нет разведки. Попробуйте выбрать соседние';
			}
		else
			{
				$inf = mysql_fetch_assoc($q);
				
				redirect('/game.php?acc_id=1&q=control&id_unit='.$inf['id_unit']);
			}
	}

setTitle('Разведка');
getHeader();

formError(isset($error) ? $error : '');
?>

<div class="alert alert-info">Если выдается ошибка &quot;Такой боевой единицы нет&quot;, значит какой-то пидорас сломал маяк</div>

<div class="panel panel-default">
	<div class="panel-body">
		<form class="form-horizontal" action="/explore.php" method="get">
		<div class="control-group">
			<div class="controls form-inline">
				<input type="text" placeholder="x" name="x" class="form-control" value="<?=isset($_GET['x']) ? (int)$_GET['x'] : ''?>" style="width: 50px; text-align: center;" />
				<input type="text" name="y" placeholder="y" class="form-control" value="<?=isset($_GET['y']) ? (int)$_GET['y'] : ''?>" style="width: 50px; text-align: center;" />
				<input type="submit" name="button" value="Go" class="form-control" />
			</div>
		</div>
		</form>
	</div>
</div>


<?php
getFooter();
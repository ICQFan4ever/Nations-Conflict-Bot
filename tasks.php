<?php
require 'inc/core.php';
autOnly();

if(isset($_GET['action']))
	{
		if($_GET['action'] == 'delete')
			{
				if(isset($_GET['type']))
					{
						switch($_GET['type'])
							{
								case 'cathedral':
									$table = 'task_cathedral';
								break;
								
								case 'mast':
									$table = 'task_mast';
								break;
								
								case 'res':
									$table = 'task_res';
								break;
								
								default: $table = 'error';
							}
						
						if($table != 'error')
							{
								if(isset($_GET['id']))
									{
										$id = (int)$_GET['id'];
										$q = mysql_query("SELECT * FROM `".$table."` WHERE `id_user` = ".$_INFO['id']." AND `id` = ".$id);
										if(mysql_num_rows($q) == 1)
											{
												if(mysql_query("DELETE FROM `".$table."` WHERE `id` = ".$id))
													{
														redirect('/tasks.php?success_del');
													}
												else
													{
														fatalError('Ошибка удаления: '.mysql_error());
													}
											}
										else
											{
												fatalError('Задание не найдено');
											}
									}
								else
									{
										redirect('/tasks.php?del_err_no_id');
									}
							}
						else
							{
								redirect('/tasks.php?del_err_unknown');
							}
					}
				else
					{
						redirect('/tasks.php?del_err_no_type');
					}
				exit;
			}
		
		if($_GET['action'] == 'on')
			{
				if(isset($_GET['type']))
					{
						switch($_GET['type'])
							{
								case 'cathedral':
									$table = 'task_cathedral';
								break;
								
								case 'mast':
									$table = 'task_mast';
								break;
								
								case 'res':
									$table = 'task_res';
								break;
								
								default: $table = 'error';
							}
						
						if($table != 'error')
							{
								if(isset($_GET['id']))
									{
										$id = (int)$_GET['id'];
										$q = mysql_query("SELECT * FROM `".$table."` WHERE `id_user` = ".$_INFO['id']." AND `id` = ".$id);
										if(mysql_num_rows($q) == 1)
											{
												if(mysql_query("UPDATE `".$table."` SET `status` = '1' WHERE `id` = ".$id))
													{
														redirect('/tasks.php?success_on');
													}
												else
													{
														fatalError('Ошибка включения: '.mysql_error());
													}
											}
										else
											{
												fatalError('Задание не найдено');
											}
									}
								else
									{
										redirect('/tasks.php?on_err_no_id');
									}
							}
						else
							{
								redirect('/tasks.php?on_err_unknown');
							}
					}
				else
					{
						redirect('/tasks.php?on_err_no_type');
					}
			}
		
		if($_GET['action'] == 'off')
			{
				if(isset($_GET['type']))
					{
						switch($_GET['type'])
							{
								case 'cathedral':
									$table = 'task_cathedral';
								break;
								
								case 'mast':
									$table = 'task_mast';
								break;
								
								case 'res':
									$table = 'task_res';
								break;
								
								default: $table = 'error';
							}
						
						if($table != 'error')
							{
								if(isset($_GET['id']))
									{
										$id = (int)$_GET['id'];
										$q = mysql_query("SELECT * FROM `".$table."` WHERE `id_user` = ".$_INFO['id']." AND `id` = ".$id);
										if(mysql_num_rows($q) == 1)
											{
												if(mysql_query("UPDATE `".$table."` SET `status` = '0' WHERE `id` = ".$id))
													{
														redirect('/tasks.php?success_off');
													}
												else
													{
														fatalError('Ошибка выключения: '.mysql_error());
													}
											}
										else
											{
												fatalError('Задание не найдено');
											}
									}
								else
									{
										redirect('/tasks.php?off_err_no_id');
									}
							}
						else
							{
								redirect('/tasks.php?off_err_unknown');
							}
					}
				else
					{
						redirect('/tasks.php?off_err_no_type');
					}
			}
		
		fatalError('Как вы сюда попали?');
	}


if(isset($_GET['mode']))
	{
		if(isset($_GET['id']))
			{
				$id = (int)$_GET['id'];
				$q = mysql_query("SELECT * FROM `accounts` WHERE `id_user` = ".$_INFO['id']." AND `id` = ".$id);
				if(mysql_num_rows($q) != 1)
					{
						redirect('/tasks.php?err_no_access');
					}
				else
					{
						$_ACC = mysql_fetch_assoc($q);
					}
			}
		# Храм
		if($_GET['mode'] == 'cathedral')
			{
				$q = mysql_query("SELECT * FROM `task_cathedral` WHERE `acc_id` = ".$id);
				if(mysql_num_rows($q) != 0)
					{
						fatalError('Для этого аккаунта уже существует задание на храм');
					}
				
				if($_ACC['cathedral_id'] == 0)
					{
						fatalError('У аккаунта не найден храм. <a href="/parseGameInfo.php?acc_id='.$_ACC['id'].'">Обновите</a> данные игры');
					}
				
				if(isset($_POST['button']))
					{
						$error = array();
						
						if(isset($_POST['id_cathedral']))
							{
								$id_cathedral = (int)$_POST['id_cathedral'];
							}
						else
							{
								$error[] = 'ID храма?';
							}
							
						if(isset($_POST['range']))
							{
								if(!preg_match("#([0-9\-/]{1,})#u", $_POST['range']))
									{
										$error[] = 'Некорректный формат строки времени';
									}
								else
									{
										$range = $_POST['range'];
									}
							}
						else
							{
								$error[] = 'Укажите диапазон выполнения';
							}
						
						if(@$_POST['status'])
							{
								$status = 1;
							}
						else
							{
								$status = 0;
							}
						
						if(empty($error))
							{
								if(mysql_query("INSERT INTO `task_cathedral`(`id_user`, `acc_id`, `id_cathedral`, `range`, `status`) VALUES (".$_INFO['id'].", ".$id.", ".$id_cathedral.", '".$range."', '".$status."')"))
									{
										redirect('/tasks.php');
									}
								else
									{
										fatalError(mysql_error());
									}
							}
					}
				
				setTitle('Храм');
				getHeader();
				formError(isset($error) ? $error : '');
				?>
				<div class="alert alert-info" role="alert">Перед созданием задания рекомендуется <b><a href="/parseGameInfo.php?acc_id=<?=$_ACC['id']?>">обновить данные игры</a></b></div>
				<form action="/tasks.php?mode=cathedral&id=<?=$id?>" method="post">
				<input type="hidden" name="id_cathedral" required="required" value="<?=$_ACC['cathedral_id']?>" class="form-control" />
				Ниже необходимо указать диапазон минут выполнения задания, в данном случае - когда пытаться начать молитву. Примеры:<br />
				<i>0-59</i> - выполнять каждую минуту<br />
				<i>1,3,5</i> - 1, 3 и 5 минута каждого часа<br />
				<i>11-23/3</i> - С 11 по 23 минуты с шагом 3 (т.е. 11, 14, 17, 20, 23)<br />
				<i>1,3,5,9-13,20-52/6</i> - 1, 3, 5, 9-13, 20, 26, 32, 38, 44, 50<br />
				<input type="text" name="range" value="<?=isset($_POST['range']) ? htmlspecialchars($_POST['range']) : ''?>" placeholder="Диапазон выполнения" class="form-control" /><br />
				<input type="checkbox" name="status"<?=@$_POST['status'] || !isset($_POST) ? ' checked="checked"' : ''?> />&nbsp;Запустить задание после добавления<br />
				<input type="submit" name="button" class="btn btn-primary" value="Создать" />
				</form>
				
				<?php
				getFooter();
				exit;
			}
		
		# Мастерская
		if($_GET['mode'] == 'mast')
			{
				if($_ACC['id_mast'] == 0)
					{
						fatalError('У аккаунта не найдена мастерская. <a href="/parseGameInfo.php?acc_id='.$_ACC['id'].'">Обновите</a> данные игры');
					}
				
				$qp = mysql_query("SELECT * FROM `patterns` WHERE `acc_id` = ".$_ACC['id']);
				if(mysql_num_rows($qp) == 0)
					{
						fatalError('У аккаунта не найдено чертежей. Невозможно создать задание на стандартное оружие. <a href="/parseGameInfo.php?acc_id='.$_ACC['id'].'">Обновите</a> данные игры');
					}
				
				if(isset($_POST['button']))
					{
						$error = array();
						
						if(isset($_POST['id_mast']))
							{
								$id_mast = (int)$_POST['id_mast'];
							}
						else
							{
								$error[] = 'Укажите ID мастерской (здания)';
							}
						
						if(isset($_POST['id_pattern']))
							{
								$id_pattern = (int)$_POST['id_pattern'];
							}
						else
							{
								$error[] = 'Укажите ID чертежа';
							}
						
						if(isset($_POST['amount']))
							{
								$amount = (int)$_POST['amount'];
							}
						else
							{
								$error[] = 'Укажите кол-во производимого оружия';
							}
						
						if(isset($_POST['range']))
							{
								if(!preg_match("#([0-9\-/]{1,})#u", $_POST['range']))
									{
										$error[] = 'Некорректный формат строки времени';
									}
								else
									{
										$range = $_POST['range'];
									}
							}
						else
							{
								$error[] = 'Укажите диапазон выполнения';
							}
						
						if(@$_POST['status'])
							{
								$status = 1;
							}
						else
							{
								$status = 0;
							}
						
						if(empty($error))
							{
								if(mysql_query("INSERT INTO `task_mast`(`id_user`, `acc_id`, `id_mast`, `id_pattern`, `amount`, `range`, `status`) VALUES (".$_INFO['id'].", ".$id.", ".$id_mast.", ".$id_pattern.", ".$amount.", '".$range."', '".$status."')"))
									{
										redirect('/tasks.php');
									}
								else
									{
										fatalError(mysql_error());
									}
							}
					}
				
				setTitle('Мастерская');
				getHeader();
				formError(isset($error) ? $error : '');
				?>
				<div class="alert alert-info" role="alert">Перед созданием задания рекомендуется <b><a href="/parseGameInfo.php?acc_id=<?=$_ACC['id']?>">обновить данные игры</a></b></div>
				<form action="/tasks.php?mode=mast&id=<?=$id?>" method="post">
				Мастерская:<br />
				<input type="text" name="id_mast" placeholder="ID мастерской" class="form-control" value="<?=isset($_POST['id_mast']) ? (int)$_POST['id_mast'] : $_ACC['id_mast']?>" /><br />
				Чертеж:<br />
				<select name="id_pattern" class="form-control">
				<?php
				while($pattern = mysql_fetch_assoc($qp))
					{
						?>
						<option value="<?=$pattern['id_pattern']?>"><?=$pattern['pattern_name']?>: (<?=$pattern['pattern_attack']?> / <?=$pattern['pattern_defence']?> / <?=$pattern['pattern_hp']?> , <?=$pattern['pattern_wood']?> / <?=$pattern['pattern_coal']?>/<?=$pattern['pattern_time']?>)</option>
						
						<?php
					}	
				?>
				</select><br />
				<input type="text" name="amount" placeholder="Кол-во оружия" class="form-control" value="<?=isset($_POST['amount']) ? (int)$_POST['amount'] : ''?>" /><br />
				Ниже необходимо указать диапазон минут выполнения задания, в данном случае - в какие минуты пытаться начать производство заданного кол-ва оружия. Примеры:<br />
				<i>0-59</i> - выполнять каждую минуту<br />
				<i>1,3,5</i> - 1, 3 и 5 минута каждого часа<br />
				<i>11-23/3</i> - С 11 по 23 минуты с шагом 3 (т.е. 11, 14, 17, 20, 23)<br />
				<i>1,3,5,9-13,20-52/6</i> - 1, 3, 5, 9-13, 20, 26, 32, 38, 44, 50<br />
				<input type="text" name="range" value="<?=isset($_POST['range']) ? htmlspecialchars($_POST['range']) : ''?>" placeholder="Диапазон выполнения" class="form-control" /><br />
				<input type="checkbox" name="status"<?=@$_POST['status'] || !isset($_POST) ? ' checked="checked"' : ''?> />&nbsp;Запустить задание после добавления<br />
				<input type="submit" name="button" class="btn btn-primary" value="Создать" />
				</form>
				
				<?php
				getFooter();
				exit;
			}
		
		# ресурсы из казны
		if($_GET['mode'] == 'res')
			{
				if($_ACC['id_union'] == 0)
					{
						fatalError('Аккаунт не состоит в союзе, или в союзе нет казны. <a href="/parseGameInfo.php?acc_id='.$_ACC['id'].'">Обновите</a> данные игры');
					}
				if(isset($_POST['button']))
					{
						//echo '<pre>';
						//die(print_r($_POST));
						$error = array();
						
						if(isset($_POST['id_building']))
							{
								$id_building = (int)$_POST['id_building'];
							}
						else
							{
								$error[] = 'Введите ID здания (казны)';
							}
						
						if(isset($_POST['action']))
							{
								if($_POST['action'] == 1)
									{
										// кладем
										$action = 1;
									}
								elseif($_POST['action'] == 2)
									{
										// берем
										$action = 2;
									}
								else
									{
										$error[] = 'Ресурсы можно только взять или отправить. Хули вам еще надо?';
									}
							}
						else
							{
								$error[] = 'Выберите тип действия';
							}
						
						if(isset($_POST['id_res']))
							{
								if($_POST['id_res'] >= 1 && $_POST['id_res'] <= 5)
									{
										$id_res = (int)$_POST['id_res'];
									}
								else
									{
										$error[] = 'Неизвестный тип ресурса';
									}
							}
						else
							{
								$error[] = 'Выберите ресурс';
							}
						
						
						
						if(isset($_POST['amount']))
							{
								$amount = (int)$_POST['amount'];
							}
						else
							{
								$error[] = 'Выберите кол-во ресурса';
							}
						
						if(isset($_POST['range']))
							{
								if(!preg_match("#([0-9\-/]{1,})#u", $_POST['range']))
									{
										$error[] = 'Некорректный формат строки времени';
									}
								else
									{
										$range = $_POST['range'];
									}
							}
						else
							{
								$error[] = 'Укажите диапазон выполнения';
							}
						
						if(@$_POST['status'])
							{
								$status = 1;
							}
						else
							{
								$status = 0;
							}
						
						if(empty($error))
							{
								if(mysql_query("INSERT INTO `task_res`(`id_user`, `acc_id`, `id_building`, `action`, `id_res`, `amount`, `range`, `status`) VALUES (".$_INFO['id'].", ".$id.", ".$id_building.", '".$action."', ".$id_res.", ".$amount.", '".$range."', '".$status."')"))
									{
										redirect('/tasks.php');
									}
								else
									{
										fatalError(mysql_error());
									}
							}
					}
				
				setTitle('Ресурсы');
				getHeader();
				formError(isset($error) ? $error : '');
				?>
				<div class="alert alert-info" role="alert">Перед созданием задания рекомендуется <b><a href="/parseGameInfo.php?acc_id=<?=$_ACC['id']?>">обновить данные игры</a></b></div>
				<form action="/tasks.php?mode=res&id=<?=$id?>" method="post">
				Казна:<br />
				<input type="text" name="id_building" placeholder="ID казны" class="form-control" value="<?=isset($_POST['id_building']) ? (int)$_POST['id_building'] : $_ACC['id_union']?>" /><br />
				<input type="radio" name="action" value="1" />&nbsp;Отправлять в казну<br />
				<input type="radio" name="action" value="2" />&nbsp;Взять из казны<br />
				<select name="id_res" class="form-control">
				<?php
				$resources = array(1 => 'Еда', 2 => 'Дерево', 3 => 'Камень', 4 => 'Руда', 5 => 'Золото');
				foreach($resources as $res_id => $res_name)
					{
						?>
						<option value="<?=$res_id?>"<?=@$_POST['id_res'] == $res_id ? ' selected="selected"' : ''?>><?=$res_name?></option>
						<?php
					}
				?>
				</select><br />
				<input type="text" name="amount" placeholder="Количество" class="form-control" value="<?=isset($_POST['amount']) ? (int)$_POST['amount'] : ''?>" /><br />
				Ниже необходимо указать диапазон минут выполнения задания, в данном случае - в какие минуты пытаться взять ресурсы. Примеры:<br />
				<i>0-59</i> - выполнять каждую минуту<br />
				<i>1,3,5</i> - 1, 3 и 5 минута каждого часа<br />
				<i>11-23/3</i> - С 11 по 23 минуты с шагом 3 (т.е. 11, 14, 17, 20, 23)<br />
				<i>1,3,5,9-13,20-52/6</i> - 1, 3, 5, 9-13, 20, 26, 32, 38, 44, 50<br />
				<input type="text" name="range" value="<?=isset($_POST['range']) ? htmlspecialchars($_POST['range']) : ''?>" placeholder="Диапазон выполнения" class="form-control" /><br />
				<input type="checkbox" name="status"<?=@$_POST['status'] || !isset($_POST) ? ' checked="checked"' : ''?> />&nbsp;Запустить задание после добавления<br />
				<input type="submit" name="button" class="btn btn-primary" value="Создать" />
				</form>
				
				<?php
				getFooter();
				exit;
			}
		
		exit;
	}

setTitle('Задания');
getHeader();
?>

<h4>Новое задание</h4>
<?php
$q_acc = mysql_query("SELECT * FROM `accounts` WHERE `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q_acc) == 0)
	{
		echo error('Нет ни одного аккаунта');
	}
else
	{
		$options = '';
		while($acc = mysql_fetch_assoc($q_acc))
			{
				$options .= '<option value="'.$acc['id'].'">'.$acc['name'].' (id '.$acc['id'].')</option>'.PHP_EOL;
			}
		?>
		<form action="/tasks.php" method="get">
		Для аккаунта: <select name="id" class="form-control"><?=$options?></select> 
		создать задание на: 
		<select name="mode" class="form-control">
		<option value="cathedral">храм</option>
		<option value="mast">мастерскую</option>
		<option value="res">ресурсы казны</option>
		</select><br />
		<button class="btn btn-primary">Далее</button>
		</form>
		<br />
		<?php
	}
?>


<h4>Задания типа &quot;Храм&quot;</h4>
<?php
$q_cathedral = mysql_query("SELECT * FROM `task_cathedral` WHERE `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q_cathedral) != 0)
	{
		while($task_cathedral = mysql_fetch_assoc($q_cathedral))
			{
				$q_acc = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$task_cathedral['acc_id']);
				$_ACC = mysql_fetch_assoc($q_acc);
				?>
				<div class="panel panel-default">
					<div class="panel-body">
						Статус: <?=$task_cathedral['status'] == 1 ? '<span class="label label-success">Активно</span>' : '<span class="label label-default">Отключено</span>'?><br />
						Аккаунт: <b>&quot;<?=$_ACC['name']?>&quot;</b> <i>(ID: <?=$_ACC['id']?>)</i><br /> 
						<!--ID храма: <b><?=$task_cathedral['id_cathedral']?></b><br />-->
						Интервал времени: <b><?=$task_cathedral['range']?></b><br />
						
						<div class="btn-group">
							<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
								Действия&nbsp;<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="/tasks.php?action=delete&type=cathedral&id=<?=$task_cathedral['id']?>" onclick="return confirm('Удалить?');">Удалить</a></li>
								<li><a href="/tasks.php?action=<?=$task_cathedral['status'] == 1 ? 'off' : 'on'?>&type=cathedral&id=<?=$task_cathedral['id']?>"><?=$task_cathedral['status'] == 1 ? 'Выключить' : 'Включить'?></a></li>
							</ul>
						</div>
					</div>
				</div>
				<?php
			}
	}
else
	{
		echo error('Пока что нет заданий');
	}
?>

<h4>Задания типа &quot;Мастерская&quot;</h4>
<?php
$q_mast = mysql_query("SELECT * FROM `task_mast` WHERE `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q_mast) != 0)
	{
		while($task_mast = mysql_fetch_assoc($q_mast))
			{
				$q_acc = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$task_mast['acc_id']);
				$_ACC = mysql_fetch_assoc($q_acc);
				
				
				# черт
				$qp = mysql_query("SELECT * FROM `patterns` WHERE `id_pattern` = ".$task_mast['id_pattern']);
				$pattern = mysql_fetch_assoc($qp);
				
				?>
				<div class="panel panel-default">
					<div class="panel-body">
						Статус: <?=$task_mast['status'] == 1 ? '<span class="label label-success">Активно</span>' : '<span class="label label-default">Отключено</span>'?><br />
						Аккаунт: <b>&quot;<?=$_ACC['name']?>&quot;</b> <i>(ID: <?=$_ACC['id']?>)</i><br /> 
						<!--ID мастерской: <b><?=$task_mast['id_mast']?></b><br />-->
						Чертеж: <b><?=$pattern['pattern_name']?></b><br />
						Количество: <b><?=$task_mast['amount']?></b><br />
						Интервал времени: <b><?=$task_mast['range']?></b><br />
						
						<div class="btn-group">
							<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
								Действия&nbsp;<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="/tasks.php?action=delete&type=mast&id=<?=$task_mast['id']?>" onclick="return confirm('Удалить?');">Удалить</a></li>
								<li><a href="/tasks.php?action=<?=$task_mast['status'] == 1 ? 'off' : 'on'?>&type=mast&id=<?=$task_mast['id']?>"><?=$task_mast['status'] == 1 ? 'Выключить' : 'Включить'?></a></li>
							</ul>
						</div>
					</div>
				</div>
				<?php
			}
	}
else
	{
		echo error('Пока что нет заданий');
	}
?>

<h4>Задания типа &quot;Ресурсы&quot;</h4>
<?php
$q_res = mysql_query("SELECT * FROM `task_res` WHERE `id_user` = ".$_INFO['id']);
if(mysql_num_rows($q_res) != 0)
	{
		while($task_res = mysql_fetch_assoc($q_res))
			{
				$q_acc = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$task_res['acc_id']);
				$_ACC = mysql_fetch_assoc($q_acc);
				$actions = array(1 => 'Положить ресурсы', 2 => 'Взять ресурсы');
				$resources = array(1 => 'Еда', 2 => 'Дерево', 3 => 'Камень', 4 => 'Руда', 5 => 'Золото');
				?>
				<div class="panel panel-default">
					<div class="panel-body">
						Статус: <?=$task_res['status'] == 1 ? '<span class="label label-success">Активно</span>' : '<span class="label label-default">Отключено</span>'?><br />
						Аккаунт: <b>&quot;<?=$_ACC['name']?>&quot;</b> <i>(ID: <?=$_ACC['id']?>)</i><br /> 
						<!--ID казны: <b><?=$task_res['id_building']?></b><br />-->
						Ресурс: <b><?=$resources[$task_res['id_res']]?></b><br />
						Действие: <b><?=$actions[$task_res['action']]?></b><br />
						Количество: <b><?=$task_res['amount']?></b><br />
						Интервал времени: <b><?=$task_res['range']?></b><br />
						<div class="btn-group">
							<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
								Действия&nbsp;<span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="/tasks.php?action=delete&type=res&id=<?=$task_res['id']?>" onclick="return confirm('Удалить?');">Удалить</a></li>
								<li><a href="/tasks.php?action=<?=$task_res['status'] == 1 ? 'off' : 'on'?>&type=res&id=<?=$task_res['id']?>"><?=$task_res['status'] == 1 ? 'Выключить' : 'Включить'?></a></li>
							</ul>
						</div>
					</div>
				</div>
				<?php
			}
	}
else
	{
		echo error('Пока что нет заданий');
	}

getFooter();
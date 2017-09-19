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

setTitle('Разведка');
getHeader();

echo '<h4>Рассылка маяков</h4>';

set_time_limit(0);
ignore_user_abort(true);

if(isset($_POST['button']))
	{
		if(isset($_POST['x_start']))
			{
				$x_start = (int)$_POST['x_start'];
				if($x_start < 1 | $x_start > 200)
					{
						$error[] = 'Неверное начало координат x';
					}
				else
					{
						if(isset($_POST['x_end']))
							{
								$x_end = (int)$_POST['x_end'];
								if($x_end < 1 | $x_end > 200 | $x_end < $x_start)
									{
										$error[] = 'Неверный конец диапазон координат x';
									}
								else
									{
										if(isset($_POST['y_start']))
											{
												$y_start = (int)$_POST['y_start'];
												if($y_start < 1 | $y_start > 200)
													{
														$error[] = 'Неверное начало координат y';
													}
												else
													{
														if(isset($_POST['y_end']))
															{
																$y_end = (int)$_POST['y_end'];
																if($y_end < 1 | $y_end > 200 | $y_end < $y_start)
																	{
																		$error[] = 'Неверный конец диапазон координат y';
																	}
															}
														else
															{
																$error[] = 'Укажите конец диапазона координат y';
															}
													}
											}
										else
											{
												$error[] = 'Укажите начало координат y';
											}
									}
							}
					}
			}
		else
			{
				$error[] = 'Укажите начало координат x';
			}
		
		if(isset($_POST['x_step']))
			{
				$x_step = (int)$_POST['x_step'];
				if($x_step < 1 | $x_step > 50)
					{
						$error[] = 'Укажите шаг смещения x от 1 до 50';
					}
			}
		else
			{
				$error[] = 'Укажите шаг смещения x';
			}
		
		if(isset($_POST['y_step']))
			{
				$y_step = (int)$_POST['y_step'];
				if($y_step < 1 | $y_step > 50)
					{
						$error[] = 'Укажите шаг смещения x от 1 до 50';
					}
			}
		else
			{
				$error[] = 'Укажите шаг смещения x';
			}
		
		if(isset($_POST['num']))
			{
				$num = (int)$_POST['num'];
				if($num < 1)
					{
						$error[] = 'Введите количество юнитов отличное от нуля';
					}
			}
		
		if(isset($_POST['pattern']))
			{
				$pattern = (int)$_POST['pattern'];
				$q = mysql_query("SELECT * FROM `patterns` WHERE `id_pattern` = ".$pattern);
				if(mysql_num_rows($q) != 1)
					{
						$error[] = 'Чертеж не найден';
					}
				else
					{
						$pattern = mysql_fetch_assoc($q);
					}
			}
		
		if(empty($error))
			{
				$ccc = 0;
				// echo '<pre>';
				// die(print_r($_POST));
				for($x = $x_start; $x <= $x_end; $x = $x + $x_step)
					{
						for($y = $y_start; $y <= $y_end; $y = $y + $y_step)
							{
								## Каждые 50 отрядов узнаем, на какой странице оружие
								if($ccc % 50 == 0)
									{
										// 1) определяем общее количество страниц в сборе войскам#u
										$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=duty');
										// прокси
										curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
										curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

										// всякая хуйня
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										// curl_setopt($ch, CURLOPT_HEADER, true);
										curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
										curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
										curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
										$a = curl_exec($ch);
										
										preg_match_all("#q\=duty\"><span>(\d+)#u", $a, $arr);
										$nummm = max($arr[1]);
										
										
										// 2) циклом обходим все страницы сбора и ищем на какой странице нужный нам чертеж
										for($i = 1; $i <= $nummm; $i++)
											{
												$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=duty');
												// прокси
												curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
												curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

												// всякая хуйня
												curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
												// curl_setopt($ch, CURLOPT_HEADER, true);
												curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
												curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
												curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
												$a = curl_exec($ch);
												if(preg_match("#id_pattern\=".$pattern['id_pattern']."\">#u", $a))
													{
														$pagee = $i;
														break;
													}
											}
									}
								
								
								
								/*
								1) Собираем войско
								2) Отправляем войско
								3) Пишем в базу ID отряда
								http://nc.default.ovh/game.php?acc_id=1&q=duty&p=0&cnf=&union=&am[1.2417563]=1&x[1.2417563]=1&cnf=1
								*/
								$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=duty&p='.$pagee.'&cnf=&union=&am['.$pattern['pattern_type'].'.'.$pattern['id_pattern'].']='.$num.'&x['.$pattern['pattern_type'].'.'.$pattern['id_pattern'].']=1&cnf=1');
								// прокси
								curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
								curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

								// всякая хуйня
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// curl_setopt($ch, CURLOPT_HEADER, true);
								curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
								curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
								// curl_setopt($ch, CURLOPT_TIMEOUT, 12);
								
								$a = curl_exec($ch);
								
								//echo $a;
								//die;
								preg_match("#\&id_unit\=(\d+)\">К войскам#u", $a, $arr);
								//print_r($arr);
								
								$id_unit = (int)$arr[1];
								
								// вычисляем id_location по x и y
								
								$id_location = 200 * ($y - 1) + $x;
								
								
								
								# Идем, идем
								$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$id_unit.'&action_id=1&id_loc_to='.$id_location.'&cnf=1');
								curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
								curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

								// всякая хуйня
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								// curl_setopt($ch, CURLOPT_HEADER, true);
								curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
								curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
								// curl_setopt($ch, CURLOPT_TIMEOUT, 12);
								
								$a = curl_exec($ch);
								// echo $a;
								
								mysql_query("INSERT INTO `razv`(`id_unit`, `x`, `y`, `acc_id`) VALUES (".$id_unit.", ".$x.", ".$y.", ".$_ACC['id'].")");
								
								sleep(3);
							}
					}
			}
	}

formError(isset($error) ? $error : '');
?>

<form action="/razv.php?acc_id=<?=$id?>" method="post">
start - начальная точка; end - конечная точка; step - шаг<br />
<input type="text" name="x_start" placeholder="x_start" size="5" class="form-control" style="display: inline-block;" />
<input type="text" name="x_end" placeholder="x_end" size="5" class="form-control" style="display: inline-block;" />
<input type="text" name="x_step" placeholder="x_step" size="5" class="form-control" style="display: inline-block;" /><br />


<input type="text" name="y_start" placeholder="y_start" size="5" class="form-control" style="display: inline-block;" />
<input type="text" name="y_end" placeholder="y_end" size="5" class="form-control" style="display: inline-block;" />
<input type="text" name="y_step" placeholder="y_step" size="5" class="form-control" style="display: inline-block;" /><br />

Чертеж:
<?php

$q = mysql_query("SELECT * FROM `patterns` WHERE `acc_id` = ".$_ACC['id']);
echo '<select name="pattern" class="form-control">';

while($pattern = mysql_fetch_assoc($q))
	{
		?>
		<option value="<?=$pattern['id_pattern']?>"><?=$pattern['pattern_name']?></option>
		
		<?php
	}

?>
</select><br />
<input type="text" name="num" class="form-control" placeholder="Размер отряда" /><br />
Время выполнения зависит от количества маяков - в среднем по 4-5 секунд на каждый. Убедитесь что есть достаточное количество арсенала и золота. После нажатия кнопки подождите 5-7 секунд. Если не произошло никаких ошибок, окно можно закрыть. Кулдаун между отправкой отрядов - 3 секунды и 1-2 сеекунды на выполнение самого скрипта.<br />
<input type="submit" name="button" value="Старт" class="btn btn-primary" />
</form>
<br />
<h4>Маяки в охрану</h4>
Выполняйте скрипт только после того, как все отряды доберутся до своих точек. Скрипт циклом проходит по всем маякам, ставит их в охрану и делает об этом пометку в базе. Существует два режима запуска этого скрипта: &quot;мягкий&quot; - ставит в охрану только те отряды, которые в базе не помечены как охраняющие. &quot;Жесткий&quot; режим запрашивает из базы все ваши маяки и пытается поставить их в охрану вне зависимости от того, есть запись &quot;на охране&quot; в базе или нет.<br />
<b>Не выполняйте жесткий режим без необходимости</b><br /><br />
<a href="/razvGuard.php?acc_id=<?=$_ACC['id']?>" target=_blank">Мягкий режим</a> / <a href="/razvGuard.php?acc_id=<?=$_ACC['id']?>&force=1" onclick="return confirm('Вы уверены?')" target=_blank">Жесткий режим</a><br />
<h4>Маяки в засаду</h4>
Аналогично тому, что выше. В мягком режиме не берутся отряды в засаде и на охране. В жестком - даже те, которые, возможно, в охране<br />
<a href="/razvWait.php?acc_id=<?=$_ACC['id']?>" target=_blank">Мягкий режим</a> / <a href="/razvWait.php?acc_id=<?=$_ACC['id']?>&force=1" onclick="return confirm('Вы уверены?')" target=_blank">Жесткий режим</a><br />

<?php
getFooter();
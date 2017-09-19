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

if($_INFO['bot_access'] != '1')
	{
		fatalError('Доступ запрещен');
	}

if(isset($_POST['button']))
	{
		// die(print_r($_POST));
		$error = array();
		$x = isset($_POST['x']) ? intval($_POST['x']) : 0;
		$y = isset($_POST['y']) ? intval($_POST['y']) : 0;
		if($x + $y <= 2)
			{
				$error[] = 'Неверные координаты';
			}
		
		$query = '&';
		
		$col = 0;
		for($i = 1; $i <= 8; $i++)
			{
				$id_pattern[$i] = isset($_POST['id_pattern'][$i]) ? intval($_POST['id_pattern'][$i]) : 0;
				$pattern_type[$i] = isset($_POST['pattern_type'][$i]) ? intval($_POST['pattern_type'][$i]) : 0;
				$numm[$i] = isset($_POST['numm'][$i]) ? intval($_POST['numm'][$i]) : 0;
				$cols[$i] = isset($_POST['cols'][$i]) ? intval($_POST['cols'][$i]) : 0;
				if($id_pattern[$i] != 0);
					{
						$col++;
						$query .= 'am['.$pattern_type[$i].'.'.$id_pattern[$i].']='.$numm[$i].'&x['.$pattern_type[$i].'.'.$id_pattern[$i].']='.$cols[$i].'&';
					}
			}
		
		if($col > 1)
			{
				$query .= 'union=1&';
			}
		// die($query);
		if(empty($error))
			{
				// По заданным координатам вычисляем id_location
				$id_location = 200 * ($y - 1) + $x;

				// Открываем локацию
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=environ&id_location='.$id_location);
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

				// парсим id_login
				preg_match("#Здесь г\. <a href=\"game\.php\?q\=userinfo\&amp\;id_login\=(\d+)\">#u", $a, $arr);
				if(empty($arr[1]))
					{
						fatalError('В этой локации нет города');
					}
				$id_login = trim($arr[1]);

				// открываем список провинций
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=user_provinces&id_login='.$id_login);
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

				// определяем количество страниц
				preg_match_all("#game\.php\?p\=(\d+)&amp;#u", $a, $arr);
				if(empty($arr[1]))
					{
						fatalError('Провинции не найдены');
					}
				$num = max($arr[1]);

				
				// по циклу открываем все страницы
				for($i = 1; $i <= $num; $i++)
					{
						// открываем список провинций
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=user_provinces&id_login='.$id_login.'&p='.$i);
						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						// curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						$a .= curl_exec($ch);
					}
					
				preg_match_all("#<a class\=\"war\" href\=\"game\.php\?q\=userinfo&amp;id_login\=(\d+)\">([a-zA-Z0-9а-яА-Я _\-]{1,})</a> <a href=\"game\.php\?q\=map&amp;x\=\d+&amp;y\=\d+\">\[(\d+);(\d+)\]</a>#u", $a, $arr);
						// $arr[1] - id_login
						// $arr[2] - имя
						// $arr[3] - x
						// $arr[4] - y
				$output = '';
				$count = count($arr[1]);

				// по циклу перебираем все провинции и собираем отряды
				for($i = 0; $i < $count; $i++)
					{
						// вычисляем координаты
						$x = $arr[3][$i];
						$y = $arr[4][$i];
						$id_location = 200 * ($y - 1) + $x;
						
						// собираем отряд
						
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=duty&p=0&'.$query.'&cnf=1');
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
						
						// die($query.$a);
						// парсим ID отряда
						preg_match("#\&id_unit\=(\d+)\">К войскам#u", $a, $arrr);
						//print_r($arr);
						if(empty($arrr[1]))
							{
								$errorr[] = 'Ошибка сбора отряда на точку '.$x.':'.$y;
							}
						else
							{
								$id_unit = trim($arrr[1]);
								
								// отправляем отряд
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
								
								$output .= '№'.$id_unit.'; ['.$x.';'.$y.'] ; <a href="/game.php?acc_id='.$_ACC['id'].'&q=control&id_unit='.$id_unit.'">Управлять</a>/<a href="/game.php?acc_id='.$_ACC['id'].'&q=control&target_type=1&target_id=0&action_id=10&id_unit='.$id_unit.'">Освободить</a>/<a href="/game.php?acc_id='.$_ACC['id'].'&q=control&id_unit='.$id_unit.'&action_id=3">В охрану</a>/<a href="/game.php?acc_id='.$_ACC['id'].'&q=control&target_type=1&target_id=0&action_id=9&id_unit='.$id_unit.'">Захватить</a><br />'.PHP_EOL;
							}
					}

				if(mysql_query("INSERT INTO `text`(`text`) VALUES ('".$output."')"))
					{
						$__id = mysql_insert_id();
						setTitle('Проворезка');
						getHeader();
						formError(isset($errorr) ? $errorr : '');
						echo good('Готово! Отряды отправлены на провинции. Для быстрого управления перейдите на <a href="/text.php?id='.$__id.'">эту страницу</a>');
						getFooter();
						exit;
					}
			}
	}

setTitle('Проворез');
getHeader();
formError(isset($error) ? $error : '');
?>


<form action="/province.php?acc_id=<?=$_ACC['id']?>" method="post">
Координаты жертвы:<br />
<input type="text" name="x" placeholder="x" size="2" style="display: inline-block; max-width: 65px;" class="form-control" />
<input type="text" name="y" placeholder="y" size="2" style="display: inline-block; max-width: 65px;" class="form-control" />
<br /><br />
Состав войск:<br />
<?php


for($i = 1; $i <= 8; $i++)
	{
		?>
		<select name="pattern_type[<?=$i?>]" style="display: inline-block; max-width: 80px; font-size: x-small;" class="form-control">
		<option value="1">Мечи</option>
		<option value="2">Луки</option>
		<option value="3">Копья</option>
		<option value="4">Баллисты</option>
		<option value="5">Колесницы</option>
		</select>
		<input type="text" name="id_pattern[<?=$i?>]" placeholder="id_pattern" style="display: inline-block; max-width: 75px; font-size: x-small;" class="form-control" />
		<input type="text" name="numm[<?=$i?>]" placeholder="Чслнст" class="form-control" style="display: inline-block; max-width: 70px; font-size: x-small;" /> x <input type="text" name="cols[<?=$i?>]" class="form-control" style="display: inline-block; max-width: 45px; font-size: x-small;" value="1" />
		
		<hr size="1" />
		<?php
	}
?>

<input type="submit" name="button" value="Отправить" class="btn btn-primary" />
</form>
<?php

getFooter();
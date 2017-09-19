<?php
// die($_SERVER['DOCUMENT_ROOT'].'/data/archlog.txt');
// die(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/data/archlog.txt'));
$starttime = microtime();
list($sec, $msec) = explode(" ", $starttime);
$startime = $sec + $msec;
require 'inc/core.php';

// Список аккаунтов с археологией

$numeric = 0;
$loggg = '<h3>Дата запуска: '.date('H:i:s d.m.Y').'</h4>';
$q = mysql_query("SELECT * FROM `accounts` WHERE `archeology` = '1' ORDER BY `id` ASC");
while($_ACC = mysql_fetch_assoc($q))
	{
		echo '<b>'.$_ACC['name'].'</b>:<br />';
		$loggg .= '<b>'.$_ACC['name'].'</b>:<br />';
		################### АЛГОРИТМ 
		# Открываем список археологии
		# снимаем с охраны (3) / засады (6)
		# пытаемся копать
		# обрабатываем выходные данные:
		## Копает - пропускаем
		## Предлагает идти - парсим алгоритмом

		# Открываем список археологии и парсим отряды
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=archeology&a=expeditions');

		// прокси
		curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

		// всякая хуйня
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
		curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		// запрос
		$a = curl_exec($ch);
		// die($a);
		// парсим отряды
		preg_match_all("#\&id\_unit\=(\d+)\">(?:[а-яА-Я]{1,})#u", $a, $arr);

		if(empty($arr[1]))
			{
				// А НИХУЯ!1
				echo 'Нет экспедиций<br />';
				$loggg .= 'Нет экспедиций<br />';
			}
		else
			{
				// Перебираем эспедиции
				foreach($arr[1] as $key => $value)
					{
						# Для определения координат
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$value);

						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

						// запрос
						$a = curl_exec($ch);
						
						preg_match("#<span class\=\"orange\">\[(\d+)\;(\d+)\]</span>#u", $a, $arr);
						// print_r($arr);
						$x = $arr[1]; $x1 = $x;
						$y = $arr[2]; $y1 = $y;
						
						
						# Снимаемся с охраны
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$value.'&action_id=5');

						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

						// запрос
						$a = curl_exec($ch);
						
						# Снимаемся с засады
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$value.'&action_id=6');

						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						
						# если уже идет - тормозим. повышается точность
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$value.'&action_id=2');

						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						
						
						// запрос
						$a = curl_exec($ch);

						
						# Пытаемся начать раскопки
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$value.'&action_id=23');

						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

						// запрос
						$a = curl_exec($ch);
						
						
						/*
						очень далеко-100-150
						достаточно далеко-70-100
						далеко 40-70
						не очень далеко 20-35
						в приделах видимости-20
						неподалеку-10-15
						достаточно близко-10
						близко-7
						очень близко-2-4
						чрезвычайно близко 2
						*/
						
						if(preg_match("#(очень далеко|достаточно далеко|не очень далеко|далеко|в пределах видимости|неподалеку|очень близко|чрезвычайно близко|достаточно близко|близко)?\s*на (северо\-востоке|юго\-востоке|юго\-западе|северо\-западе|севере|востоке|юге|западе)#u", $a, $arr))
							{
								$to = trim($arr[2]);
								$pluss = trim($arr[1]);
								// die($to);
								switch($pluss)
									{
										case 'очень далеко':
											$plus = rand(100,150);
										break;
										
										case 'достаточно далеко':
											$plus = rand(40,70);
										break;
										
										case 'далеко':
											$plus = rand(40,70);
										break;
										
										case 'не очень далеко':
											$plus = rand(20,40);
										break;
										
										case 'в пределах видмости':
											$plus = rand(15,20);
										break;
										
										case 'неподалеку':
											$plus = rand(10,15);
										break;
										
										case 'достаточно близко':
											$plus = rand(7,10);
										break;
										
										case 'близко':
											$plus = rand(4,7);
										break;
										
										case 'очень близко':
											$plus = rand(2,4);
										break;
										
										case 'чрезвычайно близко':
											$plus = rand(1,2);
										break;
										
										default: $plus = rand(150,200);
										break;
									}
								// echo $to."\n";
								// (севере|северо\-востоке|востоке|юго\-востоке|юге|юго\-западе|западе|северо\-западе)
								switch($to)
									{
										case 'севере':
											$y = $y1 - $plus;
										break;
										
										case 'северо-востоке':
											$y = $y1 - $plus; $x = $x + $plus;
										break;
										
										case 'востоке':
											$x = $x + $plus;
										break;
										
										case 'юго-востоке':
											$x = $x + $plus; $y = $y1 + $plus;
										break;
										
										case 'юге':
											$y = $y1 + $plus;
										break;
										
										case 'юго-западе':
											$x = $x - $plus; $y = $y1 + $plus;
										break;
										
										case 'западе':
											$x = $x - $plus;
										break;
										
										case 'северо-западе':
											$x = $x - $plus; $y = $y1 - $plus;
										break;
										
										default: $x = $x + $plus; $y = $y1 + $plus;
										break;
									}
								// echo "$x, $y\n"; die;
								if($x < 1)
									{
										$x = 1;
									}
								
								if($x > 200)
									{
										$x = 200;
									}
									
								if($y < 1)
									{
										$y = 1;
									}
									
								if($y > 200)
									{
										$y = 200;
									}
								
								$id_location = 200 * ($y - 1) + $x;
								
								
								// шлем отряд на точку
								$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$value.'&action_id=1&id_loc_to='.$id_location.'&cnf=1');
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
								
								echo 'Отряд <a href="/game.php?acc_id='.$_ACC['id'].'&q=control&id_unit='.$value.'">'.$value.'</a> ('.$x1.':'.$y1.') ушел на точку '.$x.':'.$y.' (раскопки следует вести <i>'.$pluss.'</i> на <i>'.$to.'</i>)';
								$loggg .= 'Отряд <a href="/game.php?acc_id='.$_ACC['id'].'&q=control&id_unit='.$value.'">'.$value.'</a> ('.$x1.':'.$y1.') ушел на точку '.$x.':'.$y.' (раскопки следует вести <i>'.$pluss.'</i> на <i>'.$to.'</i>)';
								
							}
						else
							{
								echo 'Отряд <a href="/game.php?acc_id='.$_ACC['id'].'&q=control&id_unit='.$value.'">'.$value.'</a> ('.$x1.':'.$y1.') никуда не пошел (нет карты, отряд убит или раскопки уже ведутся)';
								$loggg .= 'Отряд <a href="/game.php?acc_id='.$_ACC['id'].'&q=control&id_unit='.$value.'">'.$value.'</a> ('.$x1.':'.$y1.') никуда не пошел (нет карты, отряд убит или раскопки уже ведутся)';
							}
						$numeric++;
						$endtime = microtime();
						list($sec, $msec) = explode(" ", $endtime);
						$entime = $sec + $msec;
						
						$loggg .= ' '.round($entime - $startime, 4).' сек<br />'.PHP_EOL;
						echo ' '.round($entime - $startime, 4).' сек<br />'.PHP_EOL;
						
					}
			}
		echo '<hr />';
		$loggg .= '<hr />';
	}
echo '<br />Всего экспедиций: '.$numeric.'<br />';
$loggg .= '<br />Всего экспедиций: '.$numeric.'<br />';
$f = fopen('/var/www/nc.default.ovh/data/archlog.txt', 'w');
flock($f, LOCK_EX);
fputs($f, $loggg);
flock($f, LOCK_UN);
fclose($f);
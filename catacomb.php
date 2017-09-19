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
// print_r($_GET);

$hole = @$_GET['hole'] == 40000 ? 40000 : 1;

$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=environ&id_location='.$hole);

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

preg_match_all("#p_objects=(\d+)#u", $a, $arr);
$end_page = end($arr[1]);



// echo $a;
$counter = 0;
$arm = 0;
$resources = 0;
for($i = 1; $i <= $end_page; $i++)
	{
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=environ&p_objects=1&id_location='.$hole.'&rand='.rand(10000, 99999));
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
		
		// а есть ли на точке вообще отряды более 1000 человек? проверяем
		preg_match_all("#(\d+) чел\.\,#u", $a, $res);
		$max = max($res[1]);
		if($max >= 1000)
			{
				preg_match_all("#game\.php\?q\=control\&id_unit\=(\d+)#", $a, $arr);
				foreach($arr[1] as $id_unit)
					{
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$id_unit);
						// прокси
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
						// всякая хуйня
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						$a = curl_exec($ch);
						// die($a);

						if(preg_match("#\&target_type\=2#", $a))
							{
								# проверка численности
								preg_match("#<div>(\d+) чел\.\,#u", $a, $tmpa);
								$numm = trim($tmpa[1]);
								if($numm >= 1000 && $numm < 2000)
									{
									
										# предварительная страница входа
										// echo $id_unit.' - '.$num.'<br />';
										$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$id_unit.'&target_type=2&p_objects=0');
										curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
										curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
										// всякая хуйня
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										curl_setopt($ch, CURLOPT_HEADER, true);
										curl_setopt($ch, CURLOPT_REFERER, 'http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$id_unit);
										curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
										curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
										// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
										$a = curl_exec($ch);
										// die($a);
										sleep(1);
										# парсим secureHash 
										preg_match("#secureHash\=([a-fA-F0-9]{1,})#u", $a, $arr);
										// print_r($arr);
										$hash = trim($arr[1]);
										// echo $hash;
										
										# входим
										$url = 'http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$id_unit.'&target_type=2&target_id=0&action_id=16&p_objects=0&secureHash='.$hash;
										$ch = curl_safe_init($url);
										curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
										curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
										// всякая хуйня
										curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
										// curl_setopt($ch, CURLOPT_HEADER, true);
										curl_setopt($ch, CURLOPT_REFERER, 'http://nations.mgates.ru/conflict/game.php?q=control&id_unit='.$id_unit);
										curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
										curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
										// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
										$a = curl_exec($ch);
										// die($a);
										if(preg_match("#в игре развелось много ботоводов#u", $a))
											{
												if($counter < 1)
													{
														setTitle('Капча');
														getHeader();
													}
												mysql_query("UPDATE `accounts` SET `notification` = 'Автослив остановлен в связи с появлением капчи. Введите капчу и запустите скрипт снова', `notification_url` = '/game.php?q=control&id_unit=".$id_unit."&target_type=2&target_id=0&action_id=16&p_objects=0&secureHash=".$hash."&acc_id=".$_ACC['id']."' WHERE `id` = ".$_ACC['id']);
												echo '<div class="alert alert-warning"><audio src="/style/notify.mp3" autoplay preload></audio>Внимание, найдена капча. Автослив остановлен. Нажмите <a href="/game.php?q=control&id_unit='.$id_unit.'&target_type=2&target_id=0&action_id=16&p_objects=0&secureHash='.$hash.'&acc_id='.$_ACC['id'].'&rem_not=1" target="_blank">сюда</a>, чтобы ввести ее. Затем снова стартуйте скрипт.</div>';
												
												if($counter < 1) getFooter();
												/*# Pushover
												if($_ACC['id'] == 1  | $_ACC['id'] == 4)
													{
														curl_setopt_array($ch2 = curl_safe_init(), array(
														CURLOPT_URL => "https://api.pushover.net/1/messages.json",
														CURLOPT_POSTFIELDS => array(
														"token" => "aoTkfxH4MwdSMGVYrF3gKWC4geFW7s",
														"user" => "gefTqEJ3e8T7Jksed686KPvJAirwxr",
														"message" => "Необходимо ввести капчу и снова запустить скрипт",
														"title" => "Автослив остановлен",
														"url" => "http://nc.default.ovh/followNotification.php?acc_id=".$_ACC['id'],
														"url_title" => "Перейти на страницу ввода",
														),
														CURLOPT_SAFE_UPLOAD => true,
														));
														curl_exec($ch2);
													}*/
												
												// Солнце мое ясное
												// Рано не вставай
												// С миленькой побыть мне
												DIE; DIE; DIE;
												exit();
											}
										// echo '<b>'.$url.'</b>';
										$array = array
											(
												'"themes/night' => '"http://nations.mgates.ru/conflict/themes/night',
												'href="game.php?q=' => 'href="game.php?acc_id='.$_ACC['id'].'&q=',
												'</form>' => '<input type="hidden" name="acc_id" value="'.$_ACC['id'].'" /></form>',
												'src="ci' => 'src="http://nations.mgates.ru/conflict/ci',
												'game.php"' => 'game.php?acc_id='.$_ACC['id'].'"',
												'src="files' => 'src="http://nations.mgates.ru/conflict/files',
												'action="game.php?' => 'action="game.php?acc_id='.$_ACC['id'].'&',
												'</head>' => '<link rel="icon" type="image/x-icon" href="http://nations.mgates.ru/favicon.ico" /></head>',
												'<body>' => '<body><i>Отряд №'.$counter.', arm: '.$arm.', рес: '.$resources.'</i>',
												'/conflict/game.php' => '/game.php',
												'img src="images' => 'img src="http://nations.mgates.ru/conflict/images',
												'captcha.php?' => 'captcha.php?acc_id='.$_ACC['id'].'&',
												'"/game.php?' => '"/game.php?acc_id='.$_ACC['id'].'&',
												'mc.yandex.ru' => '4nmv.ru',
												'<div style="color:#000000;background-color: #FFFFFF;margin:0px auto;padding:1px;"><a style="color: #000000;" href="http://wap.sasisa.ru"><img src="http://nasimke.ru/images/site/sasisa.gif" alt=""/>SaSiSa</a> / <a style="color: #000000;" href="http://wap.sasisa.ru/games.php">Игры</a></div>' => '',
												'href="files' => 'href="http://nations.mgates.ru/conflict/files'
											);

										// обработка вывода
										$output = $a;
										if(preg_match("#class\=\"alert alert_error\">входа нет!#u", $a))
											{
												fatalError('Входа нет');
											}
										foreach($array as $old => $new)
											{
												$output = str_replace($old, $new, $output);
											}
										echo $output;
										
										preg_match("#(\d+) arm\!\!#u", $output, $tmmp);
										if(isset($tmmp[1]))
											{
												$arm += $tmmp[1];
												mysql_query("UPDATE `accounts` SET `arm_hole` = `arm_hole` + ".$tmmp[1]." WHERE `id` = ".$_ACC['id']);
											}
										
										
										preg_match("#res\.\d+\.png\" alt=\"\.\" /> (\d+)#u", $output, $xuj);
										if(isset($xuj[1]))
											{
												$resources += $xuj[1];
												mysql_query("UPDATE `accounts` SET `res_hole` = `res_hole` + ".$xuj[1]." WHERE `id` = ".$_ACC['id']);
											}
										
										// die();
										echo '<hr size="3" />';
										
										$counter++;
										mysql_query("UPDATE `accounts` SET `unit_hole` = `unit_hole` + 1 WHERE `id` = ".$_ACC['id']);
										usleep(300000);
									}
								
							}
					}
			}
		else
			{
				break;
			}
	}
echo good('Завершено!
Отрядов: '.$counter.', arm: '.$arm.', ресурсы: '.$resources);
//mysql_query("UPDATE `accounts` SET `arm_hole` = `arm_hole` + ".$arm." WHERE `id` = ".$_ACC['id']);
//mysql_query("UPDATE `accounts` SET `unit_hole` = `unit_hole` + ".$counter." WHERE `id` = ".$_ACC['id']);

mysql_query("UPDATE `accounts` SET `notification` = 'Слив на яму завершен. Отрядов: ".$counter.", arm: ".$arm."', ресурсы: ".$resources." `notification_url` = '/game.php?acc_id=".$_ACC['id']."' WHERE `id` = ".$_ACC['id']);

getFooter();
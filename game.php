<?php
$starttime = microtime();
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$id);
if(mysql_num_rows($q) == 1)
	{
		$_ACC = mysql_fetch_assoc($q);
		if($_ACC['bot'] == 0)
			{
				if($_ACC['id_user'] != $_INFO['id'])
					{
						# проверка на расшаренный доступ
						$q2 = mysql_query("SELECT * FROM `access_list` WHERE `id_user` = ".$_INFO['id']." AND `acc_id` = ".$id);
						if(mysql_num_rows($q2) < 1)
							{
								redirect('/?no_access=1');
							}
					}
			}
	}

else
	{
		redirect('/?not_found');
	}

if(isset($_GET['rem_not']))
	{
		mysql_query("UPDATE `accounts` SET `notification` = '', `notification_url` = '' WHERE `id` = ".$_ACC['id']);
		$_ACC['notification'] = '';
		$_ACC['notification_url'] = '';
	}
//die(print_r($_ACC));

// убираем лишнее из query
$query = str_replace('?acc_id='.$_ACC['id'], '?' , $_SERVER['QUERY_STRING']);
$query = str_replace('&acc_id='.$_ACC['id'], '&' , $_SERVER['QUERY_STRING']);
$query = str_replace('&amp;acc_id='.$_ACC['id'], '&amp;' , $_SERVER['QUERY_STRING']);
$query = str_replace('&rem_not=1', '', $_SERVER['QUERY_STRING']);

$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?'.$query);

// прокси
curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

// всякая хуйня
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';map_cells=10;');
curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);

// отправка POST (если есть)
if(!@empty($_POST))
	{
		curl_setopt($ch, CURLOPT_POST, true);
		$postdata = array();
		foreach($_POST as $key => $value)
			{
				if($key != 'acc_id')
					{
						$postdata[$key] = $value;
					}
			}
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
	}

// запрос
$a = curl_exec($ch);
if(preg_match("#воинов вернулось#u", $a) && $_GET['q'] == 'control')
	{
		mysql_query("UPDATE `accounts` SET `unit_hole` = `unit_hole` + 1 WHERE `id` = ".$_ACC['id']);
		preg_match("#(\d+) arm\!\!#u", $a, $tmmp);
		if(isset($tmmp[1]))
			{
				mysql_query("UPDATE `accounts` SET `arm_hole` = `arm_hole` + ".$tmmp[1]." WHERE `id` = ".$_ACC['id']);
			}
		
		preg_match("#res\.\d+\.png\" alt=\"\.\" /> (\d+)#u", $a, $xuj);
		if(isset($xuj[1]))
			{
				mysql_query("UPDATE `accounts` SET `res_hole` = `res_hole` + ".$xuj[1]." WHERE `id` = ".$_ACC['id']);
			}
	}

if(empty($a))
	{
		$ping_proxy = exec('ping '.$_ACC['proxy_ip'].' -W 1 -c 5', $result_proxy);
		$ping_conf = exec('ping nations.mgates.ru -W 1 -c 5', $result_conf);
		// 2 и 3
		$error = '
		<button class="btn btn-primary" onclick="location.reload();">Обновить</button><br />Не удается установить соединение с сервером игры.<br /><br />
		Проверка доступности прокси-сервера:<br />
		<i>'.$result_proxy[0].'</i><br />
		<b>'.$result_proxy[count($result_proxy) - 2].'</b><br /><br />
		Проверка доступности сервера игры:<br />
		<i>'.$result_conf[0].'</i><br />
		<b>'.$result_conf[count($result_conf) - 2].'</b>';
		fatalError($error);
	}

// проверка на предмет обрыва сессии

if(preg_match("#<title>онлайн игры для телефона</title>#u", $a))
	{
		fatalError('Сессия устарела. Нажмите <a href="/autoLogin.php?id='.$id.'">сюда</a>, что обновить ее или <a href="/">вернитесь на главную</a>');
	}

// правила обработки
$array = array
	(
		'="dig_direction.php?' => '="dig_direction.php?acc_id='.$_ACC['id'].'&',
		'"themes/night' => '"http://nations.mgates.ru/conflict/themes/night',
		'href="game.php?q=' => 'href="game.php?acc_id='.$_ACC['id'].'&q=',
		'</form>' => '<input type="hidden" name="acc_id" value="'.$_ACC['id'].'" /></form>',
		'src="ci' => 'src="http://nations.mgates.ru/conflict/ci',
		'game.php"' => 'game.php?acc_id='.$_ACC['id'].'"',
		'src="files' => 'src="http://nations.mgates.ru/conflict/files',
		'action="game.php?' => 'action="game.php?acc_id='.$_ACC['id'].'&',
		'</head>' => '<link rel="icon" type="image/x-icon" href="http://nations.mgates.ru/favicon.ico" /></head>',
		'/conflict/game.php' => '/game.php',
		'img src="images' => 'img src="http://nations.mgates.ru/conflict/images',
		'<body>' => '<body><div style="text-align: center; font-size: 13px; color: #ccc;"><a href="/">Главная</a> / <a href="/tasks.php">Задания</a> / Слив: <a href="/catacomb.php?acc_id='.$_ACC['id'].'" onclick="return confirm(\'Уверены?\')">[1:1]</a> &amp; <a href="/catacomb.php?acc_id='.$_ACC['id'].'&hole=40000" onclick="return confirm(\'Уверены?\')">[200:200]</a> / <a href="/chat.php">Чат</a> / <a href="/logout.php">Выход</a></div>',
		'captcha.php?' => 'captcha.php?acc_id='.$_ACC['id'].'&',
		'"/game.php?' => '"/game.php?acc_id='.$_ACC['id'].'&',
		'mc.yandex.ru' => '4nmv.ru',
		'<div style="color:#000000;background-color: #FFFFFF;margin:0px auto;padding:1px;"><a style="color: #000000;" href="http://wap.sasisa.ru"><img src="http://nasimke.ru/images/site/sasisa.gif" alt=""/>SaSiSa</a> / <a style="color: #000000;" href="http://wap.sasisa.ru/games.php">Игры</a></div>' => '',
		'</body>' => '<span style="color: #ccc;">Прокси: <b>'.$_ACC['proxy_ip'].':'.$_ACC['proxy_port'].'</span></body>',
		'href="files' => 'href="http://nations.mgates.ru/conflict/files',
		'from=game.php?acc_id='.$_ACC['id'].'&q=' => '&'
	);

// обработка вывода
$output = $a;
foreach($array as $old => $new)
	{
		$output = str_replace($old, $new, $output);
	}

// обработка археологии !!!

if(isset($_GET['action_id']))
	{
		if($_GET['action_id'] == 23)
			{
				$id_unit = (int)$_GET['id_unit'];
				$f = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/dig_direction.php?id_unit='.$id_unit);
				shell_exec("cuneiform -l rus -o '/var/www/nc.default.ovh/imgs/".$id_unit.".txt' '/var/www/nc.default.ovh/imgs/".$id_unit.".gif'");
				
				$addi =  file_get_contents('imgs/'.$id_unit.'.txt');
				
				$output = str_replace('<img src="dig_direction.php?', '<span style="font-size: 25px;">'.$addi.'</span><br /><img src="dig_direction.php?', $output);
			}
	}

// активные notifiction
if(!empty($_ACC['notification']) && !empty($_ACC['notification_url']))
	{
		$not = '<div class="mail_message"><div class="mail_message_title">Уведомление</div><div class="mail_message_body">'.$_ACC['notification'].'<br />
		Пройдите по <a href="/followNotification.php?acc_id='.$_ACC['id'].'">ссылке</a></div></div>';
		$output = str_replace('<body>', '<body>'.$not, $output);
	}

$endtime = microtime();
list($sec, $msec) = explode(' ', $starttime);
$start = $sec + $msec;
list($sec, $msec) = explode(' ', $endtime);
$end = $sec + $msec;

$res = round($end - $start, 5);

$output = str_replace('</body>', '<br />'.date('H:i:s').'<br />'.$res.'</body>', $output);

echo $output;
writeLog($_INFO['id'], $id, 'game', '<b>'.$_INFO['login'].':'.$_ACC['name'].', game.php?'.$query.'</b>');
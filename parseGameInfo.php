<?php
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

setTitle('Обновление информации об аккаунте');
getHeader();

# просто главная
$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php');
// прокси
curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
// всякая хуйня
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);


$a = curl_exec($ch);
echo 'Открыта главная страница<br /><br />';

# рынок

preg_match("#&id_building\=(\d+)\">Рынок</a>#u", $a, $res);
$id_trade = isset($res[1]) ? (int)$res[1] : 0;

echo 'ID рынка: '.$id_trade.'<br />';

if(mysql_query("UPDATE `accounts` SET `trade_id` = ".$id_trade." WHERE `id` = ".$id))
	{
		echo 'Обновлено<br /><br />';
	}
else
	{
		echo 'Ошибка обновления, продолжаем...<br /><br />';
	}


# храм

preg_match("#&id_building\=(\d+)\">Храм</a>#u", $a, $res);

$id_cathedral = isset($res[1]) ? (int)$res[1] : 0;

echo 'ID храма: '.($id_cathedral == 0 ? 'не найден' : $res[1]);

if($id_cathedral == 0)
	{
		echo '<br /><br />Выполняем автовход...<!--http://'.$_SERVER['HTTP_HOST'].'/autoLogin.php?id='.$id.'--><br /><br />';
		$f = file_get_contents('http://'.$_SERVER['HTTP_HOST'].'/autoLogin.php?id='.$id);
		echo 'Автовход выполнен. Попытка №2...<br /><br />';
		preg_match("#&id_building\=(\d+)\">Храм</a>#u", $a, $res);
		$id_cathedral = isset($res[1]) ? (int)$res[1] : 0;
		echo 'ID храма: '.($id_cathedral == 0 ? 'не найден' : $res[1]);
	}

echo '<br />';
if(mysql_query("UPDATE `accounts` SET `cathedral_id` = ".$id_cathedral." WHERE `id` = ".$id))
	{
		echo 'Обновлено<br /><br />';
	}
else
	{
		echo 'Ошибка обновления, продолжаем...<br /><br />';
	}

# союз

$ch2 = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=union');
// прокси
curl_setopt($ch2, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
curl_setopt($ch2, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
// всякая хуйня
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch2, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
curl_setopt($ch2, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch2, CURLOPT_TIMEOUT, 8);
$a2 = curl_exec($ch2);
preg_match("#id\=(\d+)\">Казна</a>#u", $a2, $res);
$id_union = isset($res[1]) ? (int)$res[1] : 0;
echo 'ID казны союза: '.($id_union == 0 ? 'не найден' : $res[1]);
echo '<br />';

if(mysql_query("UPDATE `accounts` SET `id_union` = ".$id_union." WHERE `id` = ".$id))
	{
		echo 'Обновлено<br /><br />';
	}
else
	{
		echo 'Ошибка обновления, продолжаем...<br /><br />';
	}


# маста и черты
preg_match("#(\d+)\">Мастерская</a>#u", $a, $result);
$id_mast = isset($result[1]) ? (int)$result[1] : 0;

echo 'ID мастерской: '.($id_mast == 0 ? '<b>не найден</b>' : $id_mast);
echo '<br />';
if($id_mast == 0)
	{
		echo 'Прерывание... не найдена мастерская';
		getFooter();
		exit;
	}

// мастерская
if(mysql_query("UPDATE `accounts` SET `id_mast` = ".$id_mast." WHERE `id` = ".$id))
	{
		echo 'Обновлено<br /><br />';
	}
else
	{
		echo 'Ошибка обновления, продолжаем...<br /><br />';
	}

echo 'Засыпаем на 1 сек<br /><br />';
sleep(1);


# мастерская открывается
$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$id_mast.'&selected_tab=start');
// прокси
curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
// всякая хуйня
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);

$a = curl_exec($ch);


echo 'Открыта мастерская. Получаем список чертежей...<br /><br />';


preg_match_all('/id_pattern=(\d+).*? <div class="links_item_title item_(?:\d+)">(.*?)<\/div>/si', $a, $result);



// массив $result[1] - id чертежа
// массив $result[2] - имя чертежа

// создаем массив массивов
$toWrite = array();

echo 'Очищаем базу чертежей<br />';
if(mysql_query("DELETE FROM `patterns` WHERE `acc_id` = ".$id))
	{
		echo 'База чертежей очищена<br /><br />';
	}
else
	{
		echo 'Ошибка удаления чертежей! '.mysql_error();
		echo '<br />Прерываем...';
		getFooter();
		exit;
	}
foreach($result[1] as $key => $id_pattern)
	{
		$db['id_pattern'] = (int)$id_pattern;
		$db['pattern_name'] = trim($result[2][$key]);
		
		// получаем информацию о чертежей
		$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$id_mast.'&selected_tab=start&id_pattern='.$id_pattern);
		// прокси
		curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
		// всякая хуйня
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
		curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		$a = curl_exec($ch);
		//echo $a;
		
		
		#### Атака
		preg_match("#attack\.png\" alt\=\"\.\" />((\d+)\-(\d+))#u", $a, $tmp);
		$db['pattern_attack'] = trim($tmp[1]);
		
		#### Защита
		preg_match("#defence.png\" alt\=\"\.\" />(\d+)#u", $a, $tmp);
		$db['pattern_defence'] = (int)$tmp[1];
		
		#### HP
		preg_match("#hp\.png\" alt\=\"\.\" />(\d+)#u", $a, $tmp);
		$db['pattern_hp'] = (int)$tmp[1];
		
		#### Дрова
		preg_match_all("#res\.2\.png\" alt\=\"\.\" /> (\d+)#u", $a, $tmp);
		$db['pattern_wood'] = (int)$tmp[1][1];
		
		#### Руда
		preg_match_all("#res\.4\.png\" alt\=\"\.\" /> (\d+)#u", $a, $tmp);
		$db['pattern_coal'] = isset($tmp[1][1]) ? (int)$tmp[1][1] : 0; // ибо может и не быть
		
		#### Время
		preg_match("#time\.png\" alt\=\"\.\" />([0-9\.]{1,}) сек\.#u", $a, $tmp);
		$db['pattern_time'] = trim($tmp[1]);
		
		#### Тип оружия
		preg_match("#unit_group_(\d+)\.png#u", $a, $tmp);
		$db['pattern_type'] = (int)$tmp[1];
		
		echo '<pre>';
		print_r($db);
		if(mysql_query("INSERT INTO `patterns` (`acc_id`, `id_pattern`, `pattern_name`, `pattern_attack`, `pattern_defence`, `pattern_hp`, `pattern_wood`, `pattern_coal`, `pattern_time`, `pattern_type`) VALUES (".$id.", ".$db['id_pattern'].", '".$db['pattern_name']."', '".$db['pattern_attack']."', ".$db['pattern_defence'].", ".$db['pattern_hp'].", ".$db['pattern_wood'].", ".$db['pattern_coal'].", '".$db['pattern_time']."', '".$db['pattern_type']."')"))
			{
				echo 'Чертеж добавлен!';
			}
		else
			{
				echo 'Ошибка добавления! '.mysql_error();
			}
		echo '</pre>';
	}
echo '<br />';

getFooter();
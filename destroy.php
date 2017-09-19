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
				redirect('/?no_access=1');
			}
	}
else
	{
		redirect('/?not_found');
	}

if(isset($_POST['button']))
	{
		$error = array();
		if(isset($_POST['start']))
			{
				$start = (int)$_POST['start'];
			}
		else
			{
				$error[] = 'Минимальный размер отряда не указан';
			}
		
		if(isset($_POST['end']))
			{
				$end = (int)$_POST['end'];
			}
		else
			{
				$error[] = 'Максимальный размер отряда не указан';
			}
		
		if(empty($error))
			{
				$result = 0;
				// print_r($_GET);
				// открываем войска и смотрим список страниц
				// Открываем локацию
				$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=command');
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
				preg_match_all("#p_units=(\d+)#u", $a, $arr);
				if(empty($arr[1]))
					{
						$max = 1;
					}
				else
					{
						$max = max($arr[1]);
					}
				// циклом открываем все страницы разом, НАХУЙ
				$output = '';

				for($i = 1; $i <= $max; $i++)
					{
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=command&p_units='.$i);
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
						$output .= $a;
					}
				// die($output);
				
				
				// 1 - Отряды
				// 2 - легионы
				// 3 - все
				if(isset($_POST['destroy_type']))
					{
						switch($_POST['destroy_type'])
							{
								case '1':
									$pattern = '\&id\_unit\=(\d+)\">(?:[а-яА-Яa-zA-Z]{1,})</a><br/><span>\((\d+)';
									$destroy_type = 1;
								break;
								
								case '2':
									$pattern = 'id_unit=(\d+)">(?:\d+)\-й легион</a><br/>(?:[\s]{1,})<span>\((\d+)';
									$destroy_type = 2;
								break;
								
								
								default: 
								$pattern = '\&id\_unit\=(\d+)\">(?:[а-яА-Яa-zA-Z]{1,})</a><br/><span>\((\d+)';
								$destroy_type = 1;
							}
					}
				else
					{
						$pattern = '\&id\_unit\=(\d+)\">(?:[а-яА-Яa-zA-Z]{1,})</a><br/><span>\((\d+)';
						$destroy_type = 1;
					}
				// die($pattern);
				preg_match_all("#".$pattern."#u", $output, $res);
				// echo '<pre>';
				//print_r($res);
				//die;

				foreach($res[2] as $key => $num)
					{
						if($num >= $start && $num <= $end)
							{
								$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?acc_id=4&q=control&id_unit='.$res[1][$key].'&action_id=11&cnf=1');
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
								$result++;
							}
					}
				outputExit('Готово. Расформировано '.($destroy_type == 1 ? 'отрядов' : 'легионов').': '.$result);
			}
	}

setTitle('Расформировывание отрядов');
getHeader();
formError(isset($error) ? $error : '');
?>

<div class="alert alert-info" role="alert">Скрипт расформирует отряды и/или легионы с указанной численностью (включительно). Если идет расформирование небольших отрядов, необходимо включить показ разведки. Скрипт может выполняться очень долго, в зависимости от количества страниц в разделе &quot;Управление войсками&quot;</div>
<form action="/destroy.php?acc_id=<?=$_ACC['id']?>" method="post">
Уничтожить 
<select name="destroy_type" class="form-control" style="display: inline-block; max-width: 125px;">
	<option value="1">Отряды</option>
	<option value="2">Легионы</option>
</select>

 численностью от
<input type="text" name="start" class="form-control" size="4" style="display: inline-block; max-width: 100px;" /> до 
<input type="text" name="end" class="form-control" size="4" style="display: inline-block; max-width: 100px;" /> человек<br /><br />
<input type="submit" name="button" class="btn btn-warning" value="Уничтожить" /></form>



<?php
getFooter();
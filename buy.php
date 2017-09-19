<?php
require_once 'inc/core.php';
autOnly();

if($_INFO['bot_access'] != '1')
{
	fatalError('Доступ запрещен');
}

if(isset($_POST['button']))
	{
		// print_r($_POST);
		$error = array();
		if(isset($_POST['id_lot']))
			{
				$id_lot = (int)$_POST['id_lot'];
			}
		else
			{
				$error[] = 'Номер лота?';
			}
	
		if(isset($_POST['nation']))
			{
				$nation = (int)$_POST['nation'];
				
				if($nation != 1 && $nation != 2)
					{
						$error[] = 'Неизвестная нация';
					}
			}
		else
			{
				$error[] = 'Нация?';
			}
		
		if(isset($_POST['price']))
			{
				$price = (float)$_POST['price'];
				if($price <= 0)
					{
						$error[] = 'Деление на ноль?';
					}
			}
		else
			{
				$error[] = 'Цена за единицу?';
			}
		
		if(empty($error))
			{
				$q = mysql_query("SELECT * FROM `accounts` WHERE `bot` = '1' AND `nation` = '".$nation."' AND `trade_id` != 0");
				echo mysql_num_rows($q);
				while($_ACC = mysql_fetch_assoc($q))
					{
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$_ACC['trade_id'].'&id_trade='.$id_lot);
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						$a = curl_exec($ch);
						echo $a;
						
						preg_match("#На счету\: <span class\=\"bonus_color\">(\d+) arm</span>#u", $a, $res);
						
						$arm = (int)$res[1];
						$am = $arm / $price;
						

					
						$ch = curl_safe_init('http://nations.mgates.ru/conflict/game.php?q=building&id_building='.$_ACC['trade_id'].'&id_trade='.$id_lot.'&a=buy&cnf=1&amount='.$am);
						curl_setopt($ch, CURLOPT_PROXY, $_ACC['proxy_ip'].':'.$_ACC['proxy_port']);
						curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);

						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, true);
						curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
						curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
						curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
						$a = curl_exec($ch);
						echo $a;
						sleep(1);
					}
			}
	}

setTitle('Слив арм');
getHeader();
formError(isset($error) ? $error : '');
//die(print_r($_INFO));
?>

<form action="/buy.php" method="POST">
<input type="text" class="form-control" name="id_lot" placeholder="ID лота" value="<?=isset($_POST['id_lot']) ? (int)$_POST['id_lot'] : ''?>" /><br />
<input type="text" class="form-control" name="price" placeholder="Цена за единицу" value="<?=isset($_POST['price']) ? (float)$_POST['price'] : ''?>" /><br />
<select name="nation" class="form-control">
<option value="1">Рим</option>
<option value="2">Греция</option>
</select><br />
<input type="submit" name="button" value="Поехали" class="btn btn-default" /></form>

<?php
getFooter();
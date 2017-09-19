<?php
require 'inc/core.php';
autOnly();

$c1 = mysql_num_rows(mysql_query("SELECT * FROM `accounts` WHERE `id_user` = ".$_INFO['id']));

if($c1 >= $_INFO['max'])
	{
		fatalError('Вы уже добавили максимальное количество аккаунтов, определенное для вашей учетной записи ('.$_INFO['max'].')');
	}

if(isset($_POST['button']))
	{	
		$error = array();
		//die(print_r($_POST));
		// обязательные поля
		$arr = array
			(
				'login' => 'Логин',
				'pass' => 'Пароль',
				'proxy_ip' => 'IP прокси',
				'proxy_port' => 'Порт прокси',
				'ua' => 'User-agent',
				'nation' => 'Нация'
			);
		foreach($arr as $input => $name)
			{
				if(!isset($_POST[$input]))
					{
						$error[] = 'Не заполнено поле '.$name;
					}
				else
					{
						$db[$input] = mysql_real_escape_string($_POST[$input]);
					}
			}
		
		// не столь важно
		$db['autologin'] = $_POST['autologin'] ? 1 : 0;
		$db['refresh'] = $_POST['refresh'] ? 1 : 0;
		$db['archeology'] = $_POST['archeology'] ? '1' : '0';
		$db['proxy_user'] = isset($_POST['proxy_user']) ? filterText($_POST['proxy_user'], 100) : '';
		$db['proxy_pass'] = isset($_POST['proxy_pass']) ? filterText($_POST['proxy_pass'], 200) : '';
		$db['name'] = isset($_POST['name']) ? filterText($_POST['name']) : @$db['login'];
		$db['bot'] = $_POST['bot'] ? 1 : 0;
		$db['bot'] == 1 ? $ownerID = 5 : $ownerID = $_INFO['id'];
		if(empty($error))
			{
				if(mysql_query("INSERT INTO `accounts`(`id_user`, `login`, `pass`, `proxy_ip`, `proxy_port`, `autologin`, `refresh`, `proxy_user`, `proxy_pass`, `name`, `ua`, `bot`, `nation`, `archeology`) VALUES (".$ownerID.", '".$db['login']."', '".$db['pass']."', '".$db['proxy_ip']."', '".$db['proxy_port']."', '".$db['autologin']."', '".$db['refresh']."', '".$db['proxy_user']."', '".$db['proxy_pass']."', '".$db['name']."', '".$db['ua']."', ".$db['bot'].", '".$db['nation']."', '".$db['archeology']."')"))
					{
						$__id = mysql_insert_id();
						mysql_query("UPDATE `proxy` SET `used` = 1 WHERE `ip` = '".$db['proxy_ip']."'");
						redirect('/autoLogin.php?id='.$__id);
					}
				else
					{
						fatalError(mysql_error());
					}
			}
	}

setTitle('Добавление аккаунта');
getHeader();
formError(isset($error) ? $error : '');
$q = mysql_query("SELECT * FROM `proxy` WHERE `used` = 0 ORDER BY RAND() LIMIT 1");

?>

<div class="panel panel-default">
  <div class="panel-heading">Справка</div>
  <div class="panel-body">
   <p>Автовход включает автоматическое обновление cookies в игре путем повторного захода под логином/паролем на mgates. Совершается каждые полчаса</p>
   <p>Накрутка онлайна - хранит вечный онлайн в игре путем открытия произвольных страниц каждую минуту</p>
   <p>Галочка &quot;Бот&quot; отвязывает созданную учетку от текущего аккаунта и переносит ее в общий раздел ботов с доступом для всех</p>
   <p>Арехология включает режим автоматического ведения раскопок</p>
   <p>User-agent - то, каким браузером будет представляться скрипт серверу. Генерируется автоматически, можно задать по своему усмотрению</p>
   </div>
</div>
<?php
if(mysql_num_rows($q) == 1)
	{
		$randProxy = mysql_fetch_assoc($q);
		$botid = mysql_num_rows(mysql_query("SELECT * FROM `accounts` WHERE `bot` = 1")) + 1;
	?>
<script>
$(function () {
	$('#autoproxy').click(function (e) {
		if (this.checked) {
			$('#proxy_ip').val('<?=$randProxy['ip']?>');
			$('#proxy_port').val('<?=$randProxy['port']?>');
			$('#proxy_login').val('<?=$randProxy['login']?>');
			$('#proxy_password').val('<?=$randProxy['pass']?>');
			$('#autoname').val('Bot <?=$botid?>');
		}
		else
		{
			$('#proxy_ip').val('');
			$('#proxy_port').val('');
			$('#proxy_login').val('');
			$('#proxy_password').val('');
			$('#autoname').val('');
		}
	});
});
</script>
<!--<input type="checkbox" name="autoproxy" id="autoproxy" />&nbsp;Взять прокси из базы<br />-->
<?php
	}
?>
<form action="/add.php" method="post">
<input type="checkbox" name="bot" id="bot" />&nbsp;Бот (для слива арм)<br />
<input type="text" name="login" placeholder="Логин mgates *" required="required" value="<?=isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''?>" class="form-control" /><br />
<input type="text" name="pass" placeholder="Пароль mgates *" required="required" value="<?=isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : ''?>" class="form-control" /><br />
<select name="nation" class="form-control">
<option value="1">Рим</option>
<option value="2">Греция</option>
</select><br />
<input type="text" name="proxy_ip" placeholder="IP прокси*" id="proxy_ip" required="required" value="<?=isset($_POST['proxy_ip']) ? htmlspecialchars($_POST['proxy_ip']) : ''?>" class="form-control" /><br />
<input type="text" name="proxy_port" placeholder="Порт прокси*" id="proxy_port" required="required" value="<?=isset($_POST['proxy_port']) ? htmlspecialchars($_POST['proxy_port']) : ''?>" class="form-control" /><br />
<input type="text" name="ua" placeholder="User-agent *" required="required" value="<?=isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : chooseBrowser();?>" class="form-control" /><br />
<input type="text" name="proxy_user" id="proxy_login" placeholder="Пользователь прокси" value="<?=isset($_POST['proxy_user']) ? htmlspecialchars($_POST['proxy_user']) : ''?>" class="form-control" /><br />
<input type="text" name="proxy_pass" id="proxy_password" placeholder="Пароль прокси" value="<?=isset($_POST['proxy_pass']) ? htmlspecialchars($_POST['proxy_pass']) : ''?>" class="form-control" /><br />
<input type="text" name="name" id="autoname" placeholder="Имя аккаунта (произвольно)" value="<?=isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''?>" class="form-control" /><br />
<input type="checkbox" name="autologin" checked="checked" />&nbsp;Автовход<br />
<input type="checkbox" name="refresh" checked="checked" />&nbsp;Накрутка онлайна<br />
<input type="checkbox" name="archeology" checked="checked" />&nbsp;Археология<br />
<input type="submit" name="button" class="btn btn-default" value="Добавить" />
</form>


<?php
getFooter();
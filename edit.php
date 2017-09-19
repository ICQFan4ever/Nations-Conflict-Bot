<?php
require 'inc/core.php';
autOnly();
$id = (int)$_GET['acc_id'];

if($id == 26)
	{
		fatalError('Невозможно редактировать тестовый аккаунт');
	}

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



if(isset($_POST['button']))
	{	
		$error = array();
		$arr = array
			(
				'login' => 'Логин',
				'pass' => 'Пароль',
				'proxy_ip' => 'IP прокси',
				'proxy_port' => 'Порт прокси',
				'ua' => 'User-agent'
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
		$db['proxy_user'] = isset($_POST['proxy_user']) ? filterText($_POST['proxy_user'], 100) : $_ACC['proxy_user'];
		$db['proxy_pass'] = isset($_POST['proxy_pass']) ? filterText($_POST['proxy_pass'], 200) : $_ACC['proxy_pass'];
		$db['name'] = isset($_POST['name']) ? filterText($_POST['name']) : $_ACC['name'];
		if(empty($error))
			{
				if(mysql_query("UPDATE `accounts` SET `login` = '".$db['login']."', `pass` = '".$db['pass']."', `proxy_ip` = '".$db['proxy_ip']."', `proxy_port` = '".$db['proxy_port']."', `ua` = '".$db['ua']."', `proxy_user` = '".$db['proxy_user']."', `proxy_pass` = '".$db['proxy_pass']."', `name` = '".$db['name']."' WHERE `id` = ".$id))
					{
						if($_ACC['bot'] == '1')
							{
								redirect('/bots.php#acc'.$_ACC['id']);
							}
						else
							{
								redirect('/?success_upd');
							}
					}
				else
					{
						fatalError(mysql_error());
					}
			}
	}

setTitle('Правка аккаунта');
getHeader();
formError(isset($error) ? $error : '');
?>

<!--<?=print_r($_INFO)?>-->
<form action="/edit.php?acc_id=<?=$id?>" method="post">
<input type="text" name="login" placeholder="Логин mgates *" required="required" value="<?=isset($_POST['login']) ? htmlspecialchars($_POST['login']) : $_ACC['login']?>" class="form-control" /><br />
<input type="text" name="pass" placeholder="Пароль mgates *" required="required" value="<?=isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : $_ACC['pass']?>" class="form-control" /><br />
<input type="text" name="proxy_ip" placeholder="IP прокси*" required="required" value="<?=isset($_POST['proxy_ip']) ? htmlspecialchars($_POST['proxy_ip']) : $_ACC['proxy_ip']?>" class="form-control" /><br />
<input type="text" name="proxy_port" placeholder="Порт прокси*" required="required" value="<?=isset($_POST['proxy_port']) ? htmlspecialchars($_POST['proxy_port']) : $_ACC['proxy_port']?>" class="form-control" /><br />
<input type="text" name="ua" placeholder="User-agent *" required="required" value="<?=isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : $_ACC['ua']?>" class="form-control" /><br />
<input type="text" name="proxy_user" placeholder="Пользователь прокси" value="<?=isset($_POST['proxy_user']) ? htmlspecialchars($_POST['proxy_user']) : $_ACC['proxy_user']?>" class="form-control" /><br />
<input type="text" name="proxy_pass" placeholder="Пароль прокси" value="<?=isset($_POST['proxy_pass']) ? htmlspecialchars($_POST['proxy_pass']) : $_ACC['proxy_pass']?>" class="form-control" /><br />
<input type="text" name="name" placeholder="Имя аккаунта (произвольно)" value="<?=isset($_POST['name']) ? htmlspecialchars($_POST['name']) : $_ACC['name']?>" class="form-control" /><br />

<input type="submit" name="button" class="btn btn-default" value="Изменить" />
</form>


<?php
getFooter();
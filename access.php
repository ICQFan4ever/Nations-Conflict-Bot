<?php
require 'inc/core.php';
autOnly();
setTitle('Настройка доступа');
getHeader();

if(isset($_GET['act']))
	{
		# Создание ACL
		if($_GET['act'] == 'add')
			{
				if(isset($_POST['button']))
					{
						$error = array();
						if(isset($_POST['acc_id']))
							{
								$acc_id = (int)$_POST['acc_id'];
								$q = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$acc_id." AND `id_user` = ".$_INFO['id']);
								if(mysql_num_rows($q) != 1)
									{
										$error[] = 'Такого аккаунта не существует';
									}
							}
						else
							{
								$error[] = 'Выберите аккаунт, к которому необходимо предоставить доступ';
							}
						
						if(isset($_POST['id_user']))
							{
								$id_user = (int)$_POST['id_user'];
								if($id_user == $_INFO['id'])
									{
										$error[] = 'Нельзя предоставить доступ самому себе, он и так есть';
									}
								else
									{
										$q  = mysql_query("SELECT * FROM `users` WHERE `id` = ".$id_user);
										if(mysql_num_rows($q) != 1)
											{
												$error[] = 'Пользователь не найден';
											}
									}
							}
						else
							{
								$error[] = 'Выберите пользователя, которому предоставляется доступ';
							}
						
						
						if(empty($error))
							{
								# Проверим ACL на существование
								$q = mysql_query("SELECT * FROM `access_list` WHERE `id_user` = ".$id_user." AND `acc_id` = ".$acc_id);
								if(mysql_num_rows($q) != 0)
									{
										echo error('Такое правило уже существует');
									}
								else
									{
										if(mysql_query("INSERT INTO `access_list`(`acc_id`, `id_user`) VALUES (".$acc_id.", ".$id_user.")"))
											{
												echo good('Доступ предоставлен');
											}
										else
											{
												echo error(mysql_error());
											}
									}
							}
					}
			}
		
		if($_GET['act'] == 'delete')
			{
				$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
				$q = mysql_query("SELECT * FROM `access_list` WHERE `id` = ".$id);
				if(mysql_num_rows($q) != 1)
					{
						echo error('Не найдено');
					}
				else
					{
						$tmp = mysql_fetch_assoc($q);
						if($tmp['id_user'] != 1)
							{
								$q2 = mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$tmp['acc_id']." AND `id_user` = ".$_INFO['id']);
								if(mysql_num_rows($q2) == 1)
									{
										if(mysql_query("DELETE FROM `access_list` WHERE `id` = ".$id))
											{
												echo good('Доступ к аккаунту закрыт');
											}
									}
								else
									{
										echo error('Что-то тут не так...');
									}
							}
						else
							{
								echo error('Ты действительно хочешь закрыть мне доступ к аккаунту? Мне, создателю этого проекта? Действительно безумная затея');
							}
					}
			}
	}
					
?>

<div class="alert alert-info">Предоставление доступа другому пользователю дает ему полный контроль над игровым аккаунтом, однако не дает возможности изменять включенные опции и просматривать информацию об игровом аккаунте (логин, пароль и прочее). Однако пользователь может инициировать слив в яму и разведку на расшаренный аккаунт.</div>

<form action="/access.php?act=add" method="post">
Выберите аккаунт:<br />
<select name="acc_id" class="form-control">
<?php
$q1 = mysql_query("SELECT * FROM `accounts` WHERE `id_user` = ".$_INFO['id']);
while($info = mysql_fetch_assoc($q1))
	{
		?>
		<option value="<?=$info['id']?>"><?=$info['name']?></option>
		
		<?php
	}
?>
</select><br />
Выберите пользователя:<br />
<select name="id_user" class="form-control">
<?php
$q2 = mysql_query("SELECT * FROM `users` WHERE `id` != ".$_INFO['id']." ORDER BY `id` ASC");
while($info = mysql_fetch_assoc($q2))
	{
		?>
		<option value="<?=$info['id']?>"><?=$info['login']?></option>
		<?php
	}
?>

</select><br />
<input type="submit" name="button" value="Сохранить" class="btn btn-default" />
</form>

<h4>Существующие доступы</h4>

<?php

$q = mysql_query("select * from access_list where acc_id = (select id from accounts where id_user = ".$_INFO['id'].")");
if(mysql_num_rows($q) < 1)
	{
		echo error('Вы еще никому не предоставили доступ к аккаунту');
	}
else
	{
		while($inf = mysql_fetch_assoc($q))
			{
				$acc = mysql_fetch_assoc(mysql_query("SELECT * FROM `accounts` WHERE `id` = ".$inf['acc_id']));
				$user = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id` = ".$inf['id_user']));
				?>
				Пользователь <b><?=$user['login']?></b> имеет доступ к аккаунту <b><?=$acc['name']?></b>. <a href="access.php?act=delete&amp;id=<?=$inf['id']?>" onclick="return confirm('Закрыть доступ к аккаунту?');">Удалить?</a><br />
				
				<?php
			}
	}

getFooter();
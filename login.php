<?php
require 'inc/core.php';
if(isAut())
	{
		redirect('/');
	}

if(isset($_POST['button']))
	{
		$error = array();
		if(isset($_POST['code']))
			{
				if($_POST['code'] == $_SESSION['code'])
					{
						if(isset($_POST['login']))
							{
								$login = filterText($_POST['login']);
								if(isset($_POST['password']))
									{
										$password = md5(md5($_POST['password']));
										$q = mysql_query("SELECT * FROM `users` WHERE `login` = '".$login."' AND `password` = '".$password."'");
										if(mysql_num_rows($q) == 1)
											{
												$_INFO = mysql_fetch_assoc($q);
												
												if(!empty($_INFO['sid']))
													{
														$sid = $_INFO['sid'];
													}
												else
													{
														$sid = md5(md5(time().microtime()));
														mysql_query("UPDATE `users` SET `sid` = '".$sid."' WHERE `id` = ".$_INFO['id']);
													}
												setcookie('sid', $sid, time() + 86400 * 365, '/', $_SERVER['HTTP_HOST']);
												redirect('/');
											}
										else
											{
												$error[] = 'Неверный логин/пароль';
											}
									}
								else
									{
										$error[] = 'Пароль?';
									}
							}
						else
							{
								$error[] = 'Логин?';
							}
					}
				else
					{
						$error[] = 'Неверный код с картинки';
					}
			}
		else
			{
				$error[] = 'Введите код с картинки';
			}
	}

setTitle('Вход');
getHeader();
formError(isset($error) ? $error : '');
?>

<form action="/login.php" method="post">
<input type="text" name="login" placeholder="Логин" maxlength="30" class="form-control" /><br />
<input type="password" required="required" name="password" placeholder="Пароль" class="form-control" /><br />
<img src="/captcha2.php?rand=<?=rand(10000, 99999)?>" alt="" /><br />
<input type="text" name="code" placeholder="Текст с картинки" class="form-control" /><br />
<input type="submit" required="required" name="button" class="btn btn-default" value="Вход" />
</form>

<?php
getFooter();
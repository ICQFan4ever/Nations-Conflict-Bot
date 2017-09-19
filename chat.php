<?php

include 'inc/core.php';

autOnly();

if(isset($_POST['button']))
	{
		$error = array();
		
		if(isset($_POST['text']))
			{
				$text = filterText($_POST['text'], 500);
			}
		else
			{
				$error[] = 'Текст?';
			}
		
		if(empty($error))
			{
				if(mysql_query("INSERT INTO `chat`(`id_user`, `text`, `time`) VALUES (".$_INFO['id'].", '".$text."', ".time().")"))
					{
						redirect('/chat.php?'.(isset($_GET['widget']) ? 'widget=1' : ''));
					}
				else
					{
						fatalError(mysql_error());
					}
			}
	}



if(isset($_GET['widget']))
	{
	?>
<!DOCTYPE html>
<html>
<head>
<title>Чят</title>

<meta name="revisit-after" content="1 day" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="/style/css/bootstrap<?=$_INFO['theme']?>.css" />
<link rel="stylesheet" type="text/css" href="/style/css/user.css" />
<script src="https://yastatic.net/jquery/2.1.1/jquery.min.js"></script>
<script src="/style/js/bootstrap.min.js"></script>
</head>

<body style="margin: 5px;">
	<?php
	$numm = 1;
	}
else
	{
		setTitle('Чат');
		getHeader();
		$numm = 100;
	}
?>
<a href="/chat.php?rand=<?=rand(10000, 99999)?>&<?=isset($_GET['widget']) ? 'widget=1' : ''?>" style="font-size: x-small">обновить</a>
<form action="/chat.php?<?=isset($_GET['widget']) ? 'widget=1' : ''?>" method="post">
<input type="text" name="text" id="txt" value="" class="form-control" aria-describedby="sizing-addon3">
<input type="submit" name="button" class="btn-xs" />
</form>
<br />

<?php

$q = mysql_query("SELECT * FROM `chat` ORDER BY `id` DESC LIMIT ".$numm);

while($msg = mysql_fetch_assoc($q))
	{
		$user = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE `id` = ".$msg['id_user']));
		?>
		<i>(<?=date('d') == date('d', $msg['time']) ? date('H:i', $msg['time']) : date('d.m.Y', $msg['time'])?>)</i> <a href="#" onclick="$('#txt').val($(this).text() + ', '); return false;"><?=$user['login'];?></a>: <?=$msg['text']?><br />
		
		<?php
	}

if(isset($_GET['widget']))
	{
		?>
		</body>
		<?php
	}
else
	{
		getFooter();
	}
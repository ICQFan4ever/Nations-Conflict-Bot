<?php

$idd = isset($_GET['id_unit']) ? (int)$_GET['id_unit'] : 164578786 ;

$ch = curl_safe_init('http://nations.mgates.ru/conflict/dig_direction.php?id_unit='.$idd);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 12);

$a = curl_exec($ch);

### сэйвим файл, для примера

$name = $idd.'.gif';
$ff = fopen('imgs/'.$name, 'w');
flock($ff, LOCK_EX);
fwrite($ff, $a);
flock($ff, LOCK_UN);
fclose($ff);

// echo $a;


echo '<img src="imgs/'.$name.'" /><br />';
shell_exec("cuneiform -l rus -o '/var/www/nc.default.ovh/imgs/".$idd.".txt' '/var/www/nc.default.ovh/imgs/".$name."'");
echo @file_get_contents('imgs/'.$idd.'.txt');

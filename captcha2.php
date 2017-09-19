<?php
require 'inc/core.php';
header("Content-type: image/png");
header("Pragma: no-cache");
# Генерируем число для капчи
$_SESSION['code'] = rand(10000, 99999);
$im = imagecreatefrompng("inc/captcha.png");
# Генерируем случайный цвет
$rand = 255;
$color = imagecolorallocate($im, $rand, $rand, $rand);
$black = imagecolorallocate($im, 255, 255, 255);
$str = 'nc.default.ovh';

# Делаем случайный угол и, исходя из этого, устанавливаем координаты
$pos = rand(0, 1);
if($pos == 0) 
	{
		$angle = mt_rand(343, 355);
		$x = 30;
		$y = 40;
		$x1 = 6;
		$y1 = 76;
	}
else
	{
		$angle = mt_rand(6, 18);
		$x = 30;
		$y = 70;
		$x1 = 100;
		$y1 = 76;
	}
# Рисуем URL
imagettftext($im, 10, 0, $x1, $y1, $black, 'inc/Ubuntu.ttf', $str);
# Прорисовываем код
imagettftext($im, 30, $angle, $x, $y, $color, 'inc/Ubuntu.ttf', $_SESSION['code']);
# Выводим в браузер
imagepng($im);
# Освобождаем память
imagedestroy($im);
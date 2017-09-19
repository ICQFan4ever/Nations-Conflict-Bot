<?php
# страница для тестирования
require 'inc/core.php';
autOnly();


$a = 30;
$b = 40;

echo $a == 230 ? 'a = 30' : ($b == 240 ? 'b = 40' : 'ничо');

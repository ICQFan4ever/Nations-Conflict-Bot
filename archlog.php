<?php
include 'inc/core.php';
autOnly();

setTitle('Последний лог археологии');
getHeader();

echo file_get_contents('data/archlog.txt');

getFooter();
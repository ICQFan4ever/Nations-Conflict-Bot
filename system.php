<?php
require 'inc/core.php';
autOnly();
setTitle('Системная информация');
getHeader();
?>


<h4>Потребление памяти</h4>
<pre>
<?=`free -m`?>
</pre>

<h4>Потребление ресурсов процессора</h4>
<pre>
<?=`ps aux | awk '{s += $3} END {print s "%"}'`?>
</pre>

<h4>Статистика базы данных</h4>
<pre>
<?php
$status = explode('  ', mysql_stat());
print_r($status);
?>
</pre>

<?php
getFooter();
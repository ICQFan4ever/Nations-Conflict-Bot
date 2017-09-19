<?php
require 'inc/core.php';
autOnly();
setTitle('Состояние PROXY');
getHeader();

echo '<div class="table-responsive"><table class="table">';
$q = mysql_query("SELECT * FROM `proxy_status` ORDER BY time DESC");
echo '<tr><th>Прокси</th><th>Состояние</th><th>Последняя проверка</th></tr>';
while ($s = mysql_fetch_assoc($q)) {
	echo '<tr class="'.($s['status'] ? 'success' : 'danger').'">';
	echo '<td>'.$s['proxy'].'</td>';
	echo '<td>'.$s['status_text'].'</td>';
	echo '<td>'.(date("Y/m/d", time()) == date("Y/m/d", $s['time']) ? date("H:i:s", $s['time']) : date("Y/m/d H:i:s", $s['time'])).'</td>';
	echo '</tr>';
}
echo '</table></div>';

getFooter();
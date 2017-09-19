<?php
require 'inc/core.php';

$q = mysql_query("SELECT * FROM `accounts` WHERE `refresh` = '1' ORDER BY `id` ASC LIMIT 0, 30");
while ($_ACC = mysql_fetch_assoc($q)) {
	$ch = curl_safe_init('http://zhumarin.ru/liza.txt');
	
	$proxy = $_ACC['proxy_ip'].':'.$_ACC['proxy_port'];
	
	curl_setopt($ch, CURLOPT_PROXY, $proxy);
	curl_setopt($ch, CURLOPT_PROXYUSERPWD, $_ACC['proxy_user'].':'.$_ACC['proxy_pass']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID='.$_ACC['phpsessid'].';enc='.$_ACC['enc'].';');
	curl_setopt($ch, CURLOPT_USERAGENT, $_ACC['ua']);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	$res = curl_exec($ch);
	
	if (preg_match("/naum pidor/", $res)) {
		$status = 1;
		$status_text = "OK";
	} else {
		$status = 0;
		$status_text = !curl_getinfo($ch, CURLINFO_HTTP_CODE) ? "Таймаут" : "HTTP:".curl_getinfo($ch, CURLINFO_HTTP_CODE);
	}
	
	mysql_query("REPLACE INTO proxy_status SET 
		proxy='".mysql_real_escape_string($proxy)."', 
		status='$status', 
		status_text='".mysql_real_escape_string($status_text)."', 
		time=".time()) or die(mysql_error());
	
	echo $_ACC['name']." | $proxy | $status_text\n";
}

mysql_query("DELETE FROM proxy_status WHERE time < ".(time() - 3600)) or die(mysql_error());

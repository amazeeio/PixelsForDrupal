<?php

$dbhost = MYSQL_HOST;
$dbusername = MYSQL_USER;
$dbpassword = MYSQL_PASS;
$database_name = MYSQL_DB;

if(!defined('MYSQL_PORT')) {
	define('MYSQL_PORT', 3306);
}
if(!defined('MYSQL_SOCKET')) {
	define('MYSQL_SOCKET', "");
}
$database_port = MYSQL_PORT;
$database_socket = MYSQL_SOCKET;

if (isset($dbhost) && isset($dbusername) && isset($database_name) && isset($database_port)) {
	if (!empty($dbhost) && !empty($dbusername) && !empty($database_name) && !empty($database_port)) {
		if(isset($database_socket) && !empty($database_socket)) {
			$GLOBALS['connection'] = mysqli_connect("$dbhost", "$dbusername", "$dbpassword", "$database_name", "$database_port", "$database_socket");
		} else {
			$GLOBALS['connection'] = mysqli_connect("$dbhost", "$dbusername", "$dbpassword", "$database_name", "$database_port");
		}
		if (mysqli_connect_errno()) {
			echo mysqli_connect_error();
			exit();
		}
		$db = mysqli_select_db($GLOBALS['connection'], "$database_name") or die(mysqli_error($GLOBALS['connection']));
		mysqli_set_charset($GLOBALS['connection'], 'utf8') or die(mysqli_error($GLOBALS['connection']));
	}
}

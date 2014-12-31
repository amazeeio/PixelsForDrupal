<?php

function check_connection($user, $pass, $host) {
	if (!($GLOBALS['connection'] = mysqli_connect("$host", "$user", "$pass"))) {
		return false;
	}

	return $GLOBALS['connection'];
}

function check_db($link, $db_name) {
	if (!($db = mysqli_select_db($link, "$db_name"))) {
		return false;
	}
	return true;
}

$dbhost = MYSQL_HOST;
$dbusername = MYSQL_USER;
$dbpassword = MYSQL_PASS;
$database_name = MYSQL_DB;

if (isset($dbhost) && isset($dbusername) && isset($database_name)) {
	if (!empty($dbhost) && !empty($dbusername) && !empty($database_name)) {
		$GLOBALS['connection'] = mysqli_connect("$dbhost", "$dbusername", "$dbpassword") or die(mysqli_error($GLOBALS['connection']));
		$db = mysqli_select_db($GLOBALS['connection'], "$database_name") or die(mysqli_error($GLOBALS['connection']));
		mysqli_set_charset($GLOBALS['connection'], 'utf8') or die(mysqli_error($GLOBALS['connection']));
	}
}

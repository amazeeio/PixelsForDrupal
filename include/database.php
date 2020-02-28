<?php
/**
 * @package        mds
 * @copyright      (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author         Ryan Rhode, ryan@milliondollarscript.com
 * @license        This program is free software; you can redistribute it and/or modify
 *		it under the terms of the GNU General Public License as published by
 *		the Free Software Foundation; either version 3 of the License, or
 *		(at your option) any later version.
 *
 *		This program is distributed in the hope that it will be useful,
 *		but WITHOUT ANY WARRANTY; without even the implied warranty of
 *		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *		GNU General Public License for more details.
 *
 *		You should have received a copy of the GNU General Public License along
 *		with this program;  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *		Million Dollar Script
 *		A pixel script for selling pixels on your website.
 *
 *		For instructions see README.txt
 *
 *		Visit our website for FAQs, documentation, a list team members,
 *		to post any bugs or feature requests, and a community forum:
 * 		https://milliondollarscript.com/
 *
 */

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

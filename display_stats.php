<?php
/**
 * @package        mds
 * @copyright    (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author        Ryan Rhode, ryan@milliondollarscript.com
 * @license        This program is free software; you can redistribute it and/or modify
 *        it under the terms of the GNU General Public License as published by
 *        the Free Software Foundation; either version 3 of the License, or
 *        (at your option) any later version.
 *
 *        This program is distributed in the hope that it will be useful,
 *        but WITHOUT ANY WARRANTY; without even the implied warranty of
 *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *        GNU General Public License for more details.
 *
 *        You should have received a copy of the GNU General Public License along
 *        with this program;  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *        Million Dollar Script
 *        A pixel script for selling pixels on your website.
 *
 *        For instructions see README.txt
 *
 *        Visit our website for FAQs, documentation, a list team members,
 *        to post any bugs or feature requests, and a community forum:
 *        https://milliondollarscript.com/
 *
 */

define('NO_HOUSE_KEEP', 'YES');

require('config.php');


$BID = $f2->bid($_REQUEST['BID']);

$banner_data = load_banner_constants($BID);

$sql = "select count(*) AS COUNT FROM blocks where status='sold' and banner_id='$BID' ";
$result = mysqli_query($GLOBALS['connection'], $sql);
$row = mysqli_fetch_array($result);
$sold = $row['COUNT'] * ($banner_data['BLK_WIDTH'] * $banner_data['BLK_HEIGHT']);

$sql = "select count(*) AS COUNT FROM blocks where status='nfs' and banner_id='$BID' ";
$result = mysqli_query($GLOBALS['connection'], $sql);
$row = mysqli_fetch_array($result);
$nfs = $row['COUNT'] * ($banner_data['BLK_WIDTH'] * $banner_data['BLK_HEIGHT']);

$available = (($banner_data['G_WIDTH'] * $banner_data['G_HEIGHT'] * ($banner_data['BLK_WIDTH'] * $banner_data['BLK_HEIGHT'])) - $nfs) - $sold;

if ($label['sold_stats'] == '') {
    $label['sold_stats'] = "Sold";
}

if ($label['available_stats'] == '') {
    $label['available_stats'] = "Available";
}

?>
<html>
<head>
    <title></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        html,body {
            background-color: transparent !important;
            position: relative;
            margin: 0;
            padding-bottom: 0;
            min-height: auto;
        }
</style>
</head>
<body class="status_body">
<div class="float-right">
    <ul class="nav">
        <li class="nav-item">
            <div class="nav-link"><?php echo $label['sold_stats']; ?>: <span class="badge badge-primary"><?php echo number_format($sold); ?></span></div>
        </li>
        <li class="nav-item">
            <div class="nav-link"><?php echo $label['available_stats']; ?>: <span class="badge badge-primary"><?php echo number_format($available); ?></span></div>
        </li>
    </ul>
</div>
</body>
</html>
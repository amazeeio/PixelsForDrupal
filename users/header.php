<?php
/**
 * @package		mds
 * @copyright	(C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author		Ryan Rhode, ryan@milliondollarscript.com
 * @license		This program is free software; you can redistribute it and/or modify
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

header('content-type: text/html; charset=utf-8');

echo $f2->get_doc(); ?>

<title><?php echo SITE_NAME; ?></title>
<meta name="Description" content="<?php echo SITE_SLOGAN; ?>">
<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="/assets/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>

<div id="main-top-header" class="main-top-header jumbotron mb-0">
    <div class="main-top-header-content container text-center">
        <h1><?php echo SITE_NAME; ?></h1>
    </div>
</div>

<?php
if (USE_AJAX=='SIMPLE') {
	$order_page = 'order_pixels.php';
} else {
	$order_page = 'select.php';
}
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav-fill w-100 align-items-start">
                <li class="nav-item">
                    <a class="nav-link" href='/'>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='/list.php'>List of Pixels</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='/faq.php'>FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='index.php'><?php echo $label['advertiser_header_nav1']; ?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='<?php echo $order_page; ?>'><?php echo $label['advertiser_header_nav2'];?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='publish.php'><?php echo $label['advertiser_header_nav3'];?></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='orders.php'><?php echo $label['advertiser_header_nav4'];?></a>
                </li>
                <?php if ($_SESSION['MDS_ID']!='') { ?>
                <li class="nav-item">
                    <a class="nav-link" href='logout.php'><?php echo $label['advertiser_header_nav5']; ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

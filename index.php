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

// set the root path
define("MDSROOT", dirname(__FILE__));

// check if a config.php exists, if not then rename the default one and redirect to install
if(!file_exists(MDSROOT . "/config.php")) {
	if(file_exists(MDSROOT . "/config-default.php")) {
		if(rename(MDSROOT . "/config-default.php", MDSROOT . "/config.php")){
			$host  = $_SERVER['HTTP_HOST'];
			$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			$protocol = ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) ? "https://" : "http://";
			$loc   = $protocol . $host . $uri . "/admin/install.php";
			header("Location: $loc");
			header("X-Frame-Options: allow-from " . $protocol . $host . $uri . "/");
		}
	}
	echo "The file config.php was not found and I was unable to automatically rename it. You may have to manually rename config-default.php to config.php and then visit $loc to install the script.";
	exit;
}

// include the config file
include_once (MDSROOT . "/config.php");

// include the header
include_once (MDSROOT . "/html/header.php");
?>
    <div class="d-lg-none alert alert-warning alert-dismissible fade show" role="alert">
        This site is best viewed on desktop.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
// Note: Below is the iframe which displays the image map. Use Process Pixels in the admin to update the image map.
echo get_html_code( $BID );
echo "<div class='container mt-4 mb-5 px-0' style='max-width: 1000px'>";
echo "
<div class='text-left'>
<p><strong>What is this?</strong></p>
<p>You might remember the <a href='http://www.milliondollarhomepage.com/'>Million Dollar Homepage</a> from way back when. You could buy pixels and use them to post whatever you wanted - and the guy who started it made a million dollars. We thought it would be fun to make a Half Million Dollar homepage to help the Drupal Association reach their goal. Donors can purchase pixels to support the DA. You’ll get 100 pixels for every $5 you donate. You can post images and links to your pixels.  </p>

<p><strong>What do I need to do?</strong></p>
<p>Donate to #DrupalCares at <a href='https://www.drupal.org/association/donate'>https://www.drupal.org/association/donate</a>.</p>

<p>Create an account on <a href='https://pixelsfordrupal.com/'>pixelsfordrupal.com</a>.</p>

<p>You’ll receive a verification email. Log in and verify your account. </p>

<p>You can now upload your pixels. You don’t need to do this all at once. You can upload some now, some later - you can use the whole amount at once or divide it up - it’s all up to you! You can also donate more to increase the amount of pixels you can upload. After you upload your pixels, you’ll be asked for the voucher code that was sent to you. </p>

<p><strong>What can I upload?</strong></p>
<p>It’s up to you! Uploads and links are subject to the <a href='https://www.drupal.org/dcoc'>Drupal Code of Conduct</a>, so keep it professional and kind. Upload a picture of yourself, your pet, your company logo - be creative! We will have a moderation team quickly reviewing each submission.</p>
</div>";
echo "</div>";

// include footer
include_once (MDSROOT . "/html/footer.php");

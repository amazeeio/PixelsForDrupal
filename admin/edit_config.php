<?php
/**
 * @version		$Id: edit_config.php 140 2011-04-19 05:08:19Z ryan $
 * @package		mds
 * @copyright	(C) Copyright 2010 Ryan Rhode, All rights reserved.
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
 * 		http://www.milliondollarscript.com/
 *
 */
error_reporting(0);
require ('admin_common.php');

// filter vars
$footer = $purifier->purify( $_REQUEST['footer'] );
$header = $purifier->purify( $_REQUEST['header'] );
foreach ($_REQUEST as $key=>$val) {
	$_REQUEST[$key] =  $val;
}

if ($_REQUEST['save'] != '') {
   if (get_magic_quotes_gpc()==0) { // magic is OFF?
	   // need to add slashes here..
	   $_REQUEST['site_name'] = addslashes($_REQUEST['site_name']);
	   $_REQUEST['site_heading'] = addslashes($_REQUEST['site_heading']);
	   $_REQUEST['site_description'] = addslashes($_REQUEST['site_description']);
	   $_REQUEST['site_keywords'] = addslashes($_REQUEST['site_keywords']);

   } else {
	   // Magic Quotes is on, need to get rid of slashes here
	   $header = stripslashes($header);
	   $footer = stripslashes($footer);

   }
echo "updating config....";
define('VERSION_INFO', addslashes($_REQUEST['version_info']));

define('BASE_HTTP_PATH', addslashes($_REQUEST['base_http_path']));
define('BASE_PATH', addslashes($_REQUEST['base_path']));
define('SERVER_PATH_TO_ADMIN', addslashes($_REQUEST['server_path_to_admin']));
define('UPLOAD_PATH', addslashes($_REQUEST['upload_path']));
define('UPLOAD_HTTP_PATH', addslashes($_REQUEST['upload_http_path']));
define('SITE_CONTACT_EMAIL', addslashes($_REQUEST['site_contact_email']));
define('SITE_LOGO_URL', addslashes($_REQUEST['site_logo_url']));
define('SITE_NAME', addslashes($_REQUEST['site_name']));
define('SITE_SLOGAN', addslashes($_REQUEST['site_slogan']));
define('MDS_RESIZE', addslashes($_REQUEST['mds_resize']));

define('MYSQL_HOST', addslashes($_REQUEST['mysql_host']));
define('MYSQL_USER', addslashes($_REQUEST['mysql_user']));
define('MYSQL_PASS', addslashes($_REQUEST['mysql_pass']));
define('MYSQL_DB', addslashes($_REQUEST['mysql_db']));
define('MYSQL_PORT', addslashes($_REQUEST['mysql_port']));
define('MYSQL_SOCKET', addslashes($_REQUEST['mysql_socket']));

define('ADMIN_PASSWORD', addslashes($_REQUEST['admin_password']));

define('DATE_FORMAT', addslashes($_REQUEST['date_format']));
define('GMT_DIF', addslashes($_REQUEST['gmt_dif']));
define('DATE_INPUT_SEQ', addslashes($_REQUEST['date_input_seq']));

define('OUTPUT_JPEG', addslashes($_REQUEST['output_jpeg']));
define('JPEG_QUALITY', addslashes($_REQUEST['jpeg_quality']));
define('INTERLACE_SWITCH', addslashes($_REQUEST['interlace_switch']));
define('USE_LOCK_TABLES', addslashes($_REQUEST['use_lock_tables']));
define('BANNER_DIR', addslashes($_REQUEST['banner_dir']));
define('DISPLAY_PIXEL_BACKGROUND', addslashes($_REQUEST['display_pixel_background']));

define('EMAIL_USER_ORDER_CONFIRMED', addslashes($_REQUEST['email_user_order_confirmed']));
define('EMAIL_ADMIN_ORDER_CONFIRMED', addslashes($_REQUEST['email_admin_order_confirmed']));
define('EMAIL_USER_ORDER_COMPLETED', addslashes($_REQUEST['email_user_order_completed']));
define('EMAIL_ADMIN_ORDER_COMPLETED', addslashes($_REQUEST['email_admin_order_completed']));
define('EMAIL_USER_ORDER_PENDED', addslashes($_REQUEST['email_user_order_pended']));
define('EMAIL_ADMIN_ORDER_PENDED', addslashes($_REQUEST['email_admin_order_pended']));
define('EMAIL_USER_ORDER_EXPIRED', addslashes($_REQUEST['email_user_order_expired']));
define('EMAIL_ADMIN_ORDER_EXPIRED', addslashes($_REQUEST['email_admin_order_expired']));
define('EM_NEEDS_ACTIVATION', addslashes($_REQUEST['em_needs_activation']));
define('EMAIL_USER_EXPIRE_WARNING', addslashes($_REQUEST['email_user_expire_warning']));
define('EMAIL_ADMIN_ACTIVATION', addslashes($_REQUEST['email_admin_activation']));
define('EMAIL_ADMIN_PUBLISH_NOTIFY', addslashes($_REQUEST['email_admin_publish_notify']));
define('EMAILS_DAYS_KEEP', addslashes($_REQUEST['emails_days_keep']));

define('DAYS_RENEW', addslashes($_REQUEST['days_renew']));
define('DAYS_CONFIRMED', addslashes($_REQUEST['days_confirmed']));
define('HOURS_UNCONFIRMED', addslashes($_REQUEST['hours_unconfirmed']));
define('DAYS_CANCEL', addslashes($_REQUEST['days_cancel']));
define('ENABLE_MOUSEOVER', addslashes($_REQUEST['enable_mouseover']));
define('ENABLE_CLOAKING', addslashes($_REQUEST['enable_cloaking']));
define('VALIDATE_LINK', addslashes($_REQUEST['validate_link']));
define('ADVANCED_CLICK_COUNT', addslashes($_REQUEST['advanced_click_count']));
define('USE_SMTP', addslashes($_REQUEST['use_smtp']));
define('EMAIL_SMTP_SERVER', addslashes($_REQUEST['email_smtp_server']));
define('EMAIL_POP_SERVER', addslashes($_REQUEST['email_pop_server']));
define('EMAIL_SMTP_USER', addslashes($_REQUEST['email_smtp_user']));
define('EMAIL_SMTP_PASS', addslashes($_REQUEST['email_smtp_pass']));
define('EMAIL_SMTP_AUTH_HOST', addslashes($_REQUEST['email_smtp_auth_host']));
define('SMTP_PORT', addslashes($_REQUEST['smtp_port']));
define('POP3_PORT', addslashes($_REQUEST['pop3_port']));
define('EMAIL_TLS', addslashes($_REQUEST['email_tls']));
define('EMAIL_POP_BEFORE_SMTP', addslashes($_REQUEST['email_pop_before_smtp']));
define('EMAIL_DEBUG', addslashes($_REQUEST['email_debug']));

define('EMAILS_PER_BATCH', addslashes($_REQUEST['emails_per_batch']));
define('EMAILS_MAX_RETRY', addslashes($_REQUEST['emails_max_retry']));
define('EMAILS_ERROR_WAIT', addslashes($_REQUEST['emails_error_wait']));

define('USE_AJAX', addslashes($_REQUEST['use_ajax']));
define('ANIMATION_SPEED', addslashes($_REQUEST['animation_speed']));
define('MAX_BLOCKS', addslashes($_REQUEST['max_blocks']));

define('MEMORY_LIMIT', addslashes($_REQUEST['memory_limit']));

define('REDIRECT_SWITCH', addslashes($_REQUEST['redirect_switch']));
define('REDIRECT_URL', addslashes($_REQUEST['redirect_url']));

define('HIDE_TIMEOUT', addslashes($_REQUEST['hide_timeout']));
define('MDS_AGRESSIVE_CACHE', addslashes($_REQUEST['mds_agressive_cache']));

define('ERROR_REPORTING', addslashes($_REQUEST['error_reporting']));

$config_str = "<?php
error_reporting(".ERROR_REPORTING.");

#########################################################################
# CONFIGURATION
# Note: Please do not edit this file. Edit the config from the admin section.
#########################################################################

define('DEBUG', false);
define('MDS_LOG', false);
define('MDS_LOG_FILE', dirname(__FILE__).'/.mds.log');

define('VERSION_INFO', 'v 2.1 (Oct 2010)');

define('BASE_HTTP_PATH', '".BASE_HTTP_PATH."'); 
define('BASE_PATH', '".BASE_PATH."');
define('SERVER_PATH_TO_ADMIN', '".SERVER_PATH_TO_ADMIN."');
define('UPLOAD_PATH', '".UPLOAD_PATH."');
define('UPLOAD_HTTP_PATH', '".UPLOAD_HTTP_PATH."');
define('MYSQL_HOST', '".MYSQL_HOST."'); # mysql database host
define('MYSQL_USER', '".MYSQL_USER."'); #mysql user name
define('MYSQL_PASS', '".MYSQL_PASS."'); # mysql password
define('MYSQL_DB', '".MYSQL_DB."'); # mysql database name
define('MYSQL_PORT', ".MYSQL_PORT."); # mysql port
define('MYSQL_SOCKET', '".MYSQL_SOCKET."'); # mysql socket

# ADMIN_PASSWORD
define('ADMIN_PASSWORD',  '".ADMIN_PASSWORD."');

define('MDS_RESIZE', '".MDS_RESIZE."');

# SITE_CONTACT_EMAIL
define('SITE_CONTACT_EMAIL', stripslashes('".SITE_CONTACT_EMAIL."'));

# SITE_LOGO_URL
define('SITE_LOGO_URL', stripslashes('".SITE_LOGO_URL."'));

# SITE_NAME
# change to your website name
define('SITE_NAME', stripslashes('".SITE_NAME."')); 

# SITE_SLOGAN
# change to your website slogan
define('SITE_SLOGAN', stripslashes('".SITE_SLOGAN."')); 

# date formats
define('DATE_FORMAT', '".DATE_FORMAT."');
define('GMT_DIF', '".GMT_DIF."');
define('DATE_INPUT_SEQ', '".DATE_INPUT_SEQ."');

# Output the image in JPEG? Y or N. 
define ('OUTPUT_JPEG', '".OUTPUT_JPEG."'); # Y or N
define ('JPEG_QUALITY', '".JPEG_QUALITY."'); # a number from 0 to 100
define('INTERLACE_SWITCH','".INTERLACE_SWITCH."');

# Note: Please do not edit this file. Edit from the admin section.

# USE_LOCK_TABLES
# The script can lock/unlock tables when a user is selecting pixels
define ('USE_LOCK_TABLES', '".USE_LOCK_TABLES."');

define('BANNER_DIR', '".BANNER_DIR."');

# IM_CONVERT_PATH
define('IM_CONVERT_PATH', '".IM_CONVERT_PATH."');

# Note: Please do not edit this file. Edit from the admin section.

define('EMAIL_USER_ORDER_CONFIRMED', '".EMAIL_USER_ORDER_CONFIRMED."');
define('EMAIL_ADMIN_ORDER_CONFIRMED', '".EMAIL_ADMIN_ORDER_CONFIRMED."');
define('EMAIL_USER_ORDER_COMPLETED', '".EMAIL_USER_ORDER_COMPLETED."');
define('EMAIL_ADMIN_ORDER_COMPLETED', '".EMAIL_ADMIN_ORDER_COMPLETED."');
define('EMAIL_USER_ORDER_PENDED', '".EMAIL_USER_ORDER_PENDED."');
define('EMAIL_ADMIN_ORDER_PENDED', '".EMAIL_ADMIN_ORDER_PENDED."');
define('EMAIL_USER_ORDER_EXPIRED', '".EMAIL_USER_ORDER_EXPIRED."');
define('EMAIL_ADMIN_ORDER_EXPIRED', '".EMAIL_ADMIN_ORDER_EXPIRED."');

define('EM_NEEDS_ACTIVATION', '".EM_NEEDS_ACTIVATION."');
define('EMAIL_ADMIN_ACTIVATION', '".EMAIL_ADMIN_ACTIVATION."');
define('EMAIL_ADMIN_PUBLISH_NOTIFY', '".EMAIL_ADMIN_PUBLISH_NOTIFY."');
define('USE_PAYPAL_SUBSCR', '".USE_PAYPAL_SUBSCR."');
define('EMAIL_USER_EXPIRE_WARNING', '".EMAIL_USER_EXPIRE_WARNING."');
define('DAYS_RENEW', '".DAYS_RENEW."');
define('DAYS_CONFIRMED', '".DAYS_CONFIRMED."');
define('HOURS_UNCONFIRMED', '".HOURS_UNCONFIRMED."');
define('DAYS_CANCEL', '".DAYS_CANCEL."');
define('ENABLE_MOUSEOVER', '".ENABLE_MOUSEOVER."');
define('ENABLE_CLOAKING', '".ENABLE_CLOAKING."');
define('VALIDATE_LINK', '".VALIDATE_LINK."');
define('DISPLAY_PIXEL_BACKGROUND', '".DISPLAY_PIXEL_BACKGROUND."');
define('USE_SMTP', '".USE_SMTP."');
define('EMAIL_SMTP_SERVER', '".EMAIL_SMTP_SERVER."');
define('EMAIL_SMTP_USER', '".EMAIL_SMTP_USER."');
define('EMAIL_SMTP_PASS', '".EMAIL_SMTP_PASS."');
define('EMAIL_SMTP_AUTH_HOST', '".EMAIL_SMTP_AUTH_HOST."');
define('SMTP_PORT', '".SMTP_PORT."');
define('POP3_PORT', '".POP3_PORT."');
define('EMAIL_TLS', '".EMAIL_TLS."');
define('EMAIL_POP_SERVER', '".EMAIL_POP_SERVER."');
define('EMAIL_POP_BEFORE_SMTP', '".EMAIL_POP_BEFORE_SMTP."');
define('EMAIL_DEBUG', '".EMAIL_DEBUG."');

define('EMAILS_PER_BATCH', '".EMAILS_PER_BATCH."');
define('EMAILS_MAX_RETRY', '".EMAILS_MAX_RETRY."');
define('EMAILS_ERROR_WAIT', '".EMAILS_ERROR_WAIT."');
define('EMAILS_DAYS_KEEP', '".EMAILS_DAYS_KEEP."');
define('USE_AJAX', '".USE_AJAX."');
define('ANIMATION_SPEED', '".ANIMATION_SPEED."');
define('MAX_BLOCKS', '".MAX_BLOCKS."');
define('MEMORY_LIMIT', '".MEMORY_LIMIT."');

define('REDIRECT_SWITCH', '".REDIRECT_SWITCH."');
define('REDIRECT_URL', '".REDIRECT_URL."');
define('ADVANCED_CLICK_COUNT', '".ADVANCED_CLICK_COUNT."');

define('HIDE_TIMEOUT', '".HIDE_TIMEOUT."');
define('MDS_AGRESSIVE_CACHE', '".MDS_AGRESSIVE_CACHE."');

if (defined('MEMORY_LIMIT')) {
	ini_set('memory_limit', MEMORY_LIMIT);
} else {
	ini_set('memory_limit', '64M');
}

define('ERROR_REPORTING', ".ERROR_REPORTING.");

	// database connection
	require_once(dirname(__FILE__).'/include/database.php');

	// load HTMLPurifier
    require_once dirname(__FILE__).'/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
    \$purifier = new HTMLPurifier(); 
	
	require_once dirname(__FILE__).'/include/functions2.php';
	\$f2 = new functions2();

	include dirname(__FILE__).'/lang/lang.php';
	require_once dirname(__FILE__).'/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
	require_once dirname(__FILE__).'/include/mail_manager.php';
	require_once dirname(__FILE__).'/include/currency_functions.php';
	require_once dirname(__FILE__).'/include/price_functions.php';
	require_once dirname(__FILE__).'/include/functions.php';
	require_once dirname(__FILE__).'/include/image_functions.php';
	if (!get_magic_quotes_gpc()) unfck_gpc();
	//escape_gpc();

function get_banner_dir() {
	if ( BANNER_DIR == 'BANNER_DIR' ) {

		\$base = BASE_PATH;
		if ( \$base == 'BASE_PATH' ) {
			\$base = __DIR__;
		}
		\$dest = \$base . '/banners/';

		if ( file_exists( \$dest ) ) {
			\$BANNER_DIR = 'banners/';
		} else {
			\$BANNER_DIR = 'pixels/';
		}
	} else {
		\$BANNER_DIR = BANNER_DIR;
	}

	return \$BANNER_DIR;

}

?>";

  // echo "<pre>[$config_str]</pre>";

   /// write out the config..

    $file =fopen ("../config.php", "w");
    fwrite($file, $config_str);

} else {
// load in the headers and footers..

}
require "../config.php";

echo $f2->get_doc(); ?>

<style>
body {
	font-family: 'Arial', sans-serif; 
	font-size:10pt;

}
</style>
</head>
<body>

<h3>
Main Configuration</h3>
Options on this page affect the running of the pixel advertising system.<p>
Note: <i>Make sure that config.php has write permissions <b>turned on</b> when editing this form. You should turn off write permission after editing this form.</i><br>
<p>
<b>Tip:</b> Looking for where to settings for the grid? It is set in 'Pixel Inventory' -> <a href="inventory.php">Manage Grids</a>. Click on Edit to edit the grid parameters.
</p>

<?php
echo "<p>";
if (is_writable("../config.php")) {
	echo "- config.php is writeable.<br>";
} else {
	echo "- <font color='red'> Note: config.php is not writable. Give write permissions to config.php if you want to save the changes</font><br>";
}

require ('config_form.php');

?>


<p>&nbsp;</p>
</body>
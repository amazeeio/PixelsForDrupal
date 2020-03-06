<?php
/**
 * @package        mds
 * @copyright      (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author         Ryan Rhode, ryan@milliondollarscript.com
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

error_reporting( 0 );
require( 'admin_common.php' );

// filter vars
$footer = $purifier->purify( $_REQUEST['footer'] );
$header = $purifier->purify( $_REQUEST['header'] );
foreach ( $_REQUEST as $key => $val ) {
	$_REQUEST[ $key ] = $val;
}

if ( $_REQUEST['save'] != '' ) {
	echo "updating config....";
	define( 'VERSION_INFO', $_REQUEST['version_info'] );
	define( 'BASE_HTTP_PATH', $_REQUEST['base_http_path'] );
	define( 'BASE_PATH', str_replace( '\\', '/', $_REQUEST['base_path'] ) );
	define( 'SERVER_PATH_TO_ADMIN', str_replace( '\\', '/', $_REQUEST['server_path_to_admin'] ) );
	define( 'UPLOAD_PATH', str_replace( '\\', '/', $_REQUEST['upload_path'] ) );
	define( 'UPLOAD_HTTP_PATH', $_REQUEST['upload_http_path'] );
	define( 'SITE_CONTACT_EMAIL', $_REQUEST['site_contact_email'] );
	define( 'SITE_LOGO_URL', $_REQUEST['site_logo_url'] );
	define( 'SITE_NAME', $_REQUEST['site_name'] );
	define( 'SITE_SLOGAN', $_REQUEST['site_slogan'] );
	define( 'MDS_RESIZE', $_REQUEST['mds_resize'] );
	define( 'MYSQL_HOST', $_REQUEST['mysql_host'] );
	define( 'MYSQL_USER', $_REQUEST['mysql_user'] );
	define( 'MYSQL_PASS', $_REQUEST['mysql_pass'] );
	define( 'MYSQL_DB', $_REQUEST['mysql_db'] );
	define( 'MYSQL_PORT', $_REQUEST['mysql_port'] );
	define( 'MYSQL_SOCKET', $_REQUEST['mysql_socket'] );
	define( 'ADMIN_PASSWORD', $_REQUEST['admin_password'] );
	define( 'DATE_FORMAT', $_REQUEST['date_format'] );
	define( 'GMT_DIF', $_REQUEST['gmt_dif'] );
	define( 'DATE_INPUT_SEQ', $_REQUEST['date_input_seq'] );
	define( 'OUTPUT_JPEG', $_REQUEST['output_jpeg'] );
	define( 'JPEG_QUALITY', $_REQUEST['jpeg_quality'] );
	define( 'INTERLACE_SWITCH', $_REQUEST['interlace_switch'] );
	define( 'USE_LOCK_TABLES', $_REQUEST['use_lock_tables'] );
	define( 'BANNER_DIR', $_REQUEST['banner_dir'] );
	define( 'DISPLAY_PIXEL_BACKGROUND', $_REQUEST['display_pixel_background'] );
	define( 'EMAIL_USER_ORDER_CONFIRMED', $_REQUEST['email_user_order_confirmed'] );
	define( 'EMAIL_ADMIN_ORDER_CONFIRMED', $_REQUEST['email_admin_order_confirmed'] );
	define( 'EMAIL_USER_ORDER_COMPLETED', $_REQUEST['email_user_order_completed'] );
	define( 'EMAIL_ADMIN_ORDER_COMPLETED', $_REQUEST['email_admin_order_completed'] );
	define( 'EMAIL_USER_ORDER_PENDED', $_REQUEST['email_user_order_pended'] );
	define( 'EMAIL_ADMIN_ORDER_PENDED', $_REQUEST['email_admin_order_pended'] );
	define( 'EMAIL_USER_ORDER_EXPIRED', $_REQUEST['email_user_order_expired'] );
	define( 'EMAIL_ADMIN_ORDER_EXPIRED', $_REQUEST['email_admin_order_expired'] );
	define( 'EM_NEEDS_ACTIVATION', $_REQUEST['em_needs_activation'] );
	define( 'EMAIL_ADMIN_ACTIVATION', $_REQUEST['email_admin_activation'] );
	define( 'EMAIL_ADMIN_PUBLISH_NOTIFY', $_REQUEST['email_admin_publish_notify'] );
	define( 'USE_PAYPAL_SUBSCR', $_REQUEST['use_paypal_subscr'] );
	define( 'EMAIL_USER_EXPIRE_WARNING', $_REQUEST['email_user_expire_warning'] );
	define( 'EMAILS_DAYS_KEEP', $_REQUEST['emails_days_keep'] );
	define( 'DAYS_RENEW', $_REQUEST['days_renew'] );
	define( 'DAYS_CONFIRMED', $_REQUEST['days_confirmed'] );
	define( 'HOURS_UNCONFIRMED', $_REQUEST['hours_unconfirmed'] );
	define( 'DAYS_CANCEL', $_REQUEST['days_cancel'] );
	define( 'ENABLE_MOUSEOVER', $_REQUEST['enable_mouseover'] );
	define( 'ENABLE_CLOAKING', $_REQUEST['enable_cloaking'] );
	define( 'VALIDATE_LINK', $_REQUEST['validate_link'] );
	define( 'ADVANCED_CLICK_COUNT', $_REQUEST['advanced_click_count'] );
	define( 'USE_SMTP', $_REQUEST['use_smtp'] );
	define( 'EMAIL_SMTP_SERVER', $_REQUEST['email_smtp_server'] );
	define( 'EMAIL_SMTP_USER', $_REQUEST['email_smtp_user'] );
	define( 'EMAIL_SMTP_PASS', $_REQUEST['email_smtp_pass'] );
	define( 'EMAIL_SMTP_AUTH_HOST', $_REQUEST['email_smtp_auth_host'] );
	define( 'SMTP_PORT', $_REQUEST['smtp_port'] );
	define( 'POP3_PORT', $_REQUEST['pop3_port'] );
	define( 'EMAIL_TLS', $_REQUEST['email_tls'] );
	define( 'EMAIL_POP_SERVER', $_REQUEST['email_pop_server'] );
	define( 'EMAIL_POP_BEFORE_SMTP', $_REQUEST['email_pop_before_smtp'] );
	define( 'EMAIL_DEBUG', $_REQUEST['email_debug'] );
	define( 'EMAILS_PER_BATCH', $_REQUEST['emails_per_batch'] );
	define( 'EMAILS_MAX_RETRY', $_REQUEST['emails_max_retry'] );
	define( 'EMAILS_ERROR_WAIT', $_REQUEST['emails_error_wait'] );
	define( 'USE_AJAX', $_REQUEST['use_ajax'] );
	define( 'ANIMATION_SPEED', $_REQUEST['animation_speed'] );
	define( 'MAX_BLOCKS', $_REQUEST['max_blocks'] );
	define( 'MEMORY_LIMIT', $_REQUEST['memory_limit'] );
	define( 'REDIRECT_SWITCH', $_REQUEST['redirect_switch'] );
	define( 'REDIRECT_URL', $_REQUEST['redirect_url'] );
	define( 'HIDE_TIMEOUT', $_REQUEST['hide_timeout'] );
	define( 'MDS_AGRESSIVE_CACHE', $_REQUEST['mds_agressive_cache'] );
	define( 'ERROR_REPORTING', $_REQUEST['error_reporting'] );

	$config_str = "<?php

#########################################################################
# CONFIGURATION
# Note: Please do not edit this file. Edit the config from the admin section.
#########################################################################

error_reporting( " . ERROR_REPORTING . " );
define( 'DEBUG', false );
define( 'MDS_LOG', false );
define( 'MDS_LOG_FILE', dirname( __FILE__ ) . '/.mds.log' );
define( 'VERSION_INFO', 'v 2.1 (Oct 2010)' );
define( 'BASE_HTTP_PATH', '" . BASE_HTTP_PATH . "' );
define( 'BASE_PATH', '" . BASE_PATH . "' );
define( 'SERVER_PATH_TO_ADMIN', '" . SERVER_PATH_TO_ADMIN . "' );
define( 'UPLOAD_PATH', '" . UPLOAD_PATH . "' );
define( 'UPLOAD_HTTP_PATH', '" . UPLOAD_HTTP_PATH . "' );
define( 'SITE_CONTACT_EMAIL', '" . SITE_CONTACT_EMAIL . "' );
define( 'SITE_LOGO_URL', '" . SITE_LOGO_URL . "' );
define( 'SITE_NAME', '" . SITE_NAME . "' );
define( 'SITE_SLOGAN', '" . SITE_SLOGAN . "' );
define( 'MDS_RESIZE', '" . MDS_RESIZE . "' );
define( 'MYSQL_HOST', '" . MYSQL_HOST . "' );
define( 'MYSQL_USER', '" . MYSQL_USER . "' );
define( 'MYSQL_PASS', '" . MYSQL_PASS . "' );
define( 'MYSQL_DB', '" . MYSQL_DB . "' );
define( 'MYSQL_PORT', " . MYSQL_PORT . " );
define( 'MYSQL_SOCKET', '" . MYSQL_SOCKET . "' );
define( 'ADMIN_PASSWORD', '" . ADMIN_PASSWORD . "' );
define( 'DATE_FORMAT', '" . DATE_FORMAT . "' );
define( 'GMT_DIF', '" . GMT_DIF . "' );
define( 'DATE_INPUT_SEQ', '" . DATE_INPUT_SEQ . "' );
define( 'OUTPUT_JPEG', '" . OUTPUT_JPEG . "' );
define( 'JPEG_QUALITY', '" . JPEG_QUALITY . "' );
define( 'INTERLACE_SWITCH', '" . INTERLACE_SWITCH . "' );
define( 'USE_LOCK_TABLES', '" . USE_LOCK_TABLES . "' );
define( 'BANNER_DIR', '" . BANNER_DIR . "' );
define( 'DISPLAY_PIXEL_BACKGROUND', '" . DISPLAY_PIXEL_BACKGROUND . "' );
define( 'EMAIL_USER_ORDER_CONFIRMED', '" . EMAIL_USER_ORDER_CONFIRMED . "' );
define( 'EMAIL_ADMIN_ORDER_CONFIRMED', '" . EMAIL_ADMIN_ORDER_CONFIRMED . "' );
define( 'EMAIL_USER_ORDER_COMPLETED', '" . EMAIL_USER_ORDER_COMPLETED . "' );
define( 'EMAIL_ADMIN_ORDER_COMPLETED', '" . EMAIL_ADMIN_ORDER_COMPLETED . "' );
define( 'EMAIL_USER_ORDER_PENDED', '" . EMAIL_USER_ORDER_PENDED . "' );
define( 'EMAIL_ADMIN_ORDER_PENDED', '" . EMAIL_ADMIN_ORDER_PENDED . "' );
define( 'EMAIL_USER_ORDER_EXPIRED', '" . EMAIL_USER_ORDER_EXPIRED . "' );
define( 'EMAIL_ADMIN_ORDER_EXPIRED', '" . EMAIL_ADMIN_ORDER_EXPIRED . "' );
define( 'EM_NEEDS_ACTIVATION', '" . EM_NEEDS_ACTIVATION . "' );
define( 'EMAIL_ADMIN_ACTIVATION', '" . EMAIL_ADMIN_ACTIVATION . "' );
define( 'EMAIL_ADMIN_PUBLISH_NOTIFY', '" . EMAIL_ADMIN_PUBLISH_NOTIFY . "' );
define( 'USE_PAYPAL_SUBSCR', '" . USE_PAYPAL_SUBSCR . "' );
define( 'EMAIL_USER_EXPIRE_WARNING', '" . EMAIL_USER_EXPIRE_WARNING . "' );
define( 'EMAILS_DAYS_KEEP', '" . EMAILS_DAYS_KEEP . "' );
define( 'DAYS_RENEW', '" . DAYS_RENEW . "' );
define( 'DAYS_CONFIRMED', '" . DAYS_CONFIRMED . "' );
define( 'HOURS_UNCONFIRMED', '" . HOURS_UNCONFIRMED . "' );
define( 'DAYS_CANCEL', '" . DAYS_CANCEL . "' );
define( 'ENABLE_MOUSEOVER', '" . ENABLE_MOUSEOVER . "' );
define( 'ENABLE_CLOAKING', '" . ENABLE_CLOAKING . "' );
define( 'VALIDATE_LINK', '" . VALIDATE_LINK . "' );
define( 'ADVANCED_CLICK_COUNT', '" . ADVANCED_CLICK_COUNT . "' );
define( 'USE_SMTP', '" . USE_SMTP . "' );
define( 'EMAIL_SMTP_SERVER', '" . EMAIL_SMTP_SERVER . "' );
define( 'EMAIL_SMTP_USER', '" . EMAIL_SMTP_USER . "' );
define( 'EMAIL_SMTP_PASS', '" . EMAIL_SMTP_PASS . "' );
define( 'EMAIL_SMTP_AUTH_HOST', '" . EMAIL_SMTP_AUTH_HOST . "' );
define( 'SMTP_PORT', '" . SMTP_PORT . "' );
define( 'POP3_PORT', '" . POP3_PORT . "' );
define( 'EMAIL_TLS', '" . EMAIL_TLS . "' );
define( 'EMAIL_POP_SERVER', '" . EMAIL_POP_SERVER . "' );
define( 'EMAIL_POP_BEFORE_SMTP', '" . EMAIL_POP_BEFORE_SMTP . "' );
define( 'EMAIL_DEBUG', '" . EMAIL_DEBUG . "' );
define( 'EMAILS_PER_BATCH', '" . EMAILS_PER_BATCH . "' );
define( 'EMAILS_MAX_RETRY', '" . EMAILS_MAX_RETRY . "' );
define( 'EMAILS_ERROR_WAIT', '" . EMAILS_ERROR_WAIT . "' );
define( 'USE_AJAX', '" . USE_AJAX . "' );
define( 'ANIMATION_SPEED', '" . ANIMATION_SPEED . "' );
define( 'MAX_BLOCKS', '" . MAX_BLOCKS . "' );
define( 'MEMORY_LIMIT', '" . MEMORY_LIMIT . "' );
define( 'REDIRECT_SWITCH', '" . REDIRECT_SWITCH . "' );
define( 'REDIRECT_URL', '" . REDIRECT_URL . "' );
define( 'HIDE_TIMEOUT', '" . HIDE_TIMEOUT . "' );
define( 'MDS_AGRESSIVE_CACHE', '" . MDS_AGRESSIVE_CACHE . "' );
define( 'ERROR_REPORTING', " . ERROR_REPORTING . " );

if ( defined( 'MEMORY_LIMIT' ) ) {
	ini_set( 'memory_limit', MEMORY_LIMIT );
} else {
	ini_set( 'memory_limit', '128M' );
}

require_once( dirname( __FILE__ ) . '/include/database.php' );
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

\$purifier = new HTMLPurifier();

require_once dirname( __FILE__ ) . '/include/functions2.php';
\$f2 = new functions2();

include dirname( __FILE__ ) . '/lang/lang.php';
require_once dirname( __FILE__ ) . '/include/mail_manager.php';
require_once dirname( __FILE__ ) . '/include/currency_functions.php';
require_once dirname( __FILE__ ) . '/include/price_functions.php';
require_once dirname( __FILE__ ) . '/include/functions.php';
require_once dirname( __FILE__ ) . '/include/image_functions.php';
if ( ! get_magic_quotes_gpc() ) {
	unfck_gpc();
}
";
	/// write out the config..

	$file = fopen( "../config.php", "w" );
	fwrite( $file, $config_str );

}

require "../config.php";

echo $f2->get_doc(); ?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        font-size: 10pt;

    }
</style>
</head>
<body>

<h3>Main Configuration</h3>
<p>Options on this page affect the running of the pixel advertising system.</p>
<p>Note: <i>Make sure that config.php has write permissions <b>turned on</b> when editing this form. You should turn off write permission after editing this form.</i></p>
<p><b>Tip:</b> Looking for where to settings for the grid? It is set in 'Pixel Inventory' -> <a href="inventory.php">Manage Grids</a>. Click on Edit to edit the grid parameters.</p>
<p>
	<?php
	if ( is_writable( "../config.php" ) ) {
		echo "- config.php is writeable.";
	} else {
		echo "- <font color='red'> Note: config.php is not writable. Give write permissions to config.php if you want to save the changes</font>";
	}

	require( 'config_form.php' );
	?>
</p>
</body>
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

#########################################################################
# CONFIGURATION
# Note: Please do not edit this file. Edit the config from the admin section.
#########################################################################

$route = getenv('LAGOON_ROUTE') ?: 'http://nginx-pixelsfordrupal.docker.amazee.io';

error_reporting( 0 );
define( 'DEBUG', false );
define( 'MDS_LOG', false );
define( 'MDS_LOG_FILE', dirname( __FILE__ ) . '/.mds.log' );
define( 'VERSION_INFO', 'Version 2.2 super alpha' );
define( 'BASE_HTTP_PATH', $route . '/' );
define( 'BASE_PATH', __DIR__ );
define( 'SERVER_PATH_TO_ADMIN', __DIR__ . '/admin/' );
define( 'UPLOAD_PATH', __DIR__ . '/files/upload_files/' );
define( 'UPLOAD_HTTP_PATH', $route . '/files/upload_files/' );
define( 'SITE_CONTACT_EMAIL', 'pixelsfordrupal@amazee.io' );
define( 'SITE_LOGO_URL', $route . '/logo.gif' );
define( 'SITE_NAME', 'Pixels for Drupal');
define( 'SITE_SLOGAN', 'The <strong>#DrupalCares</strong> campaign is a fundraiser to protect the Drupal Association from the financial impact of COVID-19.<br>Each donation for <strong>#DrupalCares</strong> gives you 20x that amount in pixels! ' );
define( 'MDS_RESIZE', 'YES' );
define( 'MYSQL_HOST', getenv('MARIADB_HOST') ?: 'mariadb' );
define( 'MYSQL_USER', getenv('MARIADB_USERNAME') ?: 'lagoon' );
define( 'MYSQL_PASS', getenv('MARIADB_PASSWORD') ?: 'lagoon' );
define( 'MYSQL_DB', getenv('MARIADB_DATABASE') ?: 'lagoon' );
define( 'MYSQL_PORT', 3306 );
define( 'MYSQL_SOCKET', '' );
define( 'ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'serupas8f23or70abf98asfv979hr87jasf' );
define( 'PASSWORD_SALT', getenv('PASSWORD_SALT') ?: 'gGCfmeO6kdMxS1Z1n2mKTIzZKD0oDThXyNfMJL4iYnj0PSIASRp1ZtVNwROB8gMtG0n2vJyq68bRw1jah8ngr7bMiNSOZBR1HND5' );
define( 'DATE_FORMAT', 'Y-M-d' );
define( 'GMT_DIF', '6' );
define( 'DATE_INPUT_SEQ', 'YMD' );
define( 'OUTPUT_JPEG', 'N' );
define( 'JPEG_QUALITY', '75' );
define( 'INTERLACE_SWITCH', 'YES' );
define( 'USE_LOCK_TABLES', 'Y' );
define( 'BANNER_DIR', 'files/pixels/' );
define( 'DISPLAY_PIXEL_BACKGROUND', 'NO' );
define( 'EMAIL_USER_ORDER_CONFIRMED', 'NO' );
define( 'EMAIL_ADMIN_ORDER_CONFIRMED', 'YES' );
define( 'EMAIL_USER_ORDER_COMPLETED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_COMPLETED', 'YES' );
define( 'EMAIL_USER_ORDER_PENDED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_PENDED', 'YES' );
define( 'EMAIL_USER_ORDER_EXPIRED', 'YES' );
define( 'EMAIL_ADMIN_ORDER_EXPIRED', 'YES' );
define( 'EM_NEEDS_ACTIVATION', 'YES' );
define( 'EMAIL_ADMIN_ACTIVATION', 'YES' );
define( 'EMAIL_ADMIN_PUBLISH_NOTIFY', 'YES' );
define( 'USE_PAYPAL_SUBSCR', 'NO' );
define( 'EMAIL_USER_EXPIRE_WARNING', '' );
define( 'EMAILS_DAYS_KEEP', '30' );
define( 'DAYS_RENEW', '7' );
define( 'DAYS_CONFIRMED', '7' );
define( 'HOURS_UNCONFIRMED', '1' );
define( 'DAYS_CANCEL', '3' );
define( 'ENABLE_MOUSEOVER', 'POPUP' );
define( 'ENABLE_CLOAKING', 'YES' );
define( 'VALIDATE_LINK', 'NO' );
define( 'ADVANCED_CLICK_COUNT', 'YES' );
define( 'USE_SMTP', '' );
define( 'EMAIL_SMTP_SERVER', '' );
define( 'EMAIL_SMTP_USER', '' );
define( 'EMAIL_SMTP_PASS', '' );
define( 'EMAIL_SMTP_AUTH_HOST', '' );
define( 'SMTP_PORT', '465' );
define( 'POP3_PORT', '995' );
define( 'EMAIL_TLS', '1' );
define( 'EMAIL_POP_SERVER', '' );
define( 'EMAIL_POP_BEFORE_SMTP', 'NO' );
define( 'EMAIL_DEBUG', 'NO' );
define( 'EMAILS_PER_BATCH', '12' );
define( 'EMAILS_MAX_RETRY', '15' );
define( 'EMAILS_ERROR_WAIT', '20' );
define( 'USE_AJAX', 'SIMPLE' );
define( 'ANIMATION_SPEED', '50' );
define( 'MAX_BLOCKS', '' );
define( 'MEMORY_LIMIT', '128M' );
define( 'REDIRECT_SWITCH', 'NO' );
define( 'REDIRECT_URL', $route );
define( 'HIDE_TIMEOUT', '500' );
define( 'MDS_AGRESSIVE_CACHE', 'NO' );
define( 'ERROR_REPORTING', 0 );
define( 'VOUCHER_IMPORT_HASH_SALT', getenv('VOUCHER_IMPORT_HASH_SALT') ?: 'or((|*D)9qa-weSwEP)>s"l-%5.^;{l');
define( 'TEMP_PATH', __DIR__ . '/files/temp/' );

if ( defined( 'MEMORY_LIMIT' ) ) {
	ini_set( 'memory_limit', MEMORY_LIMIT );
} else {
	ini_set( 'memory_limit', '128M' );
}

require_once( dirname( __FILE__ ) . '/include/database.php' );
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

$purifier = new HTMLPurifier();

require_once dirname( __FILE__ ) . '/include/functions2.php';
$f2 = new functions2();

include dirname( __FILE__ ) . '/lang/lang.php';
require_once dirname( __FILE__ ) . '/include/mail_manager.php';
require_once dirname( __FILE__ ) . '/include/currency_functions.php';
require_once dirname( __FILE__ ) . '/include/price_functions.php';
require_once dirname( __FILE__ ) . '/include/functions.php';
require_once dirname( __FILE__ ) . '/include/image_functions.php';
if ( ! get_magic_quotes_gpc() ) {
	unfck_gpc();
}

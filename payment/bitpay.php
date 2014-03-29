<?php
/**
 * @version		bitpay.php
 * @package		mds
 * @copyright	(C) Copyright 2010-2014 Ryan Rhode, All rights reserved.
 * @author		Ryan Rhode, ryan@milliondollarscript.com
 * @license		This program is free software; you can redistribute it and/or modify
 * 		it under the terms of the GNU General Public License as published by
 * 		the Free Software Foundation; either version 3 of the License, or
 * 		(at your option) any later version.
 *
 * 		This program is distributed in the hope that it will be useful,
 * 		but WITHOUT ANY WARRANTY; without even the implied warranty of
 * 		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * 		GNU General Public License for more details.
 *
 * 		You should have received a copy of the GNU General Public License along
 * 		with this program;  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * 		Million Dollar Script
 * 		A pixel script for selling pixels on your website.
 *
 * 		For instructions see README.txt
 *
 * 		Visit our website for FAQs, documentation, a list team members,
 * 		to post any bugs or feature requests, and a community forum:
 * 		http://www.milliondollarscript.com/
 *
 */
require_once "../config.php";

// https://github.com/bitpay/php-client

$_PAYMENT_OBJECTS['BitPay'] = new BitPay;

define(IPN_LOGGING, 'Y');

function bitpay_mail_error($msg) {
	$date = date("D, j M Y H:i:s O");

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	$entry_line = "(BitPay error detected) $msg\r\n ";
	$log_fp = @fopen("logs.txt", "a");
	fputs($log_fp, $entry_line);
	fclose($log_fp);

	mail(SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " BitPay script. ", $msg, $headers);
}

#####################################################################################

if ($_POST['posData'] != '') {
	require_once "bitpay/bp_lib.php";

	$verify = bpVerifyNotification(BITPAY_APIKEY);

	// catch error
	if (isset($verify['error'])) {
		exit;
	}

	// decode json result
	$invoice = json_decode($result);

	$sql = "select * FROM orders where order_id='" . $invoice['orderId'] . "'";
	$result = mysql_query($sql) or bp_mail_error(mysql_error() . $sql);
	$row = mysql_fetch_array($result);

	complete_order($row['user_id'], $invoice['orderId']);
	debit_transaction($invoice['orderId'], $invoice['amount'], $invoice['currency'], $invoice['invoiceId'], $invoice['code'], 'BitPay');
}

###########################################################################
# Payment Object

class BitPay {

	var $name;
	var $description;
	var $className = "BitPay";

	function BitPay() {
		global $label;

		$this->name = $label['payment_bitpay_name'];
		$this->description = $label['payment_bitpay_descr'];

		if ($this->is_installed()) {
			$sql = "SELECT * FROM config where `key` LIKE 'BITPAY_%'";
			$result = mysql_query($sql) or die(mysql_error() . $sql);

			while ($row = mysql_fetch_array($result)) {
				// check for default transactionspeed value
				if ($row['key'] == "BITPAY_TRANSACTIONSPEED" && $row['val'] == "default") {
					$row['val'] = "";
				}
				define($row['key'], $row['val']);
			}
		}
	}

	function get_currency() {

		return BITPAY_CURRENCY;
	}

	function install() {
		echo "Installing BitPay...<br>";

		$sql = "REPLACE INTO config (`key`, val) VALUES ('BITPAY_ENABLED', 'N'),('BITPAY_APIKEY', ''),('BITPAY_CURRENCY', 'BTC'),('BITPAY_TRANSACTIONSPEED', 'low'),('BITPAY_FULLNOTIFICATIONS', 'false'),('BITPAY_HTTPMODE', 'http'),('BITPAY_REDIRECTURL', ''),('BITPAY_THEME', 'light')";
		mysql_query($sql);
	}

	function uninstall() {
		echo "Uninstall BitPay...<br>";

		$sql = "DELETE FROM config where `key` IN ('BITPAY_ENABLED','BITPAY_APIKEY','BITPAY_CURRENCY','BITPAY_TRANSACTIONSPEED','BITPAY_FULLNOTIFICATIONS','BITPAY_HTTPMODE','BITPAY_REDIRECTURL','BITPAY_THEME')";
		mysql_query($sql);
	}

	function payment_button($order_id) {
		global $label;

		$sql = "SELECT * from orders where order_id='" . $order_id . "'";
		$result = mysql_query($sql) or die(mysql_error() . $sql);
		$order = mysql_fetch_array($result);
		
		// if site only has http support then use email
		$notificationEmail = (BITPAY_HTTPMODE == "http") ? SITE_CONTACT_EMAIL : "";

		require_once "bitpay/bp_lib.php";

		$result = bpCreateInvoice($order_id, $order['amount'], '', array('apiKey' => BITPAY_APIKEY,
			'price' => $order['amount'],
			'currency' => BITPAY_CURRENCY,
			'itemDesc' => $order['quantity'] . ' pixels',
			'transactionSpeed' => BITPAY_TRANSACTIONSPEED,
			'fullNotifications' => BITPAY_FULLNOTIFICATIONS,
			'notificationEmail' => $notificationEmail,
			'redirectURL' => BITPAY_REDIRECTURL,
			'theme' => BITPAY_THEME
		));

		// catch error
		if (isset($result['error'])) {
			echo "There was an error processing your request: " . $result['error']['message'];
			exit;
		}

		echo '<iframe src="' . $result . '&view=iframe&theme=' . BITPAY_THEME . '">Your browser does not support iframes. Please use another browser.</iframe>';
	}

	function config_form() {
		require_once "bitpay/bp_lib.php";

		if ($_REQUEST['action'] == 'save') {
			$bitpay_apikey = $_REQUEST['bitpay_apikey'];
			$bitpay_currency = $_REQUEST['bitpay_currency'];
			$bitpay_transactionspeed = $_REQUEST['bitpay_transactionspeed'];
			$bitpay_fullnotifications = $_REQUEST['bitpay_fullnotifications'];
			$bitpay_httpmode = $_REQUEST['bitpay_httpmode'];
			$bitpay_redirecturl = $_REQUEST['bitpay_redirecturl'];
			$bitpay_theme = $_REQUEST['bitpay_theme'];
		} else {
			$bitpay_apikey = BITPAY_APIKEY;
			$bitpay_currency = BITPAY_CURRENCY;
			$bitpay_transactionspeed = BITPAY_TRANSACTIONSPEED;
			$bitpay_fullnotifications = BITPAY_FULLNOTIFICATIONS;
			$bitpay_httpmode = BITPAY_HTTPMODE;
			$bitpay_redirecturl = BITPAY_REDIRECTURL;
			$bitpay_theme = BITPAY_THEME;
		}
		?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
				<tr>
					<td  bgcolor="#e6f2ea">BitPay Api Key:</td>
					<td  bgcolor="#e6f2ea">
						<input type="text" name="bitpay_apikey" size="33" value="<?php echo $bitpay_apikey; ?>">
						<br />Note: Obtain a BitPay API key by logging into your Merchant account at https://bitpay.com and going to MyAccount > API Access keys. Keep this key private and secure.</td>
				</tr>
				<tr>
					<td  bgcolor="#e6f2ea">BitPay Currency</td>
					<td  bgcolor="#e6f2ea">

						<?php
						$currencies = bpCurrencyList();

						// catch error
						if (isset($result['error'])) {
							echo "There was an error retrieving a list of currencies: " . $result['error']['message'];
							exit;
						}
						?>
						<select name="bitpay_currency">
							<?php
							foreach ($currencies as $ccode => $cname) {
								echo '<option value="' . $ccode . '" ' . (($bitpay_currency == $ccode) ? " selected " : "") . '>' . $ccode . " - " . $cname . '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td bgcolor="#e6f2ea">BitPay transaction speed:</td>
					<td bgcolor="#e6f2ea">
						<select name="bitpay_transactionspeed">
							<option value="default" <?php echo (($bitpay_transactionspeed == 'default') ? " selected " : "") ?>>From BitPay</option>
							<option value="high" <?php echo (($bitpay_transactionspeed == 'high') ? " selected " : "") ?>>High</option>
							<option value="medium" <?php echo (($bitpay_transactionspeed == 'medium') ? " selected " : "") ?>>Medium</option>
							<option value="low" <?php echo (($bitpay_transactionspeed == 'low') ? " selected " : "") ?>>Low</option>
						</select>
						<br />Note: This will override the setting in your BitPay merchant dashboard unless set it to "From BitPay".
					</td>
				</tr>
				<tr>
					<td bgcolor="#e6f2ea">BitPay Redirect URL:</td>
					<td bgcolor="#e6f2ea">
						<input type="text" name="bitpay_redirecturl" size="33" value="<?php echo $bitpay_redirecturl; ?>">
						<br />Note: The URL you want BitPay to display on the receipt to return the user back to your website. This field is optional.</td>
				</tr>
				<tr>
					<td  bgcolor="#e6f2ea">Is your site accessible by https:// ?</td>
					<td  bgcolor="#e6f2ea">
						<select name="bitpay_httpmode">
							<option value="https" <?php echo (($bitpay_httpmode == 'https') ? " selected " : "") ?>>Yes</option>
							<option value="http" <?php echo (($bitpay_httpmode == 'http') ? " selected " : "") ?>>No</option>
						</select>
						<br />Note: If your site is not accessible by https:// then you must manually verify orders. If you set this to No then when orders are confirmed you will receive emails from BitPay.com to the email address configured as your Site Contact Email in the MDS configuration.
					</td>
				</tr>
				<tr>
					<td  bgcolor="#e6f2ea">BitPay theme:</td>
					<td  bgcolor="#e6f2ea">
						<select name="bitpay_theme">
							<option value="light" <?php echo (($bitpay_theme == 'light') ? " selected " : "") ?>>Light</option>
							<option value="dark" <?php echo (($bitpay_theme == 'dark') ? " selected " : "") ?>>Dark</option>
						</select>
					</td>
				</tr>

				<td  bgcolor="#e6f2ea" colspan=2><input type="submit" value="Save">
				</td>
				</tr>

			</table>
			<input type="hidden" name="pay" value="<?php echo $_REQUEST['pay']; ?>">
			<input type="hidden" name="action" value="save">
		</form>

		<?php
	}

	function save_config() {
		$sql = "REPLACE INTO config (`key`, val) VALUES ('BITPAY_APIKEY', '" . $_REQUEST['bitpay_apikey'] . "'),('BITPAY_CURRENCY', '" . $_REQUEST['bitpay_currency'] . "'),('BITPAY_TRANSACTIONSPEED', '" . $_REQUEST['bitpay_transactionspeed'] . "'),('BITPAY_FULLNOTIFICATIONS', '" . $_REQUEST['bitpay_fullnotifications'] . "'),('BITPAY_HTTPMODE', '" . $_REQUEST['bitpay_httpmode'] . "'),('BITPAY_REDIRECTURL', '" . $_REQUEST['bitpay_redirecturl'] . "'),('BITPAY_THEME', '" . $_REQUEST['bitpay_theme'] . "')";
		mysql_query($sql);
	}

	// true or false
	function is_enabled() {
		$sql = "SELECT val from config where `key`='BITPAY_ENABLED' ";
		$result = mysql_query($sql) or die(mysql_error() . $sql);
		$row = mysql_fetch_array($result);
		if ($row['val'] == 'Y') {
			return true;
		} else {
			return false;
		}
	}

	// true or false
	function is_installed() {
		$sql = "SELECT val from config where `key`='BITPAY_ENABLED' ";
		$result = mysql_query($sql) or die(mysql_error() . $sql);
		if (mysql_num_rows($result) > 0) {
			return true;
		} else {
			return false;
		}
	}

	function enable() {
		$sql = "UPDATE config set val='Y' where `key`='BITPAY_ENABLED' ";
		$result = mysql_query($sql) or die(mysql_error() . $sql);
	}

	function disable() {
		$sql = "UPDATE config set val='N' where `key`='BITPAY_ENABLED' ";
		$result = mysql_query($sql) or die(mysql_error() . $sql);
	}

	function process_payment_return() {
		
	}

}

<?php 
/**
 * @version		$Id: 2checkout.php 69 2010-09-12 01:31:15Z ryan $
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

if (!defined('MDSROOT')) {
	define("MDSROOT", dirname(".."));
}

require_once "../config.php";

require_once("Twocheckout/Twocheckout.php");

define (LOGGING, 'Y');
$_PAYMENT_OBJECTS['_2CO'] =  new _2CO;

function _2co_mail_error($msg) {

	$date = date("D, j M Y H:i:s O"); 
	
	$headers = "From: ". SITE_CONTACT_EMAIL ."\r\n";
	$headers .= "Reply-To: ".SITE_CONTACT_EMAIL ."\r\n";
	$headers .= "Return-Path: ".SITE_CONTACT_EMAIL ."\r\n";
	$headers .= "X-Mailer: PHP" ."\r\n";
	$headers .= "Date: $date" ."\r\n"; 
	$headers .= "X-Sender-IP: $REMOTE_ADDR" ."\r\n";

	$entry_line =  "(payal error detected) $msg\r\n "; 
	$log_fp = @fopen("logs.txt", "a"); 
	@fputs($log_fp, $entry_line); 
	@fclose($log_fp);

	@mail(SITE_CONTACT_EMAIL, "Error message from ".SITE_NAME." 2Checkout script. ", $msg, $headers);

}

function _2co_log_entry ($entry_line) {

	if (LOGGING == 'Y') {

		$entry_line =  "$entry_line\r\n "; 
		$log_fp = @fopen("logs.txt", "a"); 
		@fputs($log_fp, $entry_line); 
		@fclose($log_fp);

	}


}

function format_number($str,$decimal_places='2',$decimal_padding="0"){
       /* firstly format number and shorten any extra decimal places */
       /* Note this will round off the number pre-format $str if you dont want this fucntionality */
       $str          =  number_format($str,$decimal_places,'.','');    // will return 12345.67
       $number      = explode('.',$str);
       $number[1]    = (isset($number[1]))?$number[1]:''; // to fix the PHP Notice error if str does not contain a decimal placing.
       $decimal    = str_pad($number[1],$decimal_places,$decimal_padding);
       return (float) $number[0].'.'.$decimal;
}

###########################################################################
# Payment Object
class _2CO {

	//global $label;

	var $name;
	var $description;
	var $className="_2CO";

	function _2co() {

		global $label;
		$this->name=$label['payment_2co_name'];
		$this->description=$label['payment_2co_descr'];

		if ($this->is_installed()) {

			$sql = "SELECT * FROM config where `key`='_2CO_ENABLED' OR `key`='_2CO_PRIVATE_KEY' OR `key`='_2CO_PUBLISHABLE_KEY' OR `key`='_2CO_SID' OR `key`='_2CO_DEMO' OR `key`='_2CO_VERIFY_SSL' OR `key`='_2CO_SECRET_WORD' OR `key`='_2CO_X_RECEIPT_LINK_URL'";
			$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);

			while ($row=mysqli_fetch_array($result)) {

				define ($row['key'], $row['val']);

			}

			define ('_2CO_CURRENCY', 'USD');

		}

	}

	function get_currency() {
		return 'USD';
	}

	function install() {

		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_ENABLED', 'N')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_PRIVATE_KEY', '')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_PUBLISHABLE_KEY', '')";
		mysqli_query($GLOBALS['connection'], $sql);

		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_SID', '')";
		mysqli_query($GLOBALS['connection'], $sql);
		//$sql = "REPLACE INTO config (`key`, val, descr) VALUES ('_2CO_PRODUCT_ID', '1', '# Your 2CO seller ID number.')";
		//mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_DEMO', 'Y')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_VERIFY_SSL', 'Y')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_SECRET_WORD', '')";
		mysqli_query($GLOBALS['connection'], $sql);

		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_X_RECEIPT_LINK_URL', '')";
		mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));

	}

	function uninstall() {

		$sql = "DELETE FROM config where `key`='_2CO_ENABLED'";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_SID'";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_PRIVATE_KEY'";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_PUBLISHABLE_KEY'";
		mysqli_query($GLOBALS['connection'], $sql);
		//$sql = "REPLACE INTO config (`key`, val, descr) VALUES ('_2CO_PRODUCT_ID', '1', '# Your 2CO seller ID number.')";
		//mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_DEMO'";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_VERIFY_SSL'";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_SECRET_WORD'";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "DELETE FROM config where `key`='_2CO_PAYMENT_ROUTINE'";
		mysqli_query($GLOBALS['connection'], $sql);

		$sql = "DELETE FROM config where `key`='_2CO_X_RECEIPT_LINK_URL'";
		mysqli_query($GLOBALS['connection'], $sql);


	}

	function payment_button($order_id) {

		global $label;

		$sql = "SELECT * from orders where order_id='".$order_id."'";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);
		$order_row = mysqli_fetch_array($result);
		
		// https://github.com/2Checkout/2checkout-php/wiki

		// Your sellerId(account number) and privateKey are required to make the Payment API Authorization call.
		Twocheckout::privateKey(_2CO_PRIVATE_KEY);
		Twocheckout::sellerId(_2CO_SID);
		
		// To use your sandbox account set sandbox to true
		if (_2CO_DEMO == 'Y') {
			$formUrl = "https://sandbox.2checkout.com/checkout/purchase";
			$sandbox = true;
		} else {
			$formUrl = "https://www.2checkout.com/checkout/purchase";
			$sandbox = false;
		}
		Twocheckout::sandbox($sandbox);

		// If you want to turn off SSL verification (Please don't do this in your production environment)
		$verifyssl = (_2CO_VERIFY_SSL == 'Y') ? true : false;
		Twocheckout::verifySSL($verifyssl);  // this is set to true by default

		// All methods return an Array by default or you can set the format to 'json' to get a JSON response.
		Twocheckout::format('json');		

		// output form
		$params = array(
			'mode' => '2CO',
			'sid' => _2CO_SID,
			'merchant_order_id' => $order_id,
			'li_0_name' => 'Pixels',
			'li_0_product_id' => 'Pixels',
			'li_0_price' => convert_to_currency($order_row['price'], $order_row['currency'], 'USD'),
			'li_0_type' => 'product',
			'li_0_description' => 'Pixels at ' . BASE_HTTP_PATH,
			'x_receipt_link_url' => _2CO_X_RECEIPT_LINK_URL
		);

		Twocheckout_Charge::form($params);
	}

	function config_form() {

		if ($_REQUEST['action']=='save') {
			$_2co_sid = $_REQUEST['_2co_sid'];
			$_2co_private_key = $_REQUEST['_2co_private_key'];
			$_2co_publishable_key = $_REQUEST['_2co_publishable_key'];
			$_2co_payment_routine = $_REQUEST['_2co_payment_routine'];
			$_2co_demo = $_REQUEST['_2co_demo'];
			$_2co_verify_ssl = $_REQUEST['_2co_verify_ssl'];
			$_2co_secret_word = $_REQUEST['_2co_secret_word'];
			$_2co_x_receipt_link_url = $_REQUEST['_2co_x_receipt_link_url'];
		} else {
			$_2co_sid = _2CO_SID;
			$_2co_private_key = _2CO_PRIVATE_KEY;
			$_2co_publishable_key = _2CO_PUBLISHABLE_KEY;
			$_2co_payment_routine = _2CO_PAYMENT_ROUTINE;
			$_2co_demo = _2CO_DEMO;
			$_2co_verify_ssl = _2CO_VERIFY_SSL;
			$_2co_secret_word = _2CO_SECRET_WORD;
			$_2co_x_receipt_link_url = _2CO_X_RECEIPT_LINK_URL;
		}

		$host = $_SERVER['SERVER_NAME']; // hostname
		$http_url = $_SERVER['PHP_SELF']; // eg /ojo/admin/edit_config.php
		$http_url = explode ("/", $http_url);
		array_pop($http_url); // get rid of filename
		array_pop($http_url); // get rid of /admin
		$http_url = implode ("/", $http_url);

		$returnlink = (empty($_2co_x_receipt_link_url) ? "http://" . $host.$http_url."/users/thanks.php?m=".$this->className : $_2co_x_receipt_link_url);

		?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2"  bgcolor="#e6f2ea">
      <font face="Verdana" size="1"><b>2Checkout Payment Settings</b><br>
	  Note: The script requires a 2Checkout version 2 account.<br>
	  2Checkout allows only 1 account per website, so if you do not have a 2Checkout account for this website, you will need to register a new 2Checkout account to use this payment option.<br>
	  
	</font></td>

    </tr>
	<tr>
      <td width="20%" bgcolor="#e6f2ea"><font face="Verdana" size="1">2Checkout Seller ID</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <input type="text" name="_2co_sid" size="29" value="<?php echo $_2co_sid; ?>"><br>This is your Account ID</font></td>
    </tr>
	<tr>
      <td width="20%" bgcolor="#e6f2ea"><font face="Verdana" size="1">2Checkout Publishable Key</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <input type="text" name="_2co_publishable_key" size="50" value="<?php echo $_2co_publishable_key; ?>"><br>You can find your Publishable Key by going to API in your 2Checkout account.)</font></td>
    </tr>
	<tr>
      <td width="20%" bgcolor="#e6f2ea"><font face="Verdana" size="1">2Checkout Private Key</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <input type="text" name="_2co_private_key" size="50" value="<?php echo $_2co_private_key; ?>"><br>You can find your Private Key by going to API in your 2Checkout account.)</font></td>
    </tr>
	
	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">Demo Mode (Y/N)</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
       <input type="radio" name="_2co_demo" value="Y"  <?php if ($_2co_demo=='Y') { echo " checked "; } ?> >Yes <br>
	  <input type="radio" name="_2co_demo" value="N"  <?php if ($_2co_demo=='N') { echo " checked "; } ?> >No<br></font></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">Verify SSL?</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
       <input type="radio" name="_2co_verify_ssl" value="Y"  <?php if ($_2co_verify_ssl=='Y') { echo " checked "; } ?> >Yes <br>
	  <input type="radio" name="_2co_verify_ssl" value="N"  <?php if ($_2co_verify_ssl=='N') { echo " checked "; } ?> >No<br></font></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">2CO Secret Word</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <input type="text" name="_2co_secret_word" size="50" value="<?php echo $_2co_secret_word; ?>"><br>(This is the secret word that is entered under the Account > Site Management > Checkout Options section of your 2CO account)</font></td>
    </tr>

	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">2Checkout receipt link URL.</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <input type="text" name="_2co_x_receipt_link_url" size="50" value="<?php echo $returnlink; ?>"><br> (Enter the return URL here. The return URL for should be: <b>http://<?php echo $host.$http_url."/users/thanks.php?m=".$this->className; ?></b>  <br>This setting overwrites the 'direct return' URL set in the Look and Feel section your 2CO account.)</font></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">2Checkout Currency is passed in as USD</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <select disabled name="_2co_currency" >
	  <?php /*
	  2co supported currencies:
Australian Dollar (AUD) 
Canadian Dollar (CAD) 
Swiss Franc (CHF) 
Danish Krone (DKK) 
Euro (EUR) 
British Pound (GBP) 
Hong Kong Dollar (HKD) 
Japanese Yen (JPY) 
Norwegian Krone (NOK) 
New Zealand Dollar (NZD) 
Swedish Krona (SEK) 
U.S. Dollar (USD)

	  */ ?>
	  	<option value="USD" <?php define('_2CO_CURRENCY','USD'); if (_2CO_CURRENCY=='USD') { echo " selected "; }  ?> >USD</option>
		<option value="AUD" <?php if (_2CO_CURRENCY=='AUD') { echo " selected "; }  ?> >AUD</option>
		<option value="EUR" <?php if (_2CO_CURRENCY=='EUR') { echo " selected "; }  ?> >EUR</option>
		<option value="USD" selected <?php if (_2CO_CURRENCY=='USD') { echo " selected "; }  ?> >USD</option>
		<option value="CAD" <?php if (_2CO_CURRENCY=='CAD') { echo " selected "; }  ?> >CAD</option>
		<option value="JPY" <?php if (_2CO_CURRENCY=='JPY') { echo " selected "; }  ?> >JPY</option>
		<option value="GBP" <?php if (_2CO_CURRENCY=='GBP') { echo " selected "; }  ?> >GBP</option>
	  
	  </select>(Disabled - Users select their preferred currency at checkout)</font></td>
    </tr>
	</tr>
	 <tr>
	
      <td  bgcolor="#e6f2ea" colspan=2><font face="Verdana" size="1"><input type="submit" value="Save">
	  </td>
	  </tr>
	</table>
	<input type="hidden" name="pay" value="<?php echo $_REQUEST['pay'];?>">
  <input type="hidden" name="action" value="save">
  </form>


		<?php

	}

	function save_config() {
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_PRIVATE_KEY', '".$_REQUEST['_2co_private_key']."')";
		mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_PUBLISHABLE_KEY', '".$_REQUEST['_2co_publishable_key']."')";
		mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_SID', '".$_REQUEST['_2co_sid']."')";
		mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_DEMO', '".$_REQUEST['_2co_demo']."')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_VERIFY_SSL', '".$_REQUEST['_2co_verify_ssl']."')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_SECRET_WORD', '".$_REQUEST['_2co_secret_word']."')";
		mysqli_query($GLOBALS['connection'], $sql);
		$sql = "REPLACE INTO config (`key`, val) VALUES ('_2CO_X_RECEIPT_LINK_URL', '".$_REQUEST['_2co_x_receipt_link_url']."')";
		mysqli_query($GLOBALS['connection'], $sql);
	}

	// true or false
	function is_enabled() {

		$sql = "SELECT val from `config` where `key`='_2CO_ENABLED' ";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);
		$row = mysqli_fetch_array($result);
		if ($row['val']=='Y') {
			return true;

		} else {
			return false;

		}

	}

	function is_installed() {

		$sql = "SELECT val from config where `key`='_2CO_ENABLED' ";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);
		//$row = mysqli_fetch_array($result);

		if (mysqli_num_rows($result)>0) {
			return true;

		} else {
			return false;

		}

	}

	function enable() {

		$sql = "UPDATE config set val='Y' where `key`='_2CO_ENABLED' ";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);

	}

	function disable() {

		$sql = "UPDATE config set val='N' where `key`='_2CO_ENABLED' ";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']).$sql);

	}

	function process_payment_return() {
		$params = array();
		foreach ($_REQUEST as $k => $v) {
			$params[$k] = $v;
		}
		$passback = Twocheckout_Return::check($params, _2CO_SECRET_WORD);
		if($passback['response_code'] == 'Success') {

			global $label;

			if ($_REQUEST['key']!='') { 

				$order_number = $_REQUEST['order_number'];
				//$order_number = _2CO_SID."-".$order_number;
				if (_2CO_DEMO=='Y') {
					$order_number = 1;
				}
				$card_holder_name = $_REQUEST['card_holder_name'];
				$street_address = $_REQUEST['street_address'];
				$city = $_REQUEST['city'];
				$state = $_REQUEST['state'];
				$zip = $_REQUEST['zip'];
				$country = $_REQUEST['country'];
				$email = $_REQUEST['email'];
				$phone = $_REQUEST['phone'];
				$credit_card_processed = $_REQUEST['credit_card_processed']; // Y = successfull. K = pending
				$total = $_REQUEST['total'];
				$product_id = $_REQUEST['product_id']; // c2o product id
				$quantity = $_REQUEST['quantity']; // quantity
				$merchant_product_id = $_REQUEST['merchant_product_id']; //
				$order_id = $_REQUEST['merchant_order_id'];
				$product_description = $_REQUEST['product_description'];
				$x_MD5_Hash = strtolower ( $_REQUEST['key']);  // md5 (secret word + vendor number + order number + total)

				_2co_log_entry (json_encode($params));

				// process order

				//$_2CO = new _2CO(); // load in the constants..

				// get customer's order
				$sql = "SELECT * FROM orders where order_id='".$order_id."'";
				$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);
				$order_row = mysqli_fetch_array($result);

				if ($credit_card_processed=='Y') {
					# Credit card processed OK
					complete_order ($order_row['user_id'], $order_id);
					debit_transaction($order_id, $total, 'USD', $order_number, $product_id, '_2CO');
					?>
					<center>

					<img src="<?php echo SITE_LOGO_URL; ?>" />
					<h3>Thank you. Your order was successfully completed. You may <a href="<?php echo BASE_HTTP_PATH; ?>users/publish.php">manage your pixels</a> now.</h3>

					</center>
					<?php

				} elseif ($credit_card_processed=='K') {
					# credit card pending
					pend_order ($order_row['user_id'], $order_id);
					?>
					<center>
					<img src="<?php echo SITE_LOGO_URL; ?>" />
					<h3>Thank you. Your order is pending while the funds are cleared by 2Checkout. Go to the <a href="<?php echo BASE_HTTP_PATH; ?>users/index.php">Main Menu.</a></h3>
					</center>
					<?php

				}

			} else {

				$output = "Error processing payment: " . $response['response_code'] . "<br />Message: " . $response['response_message'];
				echo $output;
				_2co_mail_error ( $output );

			}

		}

	}

}

<?php
/**
 * @version        2.1
 * @package        mds
 * @copyright    (C) Copyright 2010-2020 Ryan Rhode, All rights reserved.
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
require_once __DIR__ . "/../config.php";

$_PAYMENT_OBJECTS['external'] = new external;

function external_mail_error( $msg ) {

	$date = date( "D, j M Y H:i:s O" );

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	@mail( SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " external payment module. ", $msg, $headers );

}

function external_log_entry( $entry_line ) {
	$entry_line = "External:$entry_line\r\n ";
	$log_fp     = fopen( "logs.txt", "a" );
	fputs( $log_fp, $entry_line );
	fclose( $log_fp );
}

###########################################################################
# Payment Object

class external {

	var $name = "Payment";
	var $description = "Pay for your order.";
	var $className = "external";

	function __construct() {
		if ( $this->is_installed() ) {

			$sql = "SELECT * FROM config WHERE 
                           `key`='EXTERNAL_ENABLED' OR 
                           `key`='EXTERNAL_URL' OR 
                           `key`='EXTERNAL_AUTO_APPROVE' OR 
                           `key`='EXTERNAL_BUTTON_TEXT' OR
                           `key`='EXTERNAL_BUTTON_IMAGE'
                           ";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

			while ( $row = mysqli_fetch_array( $result ) ) {
			    $val = isset($row['val']) ? $row['val'] : "";
				define( $row['key'], $val );
			}
		}
	}

	function install() {
		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_ENABLED', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_URL', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_AUTO_APPROVE', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_BUTTON_TEXT', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_BUTTON_IMAGE', '')";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function uninstall() {
		$sql = "DELETE FROM config WHERE `key`='EXTERNAL_ENABLED'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config WHERE `key`='EXTERNAL_URL'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config WHERE `key`='EXTERNAL_AUTO_APPROVE'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config WHERE `key`='EXTERNAL_BUTTON_TEXT'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config WHERE `key`='EXTERNAL_BUTTON_IMAGE'";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function payment_button( $order_id ) {
		global $label;

		$sql = "SELECT * FROM orders WHERE order_id=" . intval( $order_id );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order_row = mysqli_fetch_array( $result );

		?>
        <div style="text-align: center;">
            <input type="image" src="<?php echo htmlspecialchars( EXTERNAL_BUTTON_IMAGE, ENT_QUOTES ); ?>" alt="<?php echo htmlspecialchars( EXTERNAL_BUTTON_TEXT, ENT_QUOTES ); ?>" value="<?php echo htmlspecialchars( EXTERNAL_BUTTON_TEXT, ENT_QUOTES ); ?>" onclick="window.location='<?php echo htmlspecialchars( BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className . "&order_id=" . $order_row['order_id'], ENT_QUOTES ); ?>'">
        </div>

		<?php
	}

	function config_form() {

		if ( $_REQUEST['action'] == 'save' ) {
			$external_enabled      = $_REQUEST['external_enabled'];
			$external_url          = $_REQUEST['external_url'];
			$external_auto_approve = $_REQUEST['external_auto_approve'];
			$external_button_text  = $_REQUEST['external_button_text'];
			$external_button_image = $_REQUEST['external_button_image'];

		} else {
			$external_enabled      = EXTERNAL_ENABLED;
			$external_url          = EXTERNAL_URL;
			$external_auto_approve = EXTERNAL_AUTO_APPROVE;
			$external_button_text  = EXTERNAL_BUTTON_TEXT;
			$external_button_image = EXTERNAL_BUTTON_IMAGE;

		}

		?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">

                <tr>
                    <td colspan="2" bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; "><b>External Payment Settings</b></span>
                    </td>
                </tr>
                <tr>
                    <td width="20%" bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">External URL</span></td>
                    <td bgcolor="#e6f2ea"><span style="font-family: Verdana,serif; font-size: xx-small; ">
                            <input type="text" name="external_url" value="<?php echo htmlspecialchars( $external_url, ENT_QUOTES ); ?>"></span>
                        <div>Scenario: https://example.com/checkout/?add-to-cart=##&quantity=%QUANTITY% - Make a product in WooCommerce that is Virtual and set to the price of a single block. ## is your product id.</div>
                        <div>%AMOUNT% will be replaced with the order amount.</div>
                        <div>%CURRENCY% will be replaced with the order currency.</div>
                        <div>%QUANTITY% will be replaced with the number of blocks ordered.</div>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">Auto-approve?</span></td>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">
                            <select name="external_auto_approve">
                                <option value="yes"<?php if ( $external_auto_approve == "yes" ) {
	                                echo 'selected="selected"';
                                } ?>>Yes</option>
                                <option value="no"<?php if ( $external_auto_approve == "no" ) {
	                                echo 'selected="selected"';
                                } ?>>No</option>
                            </select>
                        </span>
                        <div>Setting to Yes will automatically approve orders before payments are verified by an admin.</div>
                    </td>
                </tr>
                <tr>
                    <td width="20%" bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">Button text</span></td>
                    <td bgcolor="#e6f2ea"><span style="font-family: Verdana,serif; font-size: xx-small; ">
                        <input type="text" name="external_button_text" value="<?php echo htmlspecialchars( $external_button_text, ENT_QUOTES ); ?>"></span>
                    </td>
                </tr>
                <tr>
                    <td width="20%" bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">Button image</span></td>
                    <td bgcolor="#e6f2ea"><span style="font-family: Verdana,serif; font-size: xx-small; ">
                        <input type="text" name="external_button_image" value="<?php echo htmlspecialchars( $external_button_image, ENT_QUOTES ); ?>"></span>
                    </td>
                </tr>
                <tr>

                    <td bgcolor="#e6f2ea" colspan=2>
                        <span style="font-family: Verdana,serif; font-size: xx-small; "><input type="submit" value="Save"></span>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="pay" value="<?php echo htmlspecialchars( $_REQUEST['pay'], ENT_QUOTES ); ?>">
            <input type="hidden" name="action" value="save">

        </form>

		<?php

	}

	function save_config() {

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_URL', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['external_url'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_AUTO_APPROVE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['external_auto_approve'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_BUTTON_TEXT', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['external_button_text'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('EXTERNAL_BUTTON_IMAGE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['external_button_image'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	// true or false
	function is_enabled() {

		$sql = "SELECT val FROM `config` WHERE `key`='EXTERNAL_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );
		if ( $row['val'] == 'Y' ) {
			return true;

		} else {
			return false;

		}

	}

	function is_installed() {

		$sql = "SELECT val FROM config WHERE `key`='EXTERNAL_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		//$row = mysqli_fetch_array($result);

		if ( mysqli_num_rows( $result ) > 0 ) {
			return true;

		} else {
			return false;

		}

	}

	function enable() {

		$sql = "UPDATE config SET val='Y' WHERE `key`='EXTERNAL_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	}

	function disable() {

		$sql = "UPDATE config SET val='N' WHERE `key`='EXTERNAL_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	}

	function process_payment_return() {
		global $f2;
//		if ( ( $_REQUEST['order_id'] != '' ) && ( $_REQUEST['nhezk5'] != '' ) ) {
		if ( ( $_REQUEST['order_id'] != '' ) ) {

			if ( $_SESSION['MDS_ID'] == '' ) {

				echo "Error: You must be logged in to view this page";

			} else {

				$order_id = intval( $_REQUEST['order_id'] );

				// Save order id in cookie for later
				$_COOKIE['mds_order_id'] = $order_id;

				$url = $f2->filter( EXTERNAL_URL );

				$sql = "SELECT * FROM orders WHERE order_id='" . $order_id . "'";
				$result = mysqli_query( $GLOBALS['connection'], $sql ) or external_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
				$row = mysqli_fetch_array( $result );

				if ( EXTERNAL_AUTO_APPROVE == "yes" ) {
					complete_order( $row['user_id'], $order_id );
					debit_transaction( $order_id, $row['price'], $row['currency'], 'External', $url, 'External' );
				}

				$banner_data = load_banner_constants($row['banner_id']);
				$quantity = intval($row['quantity']) / intval($banner_data['block_width']) / intval($banner_data['block_height']);

				$dest = str_replace( '%AMOUNT%', urlencode( $row['price'] ), $url );
				$dest = str_replace( '%CURRENCY%', urlencode( $row['currency'] ), $dest );
				$dest = str_replace( '%QUANTITY%', urlencode( $quantity ), $dest );
				$dest = $dest . '&mdsid=' . $order_id;

				//header( "Location: " .  $dest );
				echo "<script>top.window.location = '$dest'</script>";
				exit;
			}

		}

	}

	function complete_order( $order_id ) {
		global $f2;

		$url = $f2->filter( EXTERNAL_URL );

		$sql = "SELECT * FROM orders WHERE order_id='" . intval( $order_id ) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or external_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );

		complete_order( $row['user_id'], $order_id );
		debit_transaction( $order_id, $row['price'], $row['currency'], 'External', $url, 'External' );
	}

	function get_quantity( $order_id ) {
		$sql = "SELECT * FROM orders WHERE order_id='" . intval( $order_id ) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or external_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );

		return $row['quantity'];
	}

}

?>
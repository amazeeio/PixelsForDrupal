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

$_PAYMENT_OBJECTS['voucher'] = new voucher;

function voucher_mail_error( $msg ) {

	$date = date( "D, j M Y H:i:s O" );

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	@mail( SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " voucher payment module. ", $msg, $headers );

}

function voucher_log_entry( $entry_line ) {
	$entry_line = "Voucher: $entry_line\r\n ";
	$log_fp     = fopen( "logs.txt", "a" );
	fputs( $log_fp, $entry_line );
	fclose( $log_fp );
}

###########################################################################
# Payment Object

class voucher {

	var $name = "Voucher";
	var $description = "Pay for your order using a voucher.";
	var $className = "voucher";

	function __construct() {
		if ( $this->is_installed() ) {

			$sql = "SELECT * FROM config WHERE 
                           `key`='VOUCHER_ENABLED' OR 
                           `key`='VOUCHER_AUTO_APPROVE'
                           ";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

			while ( $row = mysqli_fetch_array( $result ) ) {
			    $val = isset($row['val']) ? $row['val'] : "";
				define( $row['key'], $val );
			}
		}
	}

	function install() {
		$sql = "REPLACE INTO config (`key`, val) VALUES ('VOUCHER_ENABLED', '')";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "REPLACE INTO config (`key`, val) VALUES ('VOUCHER_AUTO_APPROVE', '')";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function uninstall() {
		$sql = "DELETE FROM config WHERE `key`='VOUCHER_ENABLED'";
		mysqli_query( $GLOBALS['connection'], $sql );

		$sql = "DELETE FROM config WHERE `key`='VOUCHER_AUTO_APPROVE'";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function payment_button( $order_id ) {
		global $label;

		$sql = "SELECT * FROM orders WHERE order_id=" . intval( $order_id );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order_row = mysqli_fetch_array( $result );

		?>
        <div style="text-align: center;">
          <form id="voucher" action="<?php echo htmlspecialchars( BASE_HTTP_PATH . "users/thanks.php", ENT_QUOTES ); ?>" method="get">
            <input type="hidden" name="m" value="<?php echo $this->className; ?>" />
            <input type="hidden" name="order_id" value="<?php echo $order_row['order_id']; ?>" />
            <label for="voucher_code">Voucher code:</label>
            <input type="input" name="voucher_code" id="voucher_code" required />
            <input type="submit" value="Submit" />
          </form>
        </div>
		<?php
	}

	function config_form() {

		if ( $_REQUEST['action'] == 'save' ) {
			$voucher_enabled      = $_REQUEST['voucher_enabled'];
			$voucher_auto_approve = $_REQUEST['voucher_auto_approve'];

		} else {
			$voucher_enabled      = VOUCHER_ENABLED;
			$voucher_auto_approve = VOUCHER_AUTO_APPROVE;
		}

		?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">

                <tr>
                    <td colspan="2" bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; "><b>Voucher Payment Settings</b></span>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">Auto-approve?</span></td>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,serif; font-size: xx-small; ">
                            <select name="voucher_auto_approve">
                                <option value="yes"<?php if ( $voucher_auto_approve == "yes" ) {
	                                echo 'selected="selected"';
                                } ?>>Yes</option>
                                <option value="no"<?php if ( $voucher_auto_approve == "no" ) {
	                                echo 'selected="selected"';
                                } ?>>No</option>
                            </select>
                        </span>
                        <div>Setting to Yes will automatically approve orders before payments are verified by an admin.</div>
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
		$sql = "REPLACE INTO config (`key`, val) VALUES ('VOUCHER_AUTO_APPROVE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voucher_auto_approve'] ) . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	// true or false
	function is_enabled() {

		$sql = "SELECT val FROM `config` WHERE `key`='VOUCHER_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );
		if ( $row['val'] == 'Y' ) {
			return true;

		} else {
			return false;

		}

	}

	function is_installed() {

		$sql = "SELECT val FROM config WHERE `key`='VOUCHER_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		//$row = mysqli_fetch_array($result);

		if ( mysqli_num_rows( $result ) > 0 ) {
			return true;

		} else {
			return false;

		}

	}

	function enable() {

		$sql = "UPDATE config SET val='Y' WHERE `key`='VOUCHER_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	}

	function disable() {

		$sql = "UPDATE config SET val='N' WHERE `key`='VOUCHER_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	}

	function process_payment_return() {
    global $f2;
    
		if ( ( $_REQUEST['order_id'] != '' ) ) {

			if ( $_SESSION['MDS_ID'] == '' ) {

				echo "Error: You must be logged in to view this page";

			} else {

        $order_id = intval( $_REQUEST['order_id'] );
        $voucher = $f2->filter($_REQUEST['voucher_code']);

				$sql = "SELECT * FROM orders WHERE order_id='" . $order_id . "'";
				$result = mysqli_query( $GLOBALS['connection'], $sql ) or voucher_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
				$row = mysqli_fetch_array( $result );

				if ( VOUCHER_AUTO_APPROVE == "yes" ) {
					complete_order( $row['user_id'], $order_id );
					debit_transaction( $order_id, $row['price'], $row['currency'], 'Voucher', $voucher, 'Voucher' );
				}

				$banner_data = load_banner_constants($row['banner_id']);
				$quantity = intval($row['quantity']) / intval($banner_data['block_width']) / intval($banner_data['block_height']);

				$dest = str_replace( '%AMOUNT%', urlencode( $row['price'] ), $url );
				$dest = str_replace( '%CURRENCY%', urlencode( $row['currency'] ), $dest );
				$dest = str_replace( '%QUANTITY%', urlencode( $quantity ), $dest );
				$dest = $dest . '&mdsid=' . $order_id;

				echo "Voucher: $voucher";
				exit;
			}

		}

	}

}

?>

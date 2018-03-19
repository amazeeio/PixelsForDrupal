<?php

require_once "../config.php";

// https://www.liqpay.ua/en/doc/checkout
// https://www.liqpay.ua/documentation/en/api/aquiring/checkout/
// https://www.liqpay.ua/documentation/en/api/aquiring/checkout/doc

$_PAYMENT_OBJECTS['LiqPay'] = new LiqPay;

function liqpay_mail_error( $msg ) {
	$date = date( "D, j M Y H:i:s O" );

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	$entry_line = "(LiqPay error detected) $msg\r\n ";
	$log_fp     = @fopen( "logs.txt", "a" );
	fputs( $log_fp, $entry_line );
	fclose( $log_fp );

	mail( SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " LiqPay script. ", $msg, $headers );
}

#####################################################################################

// https://www.liqpay.ua/documentation/en/api/callback
// https://www.liqpay.ua/documentation/en/api/information/status/doc
if ( isset( $_POST['data'] ) && $_POST['data'] != '' ) {

	require_once( __DIR__ . '/LiqPay/LiqPayAPI.php' );

	$LiqPayAPI = new LiqPayAPI( LIQPAY_PUBLIC_KEY, LIQPAY_PRIVATE_KEY );

	$sign = $LiqPayAPI->str_to_sign( LIQPAY_PRIVATE_KEY . $_POST['data'] . LIQPAY_PRIVATE_KEY );

	// check if signatures match
	if ( $sign != $_POST['signature'] ) {
		// if not then possible fraud
		die( ":(" );
	}

	$data = json_decode( base64_decode( $_POST['data'] ), true );

	// get payment status
	$res = $LiqPayAPI->api( "request", array(
		'action'   => 'status',
		'version'  => '3',
		'order_id' => $data['order_id']
	) );

	if ( $res->status == "success" || ( LIQPAY_TEST_MODE == 1 && $res->status == "sandbox" ) ) {
		/*
        stdClass Object
        (
            [result] => ok
            [action] => pay
            [payment_id] => 123456789
            [status] => sandbox
            [version] => 3
            [type] => buy
            [paytype] => card
            [public_key] => i12345678901
            [acq_id] => 123457
            [order_id] => 8
            [liqpay_order_id] => A1B2CDEF1234567890123456
            [description] => Website Title: 8
            [sender_card_mask2] => 411111*11
            [sender_card_bank] => Test
            [sender_card_type] => visa
            [sender_card_country] => 804
            [ip] => 127.0.0.1
            [amount] => 900
            [currency] => UAH
            [sender_commission] => 0
            [receiver_commission] => 24.75
            [agent_commission] => 0
            [amount_debit] => 900
            [amount_credit] => 900
            [commission_debit] => 0
            [commission_credit] => 24.75
            [currency_debit] => UAH
            [currency_credit] => UAH
            [sender_bonus] => 0
            [amount_bonus] => 0
            [verifycode] => Y
            [mpi_eci] => 7
            [is_3ds] =>
            [create_date] => 1503928704524
            [end_date] => 1503928706044
            [transaction_id] => 123456789
        )
        */

		$transaction['action']              = filter_var( $data['action'], FILTER_SANITIZE_STRING );
		$transaction['payment_id']          = filter_var( $data['payment_id'], FILTER_VALIDATE_INT );
		$transaction['status']              = filter_var( $data['status'], FILTER_SANITIZE_STRING );
		$transaction['version']             = filter_var( $data['version'], FILTER_VALIDATE_INT );
		$transaction['type']                = filter_var( $data['type'], FILTER_SANITIZE_STRING );
		$transaction['paytype']             = filter_var( $data['paytype'], FILTER_SANITIZE_STRING );
		$transaction['public_key']          = filter_var( $data['public_key'], FILTER_SANITIZE_STRING );
		$transaction['acq_id']              = filter_var( $data['acq_id'], FILTER_VALIDATE_INT );
		$transaction['order_id']            = filter_var( $data['order_id'], FILTER_SANITIZE_STRING );
		$transaction['liqpay_order_id']     = filter_var( $data['liqpay_order_id'], FILTER_SANITIZE_STRING );
		$transaction['description']         = filter_var( $data['description'], FILTER_SANITIZE_STRING );
		$transaction['sender_phone']        = filter_var( $data['sender_phone'], FILTER_SANITIZE_STRING );
		$transaction['sender_card_mask2']   = filter_var( $data['sender_card_mask2'], FILTER_SANITIZE_STRING );
		$transaction['sender_card_bank']    = filter_var( $data['sender_card_bank'], FILTER_SANITIZE_STRING );
		$transaction['sender_card_type']    = filter_var( $data['sender_card_type'], FILTER_SANITIZE_STRING );
		$transaction['sender_card_country'] = filter_var( $data['sender_card_country'], FILTER_VALIDATE_INT );
		$transaction['ip']                  = filter_var( $data['ip'], FILTER_SANITIZE_STRING );
		$transaction['card_token']          = filter_var( $data['card_token'], FILTER_SANITIZE_STRING );
		$transaction['info']                = filter_var( $data['info'], FILTER_SANITIZE_STRING );
		$transaction['amount']              = filter_var( $data['amount'], FILTER_VALIDATE_FLOAT );
		$transaction['currency']            = filter_var( $data['currency'], FILTER_SANITIZE_STRING );
		$transaction['sender_commission']   = filter_var( $data['sender_commission'], FILTER_VALIDATE_FLOAT );
		$transaction['receiver_commission'] = filter_var( $data['receiver_commission'], FILTER_VALIDATE_FLOAT );
		$transaction['agent_commission']    = filter_var( $data['agent_commission'], FILTER_VALIDATE_FLOAT );
		$transaction['amount_debit']        = filter_var( $data['amount_debit'], FILTER_VALIDATE_FLOAT );
		$transaction['amount_credit']       = filter_var( $data['amount_credit'], FILTER_VALIDATE_FLOAT );
		$transaction['commission_debit']    = filter_var( $data['commission_debit'], FILTER_VALIDATE_FLOAT );
		$transaction['commission_credit']   = filter_var( $data['commission_credit'], FILTER_VALIDATE_FLOAT );
		$transaction['currency_debit']      = filter_var( $data['currency_debit'], FILTER_SANITIZE_STRING );
		$transaction['currency_credit']     = filter_var( $data['currency_credit'], FILTER_SANITIZE_STRING );
		$transaction['sender_bonus']        = filter_var( $data['sender_bonus'], FILTER_VALIDATE_FLOAT );
		$transaction['amount_bonus']        = filter_var( $data['amount_bonus'], FILTER_VALIDATE_FLOAT );
		$transaction['bonus_type']          = filter_var( $data['bonus_type'], FILTER_SANITIZE_STRING );
		$transaction['bonus_procent']       = filter_var( $data['bonus_procent'], FILTER_VALIDATE_FLOAT );
		$transaction['authcode_debit']      = filter_var( $data['authcode_debit'], FILTER_SANITIZE_STRING );
		$transaction['authcode_credit']     = filter_var( $data['authcode_credit'], FILTER_SANITIZE_STRING );
		$transaction['rrn_debit']           = filter_var( $data['rrn_debit'], FILTER_SANITIZE_STRING );
		$transaction['rrn_credit']          = filter_var( $data['rrn_credit'], FILTER_SANITIZE_STRING );
		$transaction['mpi_eci']             = filter_var( $data['mpi_eci'], FILTER_SANITIZE_STRING );
		$transaction['is_3ds']              = filter_var( $data['is_3ds'], FILTER_VALIDATE_BOOLEAN );
		$transaction['create_date']         = filter_var( $data['create_date'], FILTER_VALIDATE_INT );
		$transaction['end_date']            = filter_var( $data['end_date'], FILTER_VALIDATE_INT );
		$transaction['moment_part']         = filter_var( $data['moment_part'], FILTER_VALIDATE_BOOLEAN );
		$transaction['transaction_id']      = filter_var( $data['transaction_id'], FILTER_VALIDATE_INT );

		$sql = "SELECT * FROM orders WHERE order_id='" . intval($transaction['order_id']) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or liqpay_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );

		complete_order( $row['user_id'], $transaction['order_id'] );
		debit_transaction( $transaction['order_id'], $transaction['amount'], $transaction['currency'], $transaction['payment_id'], $transaction['description'], 'LiqPay' );
	}
}

###########################################################################
# Payment Object

class LiqPay {

	var $name;
	var $description;
	var $className = "LiqPay";

	function __construct() {
		global $label;

		$this->name        = $label["payment_liqpay_name"];
		$this->description = $label["payment_liqpay_descr"];

		if ( $this->is_installed() ) {
			$sql = "SELECT * FROM config WHERE `key` LIKE 'LIQPAY_%'";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

			while ( $row = mysqli_fetch_array( $result ) ) {
				define( $row['key'], $row['val'] );
			}
		}
	}

	function get_currency() {
		return LIQPAY_CURRENCY;
	}

	function install() {
		echo "Installing LiqPay...<br>";

		$liqpay_ipn_url     = mysqli_real_escape_string( $GLOBALS['connection'], BASE_HTTP_PATH . 'payment/liqpay.php');
		$liqpay_success_url = mysqli_real_escape_string( $GLOBALS['connection'], BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className);

		$sql = "REPLACE INTO config (`key`, val) VALUES 
                ('LIQPAY_ENABLED', 'N'),
                ('LIQPAY_TEST_MODE', '1'),
                ('LIQPAY_PUBLIC_KEY', ''),
                ('LIQPAY_PRIVATE_KEY', ''),
                ('LIQPAY_CURRENCY', 'UAH'),
                ('LIQPAY_IPN_URL', '$liqpay_ipn_url'),
                ('LIQPAY_SUCCESS_URL', '$liqpay_success_url')
                ";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function uninstall() {
		echo "Uninstall LiqPay...<br>";

		$sql = "DELETE FROM config WHERE `key` IN (
                'LIQPAY_ENABLED',
                'LIQPAY_TEST_MODE',
                'LIQPAY_PUBLIC_KEY',
                'LIQPAY_PRIVATE_KEY',
                'LIQPAY_CURRENCY',
                'LIQPAY_IPN_URL',
                'LIQPAY_SUCCESS_URL'
                )";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function payment_button( $order_id ) {
		global $label;

		$order_id = intval( $order_id );

		$sql = "SELECT * FROM orders WHERE order_id='" . intval($order_id) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order = mysqli_fetch_array( $result );

		$item_name = SITE_NAME . " - " . $label['advertiser_ord_order_id'] . " " . $order_id;
		if ( strlen( $item_name ) > 128 ) {
			$item_name = substr( $item_name, 0, 128 );
		}

		// https://www.liqpay.ua/documentation/en/api/aquiring/checkout/doc
		require_once( __DIR__ . '/LiqPay/LiqPayAPI.php' );
		$LiqPayAPI = new LiqPayAPI( LIQPAY_PUBLIC_KEY, LIQPAY_PRIVATE_KEY );

		$html = $LiqPayAPI->cnb_form( array(
			'action'      => 'pay',
			'amount'      => $order['price'],
			'currency'    => LIQPAY_CURRENCY,
			'description' => $item_name,
			'order_id'    => $order_id,
			'sandbox'     => LIQPAY_TEST_MODE,
			'result_url'  => LIQPAY_SUCCESS_URL,
			'server_url'  => LIQPAY_IPN_URL,
			'verifycode'  => 'Y',
			'version'     => '3'
		) );
		echo $html;
	}

	function config_form() {
		if ( $_REQUEST['action'] == 'save' ) {
			$liqpay_test_mode   = filter_var( $_REQUEST['liqpay_test_mode'], FILTER_SANITIZE_STRING );
			$liqpay_public_key  = filter_var( $_REQUEST['liqpay_public_key'], FILTER_SANITIZE_STRING );
			$liqpay_private_key = filter_var( $_REQUEST['liqpay_private_key'], FILTER_SANITIZE_STRING );
			$liqpay_currency    = filter_var( $_REQUEST['liqpay_currency'], FILTER_SANITIZE_STRING );
			$liqpay_ipn_url     = filter_var( $_REQUEST['liqpay_ipn_url'], FILTER_SANITIZE_URL );
			$liqpay_success_url = filter_var( $_REQUEST['liqpay_success_url'], FILTER_SANITIZE_URL );
		} else {
			$liqpay_test_mode   = LIQPAY_TEST_MODE;
			$liqpay_public_key  = LIQPAY_PUBLIC_KEY;
			$liqpay_private_key = LIQPAY_PRIVATE_KEY;
			$liqpay_currency    = LIQPAY_CURRENCY;
			$liqpay_ipn_url     = LIQPAY_IPN_URL;
			$liqpay_success_url = LIQPAY_SUCCESS_URL;
		}

		if ( empty( $liqpay_ipn_url ) ) {
			$liqpay_ipn_url = BASE_HTTP_PATH . 'payment/liqpay.php';
		}

		if ( empty( $liqpay_success_url ) ) {
			$liqpay_success_url = BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className;
		}

		?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove;font-family: Verdana,sans-serif; font-size: small;" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
                <tr>
                    <td bgcolor="#e6f2ea">LiqPay Public Key:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="liqpay_public_key" size="33" value="<?php echo $liqpay_public_key; ?>">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">LiqPay Private Key:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="liqpay_private_key" size="33" value="<?php echo $liqpay_private_key; ?>">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">Use LiqPay Sandbox?</td>
                    <td bgcolor="#e6f2ea">
                        <select name="liqpay_test_mode">
                            <option value="1" <?php echo( ( $liqpay_test_mode == '1' ) ? " selected " : "" ) ?>>Yes</option>
                            <option value="0" <?php echo( ( $liqpay_test_mode == '0' ) ? " selected " : "" ) ?>>No</option>
                        </select>
                        <p>Note: Enables the testing environment for developers. Payer card will not be charged.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">LiqPay Currency:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="liqpay_currency" size="33" value="<?php echo $liqpay_currency; ?>">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">LiqPay IPN URL</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="liqpay_ipn_url" size="50" value="<?php echo $liqpay_ipn_url; ?>">
                        <br>(recommended: <b><?php echo $liqpay_ipn_url; ?></b>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">LiqPay Success URL</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="liqpay_success_url" size="50" value="<?php echo $liqpay_success_url; ?>">
                        <br>(recommended: <b><?php echo $liqpay_success_url ?></b>
                    </td>
                </tr>
                <td bgcolor="#e6f2ea" colspan=2>
                    <input type="submit" value="Save">
                </td>
                </tr>

            </table>
            <input type="hidden" name="pay" value="<?php echo $_REQUEST['pay']; ?>">
            <input type="hidden" name="action" value="save">
        </form>

		<?php
	}

	function save_config() {
		$sql = "REPLACE INTO config (`key`, val) VALUES ('LIQPAY_TEST_MODE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['liqpay_test_mode']) . "'),('LIQPAY_PUBLIC_KEY', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['liqpay_public_key']) . "'),('LIQPAY_PRIVATE_KEY', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['liqpay_private_key']) . "'),('LIQPAY_CURRENCY', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['liqpay_currency']) . "'),('LIQPAY_IPN_URL', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['liqpay_ipn_url']) . "'),('LIQPAY_SUCCESS_URL', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['liqpay_success_url']) . "')";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	// true or false
	function is_enabled() {
		$sql = "SELECT val FROM config WHERE `key`='LIQPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$row = mysqli_fetch_array( $result );
		if ( $row['val'] == 'Y' ) {
			return true;
		} else {
			return false;
		}
	}

	// true or false
	function is_installed() {
		$sql = "SELECT val FROM config WHERE `key`='LIQPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		if ( mysqli_num_rows( $result ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	function enable() {
		$sql = "UPDATE config SET val='Y' WHERE `key`='LIQPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function disable() {
		$sql = "UPDATE config SET val='N' WHERE `key`='LIQPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function process_payment_return() {

	}

}

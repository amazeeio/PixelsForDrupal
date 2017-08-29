<?php

require_once "../config.php";

// https://www.coinpayments.net/merchant-tools
// https://github.com/sigismund/coinpayments
//error_log( print_r( $_POST, true ) );

$_PAYMENT_OBJECTS['CoinPayments'] = new CoinPayments;

function coinpayments_mail_error( $msg ) {
	$date = date( "D, j M Y H:i:s O" );

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	$entry_line = "(CoinPayments error detected) $msg\r\n ";
	$log_fp     = @fopen( "logs.txt", "a" );
	fputs( $log_fp, $entry_line );
	fclose( $log_fp );

	mail( SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " CoinPayments script. ", $msg, $headers );
}

#####################################################################################

// https://www.coinpayments.net/merchant-tools-ipn
if ( isset( $_POST['txn_id'] ) && $_POST['txn_id'] != '' ) {

	require_once( __DIR__ . '/CoinPayments/CoinPaymentsAPI.php' );

	$process_ipn = false;

	if ( COINPAYMENTS_IPN_MODE == "HTTP Auth" ) {
		if ( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			if ( $_SERVER['PHP_AUTH_USER'] == COINPAYMENTS_MERCHANT_ID && $_SERVER['PHP_AUTH_PW'] == COINPAYMENTS_IPN_SECRET ) {
				$process_ipn = true;
			}
		}
	} else if ( COINPAYMENTS_IPN_MODE == "HMAC" ) {

		if ( ! isset( $_SERVER['HTTP_HMAC'] ) || empty( $_SERVER['HTTP_HMAC'] ) ) {
			die( "No HMAC signature sent" );
		}

		$request = file_get_contents( 'php://input' );
		if ( $request === false || empty( $request ) ) {
			die( "Error reading POST data" );
		}

		$merchant = isset( $_POST['merchant'] ) ? $_POST['merchant'] : '';
		if ( empty( $merchant ) ) {
			die( "No Merchant ID passed" );
		}
		if ( $merchant != COINPAYMENTS_MERCHANT_ID ) {
			die( "Invalid Merchant ID" );
		}

		$hmac = hash_hmac( "sha512", $request, COINPAYMENTS_IPN_SECRET );
		if ( $hmac != $_SERVER['HTTP_HMAC'] ) {
			die( "HMAC signature does not match" );
		}

		$process_ipn = true;
	}

	if ( $process_ipn ) {
		$CoinPaymentsAPI = new \Sigismund\CoinPayments\CoinPaymentsAPI( COINPAYMENTS_PRIVATE_KEY, COINPAYMENTS_PUBLIC_KEY, COINPAYMENTS_MERCHANT_ID, COINPAYMENTS_IPN_SECRET );

		try {
			$result = $CoinPaymentsAPI->validate( $_POST, $_SERVER );
		} catch ( Exception $e ) {
			die( $e->getMessage() );
		}

		if ( $result ) {
			/*
			 $_POST array received:
			 Array
            (
                [ipn_version] => 1.0
                [ipn_id] => 92eb5ffee6ae2fec3ad71c777531578f
                [ipn_mode] => hmac
                [merchant] => 0cc175b9c0f1b6a831c399e269772661
                [ipn_type] => simple
                [txn_id] => 4A8A08F09D37B7379564903840
                [status] => 100
                [status_text] => Complete
                [currency1] => USD
                [currency2] => LTCT
                [amount1] => 5.03
                [amount2] => 0.00119
                [subtotal] => 5.03
                [shipping] => 0
                [tax] => 0
                [fee] => 1.0E-5
                [net] => 0.00118
                [item_amount] => 5.03
                [item_name] => Test Site - Order ID: 35
                [first_name] => Firstname
                [last_name] => Lastname
                [email] => test@example.com
                [invoice] => 35
                [received_amount] => 0.00119
                [received_confirms] => 0
            )
			*/

			// Required Fields
			$transaction['ipn_version'] = filter_var( $_POST['ipn_version'], FILTER_SANITIZE_STRING );
			$transaction['ipn_type']    = filter_var( $_POST['ipn_type'], FILTER_SANITIZE_STRING );
			$transaction['ipn_mode']    = filter_var( $_POST['ipn_mode'], FILTER_SANITIZE_STRING );
			$transaction['ipn_id']      = filter_var( $_POST['ipn_id'], FILTER_SANITIZE_STRING );
			$transaction['merchant']    = filter_var( $_POST['merchant'], FILTER_SANITIZE_STRING );

			// Buyer Information (ipn_type = 'simple','button','cart','donation')
			$transaction['first_name'] = filter_var( $_POST['first_name'], FILTER_SANITIZE_STRING );
			$transaction['last_name']  = filter_var( $_POST['last_name'], FILTER_SANITIZE_STRING );
			$transaction['company']    = filter_var( $_POST['company'], FILTER_SANITIZE_STRING );
			$transaction['email']      = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );

			// Simple Button Fields (ipn_type = 'simple')
			$transaction['status']            = filter_var( $_POST['status'], FILTER_VALIDATE_INT );
			$transaction['status_text']       = filter_var( $_POST['status_text'], FILTER_SANITIZE_STRING );
			$transaction['txn_id']            = filter_var( $_POST['txn_id'], FILTER_SANITIZE_STRING );
			$transaction['currency1']         = filter_var( $_POST['currency1'], FILTER_SANITIZE_STRING );
			$transaction['currency2']         = filter_var( $_POST['currency2'], FILTER_SANITIZE_STRING );
			$transaction['amount1']           = filter_var( $_POST['amount1'], FILTER_VALIDATE_FLOAT );
			$transaction['amount2']           = filter_var( $_POST['amount2'], FILTER_VALIDATE_FLOAT );
			$transaction['subtotal']          = filter_var( $_POST['subtotal'], FILTER_VALIDATE_FLOAT );
			$transaction['shipping']          = filter_var( $_POST['shipping'], FILTER_VALIDATE_FLOAT );
			$transaction['tax']               = filter_var( $_POST['tax'], FILTER_VALIDATE_FLOAT );
			$transaction['fee']               = filter_var( $_POST['fee'], FILTER_VALIDATE_FLOAT );
			$transaction['net']               = filter_var( $_POST['net'], FILTER_VALIDATE_FLOAT );
			$transaction['item_amount']       = filter_var( $_POST['item_amount'], FILTER_VALIDATE_INT );
			$transaction['item_name']         = filter_var( $_POST['item_name'], FILTER_SANITIZE_STRING );
			$transaction['invoice']           = filter_var( $_POST['invoice'], FILTER_VALIDATE_INT );
			$transaction['received_amount']   = filter_var( $_POST['received_amount'], FILTER_VALIDATE_FLOAT );
			$transaction['received_confirms'] = filter_var( $_POST['received_confirms'], FILTER_VALIDATE_INT );

			// error_log( print_r( $transaction, true ) );

			$sql = "SELECT * FROM orders WHERE order_id='" . $transaction['invoice'] . "'";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or coinpayments_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
			$row = mysqli_fetch_array( $result );

			complete_order( $row['user_id'], $transaction['invoice'] );
			debit_transaction( $transaction['invoice'], $transaction['amount1'], $transaction['currency1'], $transaction['txn_id'], $transaction['item_name'], 'CoinPayments' );
		}
	}
}

###########################################################################
# Payment Object

class CoinPayments {

	var $name;
	var $description;
	var $className = "CoinPayments";

	function __construct() {
		global $label;

		$this->name        = $label["payment_coinpayments_name"];
		$this->description = $label["payment_coinpayments_descr"];

		if ( $this->is_installed() ) {
			$sql = "SELECT * FROM config WHERE `key` LIKE 'COINPAYMENTS_%'";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

			while ( $row = mysqli_fetch_array( $result ) ) {
				define( $row['key'], $row['val'] );
			}
		}
	}

	function get_currency() {
		return COINPAYMENTS_CURRENCY;
	}

	function install() {
		echo "Installing CoinPayments...<br>";

		$coinpayments_ipn_url     = BASE_HTTP_PATH . 'payment/coinpayments.php';
		$coinpayments_success_url = BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className;
		$coinpayments_cancel_url  = BASE_HTTP_PATH . "users/";

		$sql = "REPLACE INTO config (`key`, val) VALUES 
                ('COINPAYMENTS_ENABLED', 'N'),
                ('COINPAYMENTS_TEST_MODE', 'yes'),
                ('COINPAYMENTS_PUBLIC_KEY', ''),
                ('COINPAYMENTS_PRIVATE_KEY', ''),
                ('COINPAYMENTS_MERCHANT_ID', ''),
                ('COINPAYMENTS_CURRENCY', 'USD'),
                ('COINPAYMENTS_IPN_MODE', 'HMAC'),
                ('COINPAYMENTS_IPN_SECRET', ''),
                ('COINPAYMENTS_IPN_URL', '$coinpayments_ipn_url'),
                ('COINPAYMENTS_SUCCESS_URL', '$coinpayments_success_url'),
                ('COINPAYMENTS_CANCEL_URL', '$coinpayments_cancel_url'),
                ('COINPAYMENTS_BUTTON', '0')
                ";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function uninstall() {
		echo "Uninstall CoinPayments...<br>";

		$sql = "DELETE FROM config WHERE `key` IN (
                'COINPAYMENTS_ENABLED',
                'COINPAYMENTS_TEST_MODE',
                'COINPAYMENTS_PUBLIC_KEY',
                'COINPAYMENTS_PRIVATE_KEY',
                'COINPAYMENTS_MERCHANT_ID',
                'COINPAYMENTS_CURRENCY',
                'COINPAYMENTS_IPN_MODE',
                'COINPAYMENTS_IPN_SECRET',
                'COINPAYMENTS_IPN_URL',
                'COINPAYMENTS_SUCCESS_URL',
                'COINPAYMENTS_CANCEL_URL',
                'COINPAYMENTS_BUTTON'
                )";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function payment_button( $order_id ) {
		global $label;

		$order_id = intval( $order_id );

		$sql = "SELECT * FROM orders WHERE order_id='" . $order_id . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order = mysqli_fetch_array( $result );

		$buttons = array(
			'https://www.coinpayments.net/images/pub/buynow-grey.png',
			'https://www.coinpayments.net/images/pub/buynow-med-grey.png',
			'https://www.coinpayments.net/images/pub/CP-third-large.png',
			'https://www.coinpayments.net/images/pub/CP-third-med.png',
			'https://www.coinpayments.net/images/pub/CP-main-large.png',
			'https://www.coinpayments.net/images/pub/CP-main-medium.png',
			'https://www.coinpayments.net/images/pub/buynow.png',
			'https://www.coinpayments.net/images/pub/buynow-med.png',
			'https://www.coinpayments.net/images/pub/buynow-white.png',
			'https://www.coinpayments.net/images/pub/buynow-med-white.png',
			'https://www.coinpayments.net/images/pub/buynow-ani.png',
			'https://www.coinpayments.net/images/pub/buynow-blue.png',
			'https://www.coinpayments.net/images/pub/buynow-ani-2.png',
			'https://www.coinpayments.net/images/pub/buynow-wide-yellow.png',
			'https://www.coinpayments.net/images/pub/buynow-wide-blue.png',
			'https://www.coinpayments.net/images/pub/buynow-small-white.png'
		);

		$item_name = SITE_NAME . " - " . $label['advertiser_ord_order_id'] . " " . $order_id;
		if ( strlen( $item_name ) > 128 ) {
			$item_name = substr( $item_name, 0, 128 );
		}

		// https://www.coinpayments.net/merchant-tools-simple
		?>
        <form action="https://www.coinpayments.net/index.php" method="post">
            <input type="hidden" name="cmd" value="_pay_simple">
            <input type="hidden" name="reset" value="1">
            <input type="hidden" name="merchant" value="<?php echo COINPAYMENTS_MERCHANT_ID; ?>">
            <input type="hidden" name="currency" value="<?php echo COINPAYMENTS_CURRENCY; ?>">
            <input type="hidden" name="amountf" value="<?php echo $order['price']; ?>">
            <input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
            <input type="hidden" name="invoice" value="<?php echo $order_id; ?>">
            <input type="hidden" name="success_url" value="<?php echo COINPAYMENTS_SUCCESS_URL; ?>">
            <input type="hidden" name="cancel_url" value="<?php echo COINPAYMENTS_CANCEL_URL; ?>">
            <input type="hidden" name="ipn_url" value="<?php echo COINPAYMENTS_IPN_URL; ?>">
            <input type="image" src="<?php echo $buttons[ COINPAYMENTS_BUTTON ]; ?>" alt="Buy Now with CoinPayments.net">
        </form>
		<?php
	}

	function config_form() {
		if ( $_REQUEST['action'] == 'save' ) {
			$coinpayments_test_mode   = filter_var( $_REQUEST['coinpayments_test_mode'], FILTER_SANITIZE_STRING );
			$coinpayments_public_key  = filter_var( $_REQUEST['coinpayments_public_key'], FILTER_SANITIZE_STRING );
			$coinpayments_private_key = filter_var( $_REQUEST['coinpayments_private_key'], FILTER_SANITIZE_STRING );
			$coinpayments_merchant_id = filter_var( $_REQUEST['coinpayments_merchant_id'], FILTER_SANITIZE_STRING );
			$coinpayments_currency    = filter_var( $_REQUEST['coinpayments_currency'], FILTER_SANITIZE_STRING );
			$coinpayments_ipn_mode    = filter_var( $_REQUEST['coinpayments_ipn_mode'], FILTER_SANITIZE_STRING );
			$coinpayments_ipn_secret  = filter_var( $_REQUEST['coinpayments_ipn_secret'], FILTER_SANITIZE_STRING );
			$coinpayments_ipn_url     = filter_var( $_REQUEST['coinpayments_ipn_url'], FILTER_SANITIZE_URL );
			$coinpayments_success_url = filter_var( $_REQUEST['coinpayments_success_url'], FILTER_SANITIZE_URL );
			$coinpayments_cancel_url  = filter_var( $_REQUEST['coinpayments_cancel_url'], FILTER_SANITIZE_URL );
			$coinpayments_button      = filter_var( $_REQUEST['coinpayments_button'], FILTER_VALIDATE_INT );
		} else {
			$coinpayments_test_mode   = COINPAYMENTS_TEST_MODE;
			$coinpayments_public_key  = COINPAYMENTS_PUBLIC_KEY;
			$coinpayments_private_key = COINPAYMENTS_PRIVATE_KEY;
			$coinpayments_merchant_id = COINPAYMENTS_MERCHANT_ID;
			$coinpayments_currency    = COINPAYMENTS_CURRENCY;
			$coinpayments_ipn_mode    = COINPAYMENTS_IPN_MODE;
			$coinpayments_ipn_secret  = COINPAYMENTS_IPN_SECRET;
			$coinpayments_ipn_url     = COINPAYMENTS_IPN_URL;
			$coinpayments_success_url = COINPAYMENTS_SUCCESS_URL;
			$coinpayments_cancel_url  = COINPAYMENTS_CANCEL_URL;
			$coinpayments_button      = COINPAYMENTS_BUTTON;
		}

		if ( empty( $coinpayments_ipn_url ) ) {
			$coinpayments_ipn_url = BASE_HTTP_PATH . 'payment/coinpayments.php';
		}

		if ( empty( $coinpayments_success_url ) ) {
			$coinpayments_success_url = BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className;
		}

		if ( empty( $coinpayments_cancel_url ) ) {
			$coinpayments_cancel_url = BASE_HTTP_PATH . "users/";
		}

		?>
        <p>
            <a href="https://www.coinpayments.net/index.php?ref=4ec026c11e3c5d30856e114fb8181aaf" target="_blank">Sign up for a CoinPayments account here</a>
        </p>
        <p>Go to the
            <a href="https://www.coinpayments.net/index.php?cmd=acct_api_keys" target="_blank">API Keys</a> page and generate an API key. Enter the private and public key below. Don't share your private key with anyone!
        </p>
		<?php /*<p>Then click the 'Edit Permissions' button and add the following permissions: get_basic_info, create_transaction, get_tx_info, get_callback_address, rates, balances, get_deposit_address, create_transfer</p>*/ ?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove;font-family: Verdana,sans-serif; font-size: small;" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Public Key:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_public_key" size="33" value="<?php echo $coinpayments_public_key; ?>">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Private Key:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_private_key" size="33" value="<?php echo $coinpayments_private_key; ?>">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">Use CoinPayments Test Mode?</td>
                    <td bgcolor="#e6f2ea">
                        <select name="coinpayments_test_mode">
                            <option value="yes" <?php echo( ( $coinpayments_test_mode == 'yes' ) ? " selected " : "" ) ?>>Yes</option>
                            <option value="no" <?php echo( ( $coinpayments_test_mode == 'no' ) ? " selected " : "" ) ?>>No</option>
                        </select>
                        <p>Note: This is for testing and uses the LiteCoin Testnet (LTCT) wallet. See https://www.coinpayments.net/help-testnet
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Merchant ID:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_merchant_id" size="33" value="<?php echo $coinpayments_merchant_id; ?>">
                        <br/>Note: Can be found under Account > Account Settings or on this page: https://www.coinpayments.net/acct-settings
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Currency:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_currency" size="33" value="<?php echo $coinpayments_currency; ?>">
                        <br/>Note: CoinPayments will convert this with the currency the user chooses to pay in.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">IPN Verification Method</td>
                    <td bgcolor="#e6f2ea">
                        <select name="coinpayments_ipn_mode">
                            <option value="HTTP Auth" <?php echo( ( $coinpayments_ipn_mode == 'HTTP Auth' ) ? " selected " : "" ) ?>>HTTP Auth</option>
                            <option value="HMAC" <?php echo( ( $coinpayments_ipn_mode == 'HMAC' ) ? " selected " : "" ) ?>>HMAC</option>
                        </select>
                        <br/>Note: Can be found under Account > Account Settings > Merchant Settings tab.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments IPN Secret</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_ipn_secret" size="50" value="<?php echo $coinpayments_ipn_secret; ?>">
                        <br/>Note: Can be found under Account > Account Settings > Merchant Settings tab.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments IPN URL</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_ipn_url" size="50" value="<?php echo $coinpayments_ipn_url; ?>">
                        <br>(recommended: <b><?php echo $coinpayments_ipn_url; ?></b>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Success URL</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_success_url" size="50" value="<?php echo $coinpayments_success_url; ?>">
                        <br>(recommended: <b><?php echo $coinpayments_success_url ?></b>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Cancel URL</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="coinpayments_cancel_url" size="50" value="<?php echo $coinpayments_cancel_url; ?>">
                        <br>(eg. <?php echo $coinpayments_cancel_url; ?>)
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">CoinPayments Button</td>
                    <td bgcolor="#e6f2ea">
                        <select name="coinpayments_button">
							<?php
							$buttons = array(
								'https://www.coinpayments.net/images/pub/buynow-grey.png',
								'https://www.coinpayments.net/images/pub/buynow-med-grey.png',
								'https://www.coinpayments.net/images/pub/CP-third-large.png',
								'https://www.coinpayments.net/images/pub/CP-third-med.png',
								'https://www.coinpayments.net/images/pub/CP-main-large.png',
								'https://www.coinpayments.net/images/pub/CP-main-medium.png',
								'https://www.coinpayments.net/images/pub/buynow.png',
								'https://www.coinpayments.net/images/pub/buynow-med.png',
								'https://www.coinpayments.net/images/pub/buynow-white.png',
								'https://www.coinpayments.net/images/pub/buynow-med-white.png',
								'https://www.coinpayments.net/images/pub/buynow-ani.png',
								'https://www.coinpayments.net/images/pub/buynow-blue.png',
								'https://www.coinpayments.net/images/pub/buynow-ani-2.png',
								'https://www.coinpayments.net/images/pub/buynow-wide-yellow.png',
								'https://www.coinpayments.net/images/pub/buynow-wide-blue.png',
								'https://www.coinpayments.net/images/pub/buynow-small-white.png'
							);
							$b       = 0;
							foreach ( $buttons as $button ) {
								echo '<option value="' . $b . '" ' . ( ( $coinpayments_button == $b ) ? " selected " : "" ) . '>' . $button . '</option>';
								$b ++;
							}
							?>
                        </select>
                        <br/>Button preview:
                        <br/><img src="<?php echo $buttons[ $coinpayments_button ]; ?>"/>
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
		$sql = "REPLACE INTO config (`key`, val) VALUES ('COINPAYMENTS_TEST_MODE', '" . $_REQUEST['coinpayments_test_mode'] . "'),('COINPAYMENTS_PUBLIC_KEY', '" . $_REQUEST['coinpayments_public_key'] . "'),('COINPAYMENTS_PRIVATE_KEY', '" . $_REQUEST['coinpayments_private_key'] . "'),('COINPAYMENTS_MERCHANT_ID', '" . $_REQUEST['coinpayments_merchant_id'] . "'),('COINPAYMENTS_CURRENCY', '" . $_REQUEST['coinpayments_currency'] . "'),('COINPAYMENTS_IPN_MODE', '" . $_REQUEST['coinpayments_ipn_mode'] . "'),('COINPAYMENTS_IPN_SECRET', '" . $_REQUEST['coinpayments_ipn_secret'] . "'),('COINPAYMENTS_IPN_URL', '" . $_REQUEST['coinpayments_ipn_url'] . "'),('COINPAYMENTS_SUCCESS_URL', '" . $_REQUEST['coinpayments_success_url'] . "'),('COINPAYMENTS_CANCEL_URL', '" . $_REQUEST['coinpayments_cancel_url'] . "'),('COINPAYMENTS_BUTTON', '" . $_REQUEST['coinpayments_button'] . "')";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	// true or false
	function is_enabled() {
		$sql = "SELECT val FROM config WHERE `key`='COINPAYMENTS_ENABLED' ";
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
		$sql = "SELECT val FROM config WHERE `key`='COINPAYMENTS_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		if ( mysqli_num_rows( $result ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	function enable() {
		$sql = "UPDATE config SET val='Y' WHERE `key`='COINPAYMENTS_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function disable() {
		$sql = "UPDATE config SET val='N' WHERE `key`='COINPAYMENTS_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function process_payment_return() {

	}

}

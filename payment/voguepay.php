<?php

require_once "../config.php";

// https://voguepay.com/developers

$_PAYMENT_OBJECTS['VoguePay'] = new VoguePay;

function voguepay_mail_error( $msg ) {
	$date = date( "D, j M Y H:i:s O" );

	$headers = "From: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Reply-To: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "Return-Path: " . SITE_CONTACT_EMAIL . "\r\n";
	$headers .= "X-Mailer: PHP" . "\r\n";
	$headers .= "Date: $date" . "\r\n";
	$headers .= "X-Sender-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";

	$entry_line = "(VoguePay error detected) $msg\r\n ";
	$log_fp     = @fopen( "logs.txt", "a" );
	fputs( $log_fp, $entry_line );
	fclose( $log_fp );

	mail( SITE_CONTACT_EMAIL, "Error message from " . SITE_NAME . " VoguePay script. ", $msg, $headers );
}

#####################################################################################

if ( isset( $_POST['transaction_id'] ) && $_POST['transaction_id'] != '' ) {
	//get the full transaction details as an json from voguepay
	$url = 'https://voguepay.com/?v_transaction_id=' . $_POST['transaction_id'] . '&type=json';

	if ( VOGUEPAY_DEMO_MODE == "yes" ) {
		$url .= "&demo=true";
	}

	$json = file_get_contents( $url );

	//create new array to store our transaction detail
	$transaction = json_decode( $json, true );

	// Now we have the following keys in our $transaction array
	$transaction['merchant_id']                = filter_var( $transaction['merchant_id'], FILTER_SANITIZE_STRING );
	$transaction['transaction_id']             = filter_var( $transaction['transaction_id'], FILTER_VALIDATE_INT );
	$transaction['email']                      = filter_var( $transaction['email'], FILTER_VALIDATE_EMAIL );
	$transaction['total']                      = filter_var( $transaction['total'], FILTER_VALIDATE_FLOAT );
	$transaction['total_paid_by_buyer']        = filter_var( $transaction['total_paid_by_buyer'], FILTER_VALIDATE_FLOAT );
	$transaction['total_credited_to_merchant'] = filter_var( $transaction['total_credited_to_merchant'], FILTER_VALIDATE_FLOAT );
	$transaction['extra_charges_by_merchant']  = filter_var( $transaction['extra_charges_by_merchant'], FILTER_VALIDATE_FLOAT );
	$transaction['merchant_ref']               = filter_var( $transaction['merchant_ref'], FILTER_SANITIZE_STRING );
	$transaction['memo']                       = filter_var( $transaction['memo'], FILTER_SANITIZE_STRING );
	$transaction['status']                     = filter_var( $transaction['status'], FILTER_SANITIZE_STRING );
	$transaction['date']                       = filter_var( $transaction['date'], FILTER_SANITIZE_STRING );
	$transaction['referrer']                   = filter_var( $transaction['referrer'], FILTER_SANITIZE_STRING );
	$transaction['method']                     = filter_var( $transaction['method'], FILTER_SANITIZE_STRING );
	$transaction['fund_maturity']              = filter_var( $transaction['fund_maturity'], FILTER_SANITIZE_STRING );
	$transaction['cur']                        = filter_var( $transaction['cur'], FILTER_SANITIZE_STRING );

	/*
	Example JSON response:
	{"merchant_id":"qa331322179752","transaction_id":"11111","email":"mii@mydomain.com","total":500,"total_paid_by_buyer":"507.61","total_credited_to_merchant":"495.00","extra_charges_by_merchant":"0.00","merchant_ref":"2f093e72","memo":"1000 SMS units at &amp;#8358;1.20 each on www.bulksms.com","status":"Approved","date":"2012-01-09 18:56:23","referrer":"http://www.afrisoft.net/viewinvoice.php?id=2012","method":"Interswitch","fund_maturity":"2012-01-11","cur":"USD"}
	*/

	// validate transaction
	if ( $transaction['total'] == 0 ) {
		die( 'Invalid total' );
	}
	if ( $transaction['status'] != 'Approved' ) {
		die( 'Failed transaction' );
	}
	if ( $transaction['merchant_id'] != VOGUEPAY_MERCHANT_ID ) {
		die( 'Invalid merchant' );
	}

	/*You can do anything you want now with the transaction details or the merchant reference.
	You should query your database with the merchant reference and fetch the records you saved for this transaction.
	Then you should compare the $transaction['total'] with the total from your database.*/

	$sql = "SELECT * FROM orders WHERE order_id='" . intval($transaction['transaction_id']) . "'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or voguepay_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );
	$row = mysqli_fetch_array( $result );

	complete_order( $row['user_id'], $transaction['merchant_ref'] );
	debit_transaction( $transaction['merchant_ref'], $transaction['total'], $transaction['cur'], $transaction['transaction_id'], $transaction['memo'], 'VoguePay' );
}

###########################################################################
# Payment Object

class VoguePay {

	var $name;
	var $description;
	var $className = "VoguePay";

	function __construct() {
		global $label;

		$this->name        = "VoguePay";
		$this->description = "Secure Payment Processor";

		if ( $this->is_installed() ) {
			$sql = "SELECT * FROM config WHERE `key` LIKE 'VOGUEPAY_%'";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

			while ( $row = mysqli_fetch_array( $result ) ) {
				define( $row['key'], $row['val'] );
			}
		}
	}

	function get_currency() {

		return VOGUEPAY_CURRENCY;
	}

	function install() {
		echo "Installing VoguePay...<br>";

		$voguepay_notify_url  = mysqli_real_escape_string( $GLOBALS['connection'], BASE_HTTP_PATH . $_SERVER['PHP_SELF']);
		$voguepay_success_url = mysqli_real_escape_string( $GLOBALS['connection'], BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className);
		$voguepay_fail_url    = mysqli_real_escape_string( $GLOBALS['connection'], BASE_HTTP_PATH . "users/");

		$sql = "REPLACE INTO config (`key`, val) VALUES 
                ('VOGUEPAY_ENABLED', 'N'),
                ('VOGUEPAY_DEMO_MODE', 'yes'),
                ('VOGUEPAY_STORE_ID', '1'),
                ('VOGUEPAY_MERCHANT_ID', 'demo'),
                ('VOGUEPAY_CURRENCY', 'USD'),
                ('VOGUEPAY_HTTPMODE', 'https'),
                ('VOGUEPAY_NOTIFY_URL', '$voguepay_notify_url'),
                ('VOGUEPAY_SUCCESS_URL', '$voguepay_success_url'),
                ('VOGUEPAY_FAIL_URL', '$voguepay_fail_url'),
                ('VOGUEPAY_BUTTON', '0')
                ";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function uninstall() {
		echo "Uninstall VoguePay...<br>";

		$sql = "DELETE FROM config WHERE `key` IN (
                'VOGUEPAY_ENABLED',
                'VOGUEPAY_DEMO_MODE',
                'VOGUEPAY_STORE_ID',
                'VOGUEPAY_MERCHANT_ID',
                'VOGUEPAY_CURRENCY',
                'VOGUEPAY_HTTPMODE',
                'VOGUEPAY_NOTIFY_URL',
                'VOGUEPAY_SUCCESS_URL',
                'VOGUEPAY_FAIL_URL',
                'VOGUEPAY_BUTTON'
                )";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	function payment_button( $order_id ) {
		global $label;

		$order_id = intval( $order_id );

		$sql = "SELECT * FROM orders WHERE order_id='" . intval($order_id) . "'";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$order = mysqli_fetch_array( $result );

		// TODO: if site only has http support then use email
		$notificationEmail = ( VOGUEPAY_HTTPMODE == "http" ) ? SITE_CONTACT_EMAIL : "";

		$buttons = array(
			'https://voguepay.com/images/buttons/buynow_blue.png',
			'https://voguepay.com/images/buttons/buynow_red.png',
			'https://voguepay.com/images/buttons/buynow_green.png',
			'https://voguepay.com/images/buttons/buynow_grey.png',
			'https://voguepay.com/images/buttons/addtocart_blue.png',
			'https://voguepay.com/images/buttons/addtocart_red.png',
			'https://voguepay.com/images/buttons/addtocart_green.png',
			'https://voguepay.com/images/buttons/addtocart_grey.png',
			'https://voguepay.com/images/buttons/checkout_blue.png',
			'https://voguepay.com/images/buttons/checkout_red.png',
			'https://voguepay.com/images/buttons/checkout_green.png',
			'https://voguepay.com/images/buttons/checkout_grey.png',
			'https://voguepay.com/images/buttons/donate_blue.png',
			'https://voguepay.com/images/buttons/donate_red.png',
			'https://voguepay.com/images/buttons/donate_green.png',
			'https://voguepay.com/images/buttons/donate_grey.png',
			'https://voguepay.com/images/buttons/subscribe_blue.png',
			'https://voguepay.com/images/buttons/subscribe_red.png',
			'https://voguepay.com/images/buttons/subscribe_green.png',
			'https://voguepay.com/images/buttons/subscribe_grey.png',
			'https://voguepay.com/images/buttons/make_payment_blue.png',
			'https://voguepay.com/images/buttons/make_payment_red.png',
			'https://voguepay.com/images/buttons/make_payment_green.png',
			'https://voguepay.com/images/buttons/make_payment_grey.png'
		);

		?>
        <form method='POST' action='https://voguepay.com/pay/'>

            <input type='hidden' name='v_merchant_id' value='<?php echo VOGUEPAY_MERCHANT_ID; ?>'/>
            <input type='hidden' name='merchant_ref' value='<?php echo $order_id; ?>'/>
            <input type='hidden' name='memo' value='<?php echo SITE_NAME . " - " . $label['advertiser_ord_order_id'] . " " . $order_id; ?>'/>

            <input type='hidden' name='notify_url' value='<?php echo VOGUEPAY_NOTIFY_URL; ?>'/>
            <input type='hidden' name='success_url' value='<?php echo VOGUEPAY_SUCCESS_URL; ?>'/>
            <input type='hidden' name='fail_url' value='<?php echo VOGUEPAY_FAIL_URL; ?>'/>

            <input type='hidden' name='developer_code' value=''/>
            <input type='hidden' name='store_id' value='<?php echo VOGUEPAY_STORE_ID; ?>'/>

            <input type='hidden' name='total' value='<?php echo $order['price']; ?>'/>

            <input type='image' src='<?php echo $buttons[ VOGUEPAY_BUTTON ]; ?>' alt='Submit'/>

        </form>
		<?php
	}

	function config_form() {
		if ( $_REQUEST['action'] == 'save' ) {
			$voguepay_demo_mode   = filter_var( $_REQUEST['voguepay_demo_mode'], FILTER_SANITIZE_STRING );
			$voguepay_merchant_id = filter_var( $_REQUEST['voguepay_merchant_id'], FILTER_SANITIZE_STRING );
			$voguepay_store_id    = filter_var( $_REQUEST['voguepay_store_id'], FILTER_SANITIZE_STRING );
			$voguepay_currency    = filter_var( $_REQUEST['voguepay_currency'], FILTER_SANITIZE_STRING );
			$voguepay_httpmode    = filter_var( $_REQUEST['voguepay_httpmode'], FILTER_SANITIZE_STRING );
			$voguepay_notify_url  = filter_var( $_REQUEST['voguepay_notify_url'], FILTER_SANITIZE_URL );
			$voguepay_success_url = filter_var( $_REQUEST['voguepay_success_url'], FILTER_SANITIZE_URL );
			$voguepay_fail_url    = filter_var( $_REQUEST['voguepay_fail_url'], FILTER_SANITIZE_URL );
			$voguepay_button      = filter_var( $_REQUEST['voguepay_button'], FILTER_VALIDATE_INT );
		} else {
			$voguepay_demo_mode   = VOGUEPAY_DEMO_MODE;
			$voguepay_store_id    = VOGUEPAY_STORE_ID;
			$voguepay_merchant_id = VOGUEPAY_MERCHANT_ID;
			$voguepay_currency    = VOGUEPAY_CURRENCY;
			$voguepay_httpmode    = VOGUEPAY_HTTPMODE;
			$voguepay_notify_url  = VOGUEPAY_NOTIFY_URL;
			$voguepay_success_url = VOGUEPAY_SUCCESS_URL;
			$voguepay_fail_url    = VOGUEPAY_FAIL_URL;
			$voguepay_button      = VOGUEPAY_BUTTON;
		}

		if ( empty( $voguepay_notify_url ) ) {
			$voguepay_notify_url = BASE_HTTP_PATH . $_SERVER['PHP_SELF'];
		}

		if ( empty( $voguepay_success_url ) ) {
			$voguepay_success_url = BASE_HTTP_PATH . "users/thanks.php?m=" . $this->className;
		}

		if ( empty( $voguepay_fail_url ) ) {
			$voguepay_fail_url = BASE_HTTP_PATH . "users/";
		}

		?>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
                <tr>
                    <td bgcolor="#e6f2ea">Use VoguePay Demo mode ?</td>
                    <td bgcolor="#e6f2ea">
                        <select name="voguepay_demo_mode">
                            <option value="yes" <?php echo( ( $voguepay_demo_mode == 'yes' ) ? " selected " : "" ) ?>>Yes</option>
                            <option value="no" <?php echo( ( $voguepay_demo_mode == 'no' ) ? " selected " : "" ) ?>>No</option>
                        </select>
                        <p>Note: This is for testing. See https://voguepay.com/developers
                        <p>
                        <h4 class="th curved">Test/Demo Accounts</h4>
                        While integrating VoguePay, you may need a test account. We have provided a simple solution to test your integration.<br><br>
                        Use <strong>demo</strong> as your <strong>merchant ID</strong> in test environment.<br><br>
                        Once "<strong>demo</strong>" is used as your
                        <strong>merchant ID</strong>, you can use any email and password to make payment.<br><br>
                        To simulate a <strong>Failed transaction</strong>, use
                        <strong>failed@anydomain.com</strong> with any password to pay for the transaction e.g:
                        <strong>failed@ivoryserver.com</strong> or <strong>failed@trashmail.com</strong>.<br><br>
                        To simulate a successful transaction, use any email and any password to pay for the transaction. You may use your real email since a notification will be sent to the email address you use for the transaction.<br><br>
                        The transaction ID will be sent to the notify_url parameter submitted by your form e.g:
                        <br><span class="red"><strong>&lt;input type="hidden" name="notify_url" value="http://www.mydomain.com/notification.php" /&gt;</strong></span><br>
                        You may then call the notification/order processing API from there.<br>
                        For demo transactions, use add demo=true to the notification API as shown below:<br>
                        <b>https://voguepay.com/?v_transaction_id=11111&amp;type=xml&amp;demo=true</b>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">VoguePay Merchant ID:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="voguepay_merchant_id" size="33" value="<?php echo $voguepay_merchant_id; ?>">
                        <br/>Note: Can be found on the top right hand side after you login. https://voguepay.com/
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">VoguePay Store ID:</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" name="voguepay_store_id" size="33" value="<?php echo $voguepay_store_id; ?>">
                        <br/>Note: A unique store identifier which identifies a particular store a transaction was made.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">VoguePay Currency</td>
                    <td bgcolor="#e6f2ea">
                        <input type="text" value="<?php echo $voguepay_currency; ?>" name="voguepay_currency">
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">Is your site accessible by https:// ?</td>
                    <td bgcolor="#e6f2ea">
                        <select name="voguepay_httpmode">
                            <option value="https" <?php echo( ( $voguepay_httpmode == 'https' ) ? " selected " : "" ) ?>>Yes</option>
                            <option value="http" <?php echo( ( $voguepay_httpmode == 'http' ) ? " selected " : "" ) ?>>No</option>
                        </select>
                        <br/>Note: If your site is not accessible by https:// then you must manually verify orders. If you set this to No then when orders are confirmed you will receive emails from VoguePay.com to the email address configured as your Site Contact Email in the MDS configuration.
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,sans-serif; font-size: xx-small; ">VoguePay Notify URL</span>
                    </td>
                    <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
                            <input type="text" name="voguepay_notify_url" size="50" value="<?php echo $voguepay_notify_url; ?>"><br>(recommended: <b><?php echo $voguepay_notify_url; ?></b></span>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,sans-serif; font-size: xx-small; ">VoguePay Success URL</span>
                    </td>
                    <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
                            <input type="text" name="voguepay_success_url" size="50" value="<?php echo $voguepay_success_url; ?>"><br>(recommended: <b><?php echo $voguepay_success_url ?></b></span>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">
                        <span style="font-family: Verdana,sans-serif; font-size: xx-small; ">VoguePay Fail URL</span>
                    </td>
                    <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
                            <input type="text" name="voguepay_fail_url" size="50" value="<?php echo $voguepay_fail_url; ?>"><br>(eg. <?php echo $voguepay_fail_url; ?>)</span>
                    </td>
                </tr>
                <tr>
                    <td bgcolor="#e6f2ea">VoguePay Button</td>
                    <td bgcolor="#e6f2ea">
                        <select name="voguepay_button">
							<?php
							$buttons = array(
								'https://voguepay.com/images/buttons/buynow_blue.png',
								'https://voguepay.com/images/buttons/buynow_red.png',
								'https://voguepay.com/images/buttons/buynow_green.png',
								'https://voguepay.com/images/buttons/buynow_grey.png',
								'https://voguepay.com/images/buttons/addtocart_blue.png',
								'https://voguepay.com/images/buttons/addtocart_red.png',
								'https://voguepay.com/images/buttons/addtocart_green.png',
								'https://voguepay.com/images/buttons/addtocart_grey.png',
								'https://voguepay.com/images/buttons/checkout_blue.png',
								'https://voguepay.com/images/buttons/checkout_red.png',
								'https://voguepay.com/images/buttons/checkout_green.png',
								'https://voguepay.com/images/buttons/checkout_grey.png',
								'https://voguepay.com/images/buttons/donate_blue.png',
								'https://voguepay.com/images/buttons/donate_red.png',
								'https://voguepay.com/images/buttons/donate_green.png',
								'https://voguepay.com/images/buttons/donate_grey.png',
								'https://voguepay.com/images/buttons/subscribe_blue.png',
								'https://voguepay.com/images/buttons/subscribe_red.png',
								'https://voguepay.com/images/buttons/subscribe_green.png',
								'https://voguepay.com/images/buttons/subscribe_grey.png',
								'https://voguepay.com/images/buttons/make_payment_blue.png',
								'https://voguepay.com/images/buttons/make_payment_red.png',
								'https://voguepay.com/images/buttons/make_payment_green.png',
								'https://voguepay.com/images/buttons/make_payment_grey.png'
							);
							$b       = 0;
							foreach ( $buttons as $button ) {
								echo '<option value="' . $b . '" ' . ( ( $voguepay_button == $b ) ? " selected " : "" ) . '>' . $button . '</option>';
								$b ++;
							}
							?>
                        </select>
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
		$sql = "REPLACE INTO config (`key`, val) VALUES ('VOGUEPAY_DEMO_MODE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_demo_mode']) . "'),('VOGUEPAY_STORE_ID', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_store_id']) . "'),('VOGUEPAY_MERCHANT_ID', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_merchant_id']) . "'),('VOGUEPAY_CURRENCY', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_currency']) . "'),('VOGUEPAY_HTTPMODE', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_httpmode']) . "'),('VOGUEPAY_NOTIFY_URL', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_notify_url']) . "'),('VOGUEPAY_SUCCESS_URL', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_success_url']) . "'),('VOGUEPAY_FAIL_URL', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_fail_url']) . "'),('VOGUEPAY_BUTTON', '" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voguepay_button']) . "')";
		mysqli_query( $GLOBALS['connection'], $sql );
	}

	// true or false
	function is_enabled() {
		$sql = "SELECT val FROM config WHERE `key`='VOGUEPAY_ENABLED' ";
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
		$sql = "SELECT val FROM config WHERE `key`='VOGUEPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
		if ( mysqli_num_rows( $result ) > 0 ) {
			return true;
		} else {
			return false;
		}
	}

	function enable() {
		$sql = "UPDATE config SET val='Y' WHERE `key`='VOGUEPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function disable() {
		$sql = "UPDATE config SET val='N' WHERE `key`='VOGUEPAY_ENABLED' ";
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

	function process_payment_return() {

	}

}

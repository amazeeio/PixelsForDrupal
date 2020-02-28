<?php
/**
 * @package		mds
 * @copyright	(C) Copyright 2020 Ryan Rhode, All rights reserved.
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
 * 		https://milliondollarscript.com/
 *
 */


function q_mail_error($s) {

	mail(SITE_CONTACT_EMAIL, SITE_NAME.'email q error', $s."\n");


}

#################################################

function queue_mail($to_address, $to_name, $from_address, $from_name, $subject, $message, $html_message, $template_id, $att=false) {

	$to_address=trim($to_address);
	$to_name=trim($to_name);
	$from_address=trim($from_address);
	$from_name=trim($from_name);
	$subject=trim($subject);
	$message=trim($message);
	$html_message=trim($html_message);

	
	$attachments='N';
	
	$now = (gmdate("Y-m-d H:i:s"));


	$sql = "INSERT INTO mail_queue (mail_date, to_address, to_name, from_address, from_name, subject, message, html_message, attachments, status, error_msg, retry_count, template_id, date_stamp) VALUES('$now', '".mysqli_real_escape_string( $GLOBALS['connection'], $to_address)."', '".mysqli_real_escape_string( $GLOBALS['connection'], $to_name)."', '".mysqli_real_escape_string( $GLOBALS['connection'], $from_address)."', '".mysqli_real_escape_string( $GLOBALS['connection'], $from_name)."', '".mysqli_real_escape_string( $GLOBALS['connection'], $subject)."', '".mysqli_real_escape_string( $GLOBALS['connection'], $message)."', '".mysqli_real_escape_string( $GLOBALS['connection'], $html_message)."', '$attachments', 'queued', '', 0, '".intval($template_id)."', '$now')"; // 2006 copyr1ght jam1t softwar3

	mysqli_query($GLOBALS['connection'], $sql) or q_mail_error (mysqli_error($GLOBALS['connection']).$sql);

	$mail_id = mysqli_insert_id($GLOBALS['connection']);

	return $mail_id;

}

############################
function process_mail_queue($send_count=1) {

	$now = (gmdate("Y-m-d H:i:s"));
	$unix_time = time();

	// get the time of last run
	$sql = "SELECT * FROM `config` where `key` = 'LAST_MAIL_QUEUE_RUN' ";
	$result = @mysqli_query($GLOBALS['connection'], $sql) or $DB_ERROR = mysqli_error($GLOBALS['connection']);
	$t_row = @mysqli_fetch_array($result);

	if ($DB_ERROR!='') return $DB_ERROR;

	// Poor man's lock (making sure that this function is a Singleton)
	$sql = "UPDATE `config` SET `val`='YES' WHERE `key`='MAIL_QUEUE_RUNNING' AND `val`='NO' ";
	$result = @mysqli_query($GLOBALS['connection'], $sql) or $DB_ERROR = mysqli_error($GLOBALS['connection']);
	if (@mysqli_affected_rows($GLOBALS['connection'])==0) {

		// make sure it cannot be locked for more than 30 secs 
		// This is in case the proccess fails inside the lock
		// and does not release it.

		if ($unix_time > $t_row['val']+30) {
			// release the lock
			
			$sql = "UPDATE `config` SET `val`='NO' WHERE `key`='MAIL_QUEUE_RUNNING' ";
			$result = @mysqli_query($GLOBALS['connection'], $sql) or $DB_ERROR = mysqli_error($GLOBALS['connection']);

			// update timestamp
			$sql = "REPLACE INTO config (`key`, `val`) VALUES ('LAST_MAIL_QUEUE_RUN', '$unix_time')  ";
			$result = @mysqli_query($GLOBALS['connection'], $sql) or $DB_ERROR = mysqli_error($GLOBALS['connection']);
		}


		return; // this function is already executing in another process.
	}



	if ($unix_time > $t_row['val']+5) { // did 5 seconds elapse since last run?

		$and_mail_id = "";
		if (func_num_args()>1) {
			$mail_id = func_get_arg(1);

			$and_mail_id = " AND mail_id=".intval($mail_id)." ";

		}

		$EMAILS_MAX_RETRY = EMAILS_MAX_RETRY;
		if ($EMAILS_MAX_RETRY=='') {
			$EMAILS_MAX_RETRY = 5;
		}

		$EMAILS_ERROR_WAIT = EMAILS_ERROR_WAIT;
		if ($EMAILS_ERROR_WAIT=='') {
			$EMAILS_ERROR_WAIT = 10;
		}

		$sql = "SELECT * from mail_queue where (status='queued' OR status='error') AND retry_count <= ".intval($EMAILS_MAX_RETRY)." $and_mail_id order by mail_date DESC";
		$result = mysqli_query($GLOBALS['connection'], $sql) or q_mail_error (mysqli_error($GLOBALS['connection']).$sql);
		while (($row = mysqli_fetch_array($result))&&($send_count > 0)) {
			$time_stamp = strtotime($row['date_stamp']);
			$now = strtotime(gmdate("Y-m-d H:i:s"));
			$wait = $EMAILS_ERROR_WAIT * 60;
			//echo "(($now - $wait) > $time_stamp) status:".$row['status']."\n";
			if (((($now - $wait) > $time_stamp) && ($row['status']=='error')) || ($row['status']=='queued')) {
				$send_count--;
				if ( defined( "EMAIL_DEBUG" ) && EMAIL_DEBUG == 'YES' ) {
					echo "Sending mail: " . print_r( $row, true ) . "<br>";
				}

				if ( USE_SMTP == 'YES' ) {
					$error = send_smtp_email( $row );
				} else {

					$sql    = "SELECT * FROM mail_queue WHERE mail_id=" . intval( $_REQUEST['mail_id'] );
					$result = mysqli_query( $GLOBALS['connection'], $sql );
					$row    = mysqli_fetch_array( $result );

					send_phpmail(array(
						'from_address' => $row['from_address'],
						'from_name' => $row['from_name'],
						'to_address' => $row['to_address'],
						'to_name' => $row['to_name'],
						'subject' => $row['subject'],
						'html_message' => $row['html_message'],
						'message' => $row['message'],
						'mail_id' => intval( $_REQUEST['mail_id'] ),

					));
				}
			}
		}

		
		// delete old stuff

		if ((EMAILS_DAYS_KEEP=='EMAILS_DAYS_KEEP')) { define (EMAILS_DAYS_KEEP, '0'); }

		if (EMAILS_DAYS_KEEP>0) {

			$now = (gmdate("Y-m-d H:i:s"));

			$sql = "SELECT mail_id, att1_name, att2_name, att3_name from mail_queue where status='sent' AND DATE_SUB('$now',INTERVAL ".intval(EMAILS_DAYS_KEEP)." DAY) >= date_stamp  ";

			$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));

			while ($row=mysqli_fetch_array($result)) {

				if ($row['att1_name']!='') {
					unlink($row['att1_name']);
				}

				if ($row['att2_name']!='') {
					unlink($row['att2_name']);
				}

				if ($row['att3_name']!='') {
					unlink($row['att3_name']);
				}

				$sql = "DELETE FROM mail_queue where mail_id='".intval($row['mail_id'])."' ";
				mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));
			}
		}
	}

	// release the poor man's lock
	$sql = "UPDATE `config` SET `val`='NO' WHERE `key`='MAIL_QUEUE_RUNNING' ";
	@mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));


}

############################

function send_smtp_email( $mail_row ) {

	$debug_level = 0;
	if ( defined( "EMAIL_DEBUG" ) && EMAIL_DEBUG == 'YES' ) {
		$debug_level = 2;
	}

	if ( EMAIL_POP_BEFORE_SMTP == 'YES' ) {
		$pop = POP3::popBeforeSmtp(
			EMAIL_POP_SERVER,
			POP3_PORT,
			30,
			EMAIL_SMTP_USER,
			EMAIL_SMTP_PASS,
			$debug_level
		);
	}

	$mail = new PHPMailer\PHPMailer\PHPMailer;

	$error = "";
	try {
		$mail->CharSet = "UTF-8";
		$mail->isSMTP();

		$mail->SMTPDebug   = $debug_level;
		$mail->Debugoutput = 'html';

		$mail->Host = EMAIL_SMTP_SERVER;
		$mail->Port = SMTP_PORT;

		if ( EMAIL_TLS == 1 ) {
			$mail->SMTPSecure = 'tls';
		} else {
			$mail->SMTPSecure = '';
		}

		if ( defined( "EMAIL_SMTP_USER" ) && EMAIL_SMTP_USER != "" ) {
			$mail->SMTPAuth = true;
			$mail->Username = EMAIL_SMTP_USER;
			$mail->Password = EMAIL_SMTP_PASS;
		}

		$mail->setFrom( $mail_row['from_address'], mds_specialchars_decode( $mail_row['from_name'] ) );
		$mail->addReplyTo( $mail_row['from_address'], mds_specialchars_decode( $mail_row['from_name'] ) );
		$mail->addAddress( $mail_row['to_address'], mds_specialchars_decode( $mail_row['to_name'] ) );
		$mail->Subject = mds_specialchars_decode( $mail_row['subject'] );

		$html = mds_specialchars_decode( $mail_row['html_message'] );
		$text = mds_specialchars_decode( $mail_row['message'] );
		if(!empty($html)) {
			$mail->msgHTML($html);
		} else {
			$mail->msgHTML(nl2br($text));
		}
		$mail->AltBody = $text;

		if ( ! $mail->send() ) {
			$error = $mail->ErrorInfo;
			if ( $debug_level > 0 ) {
				file_put_contents( __DIR__ . '/.maildebug.log', "Mailer Error: " . $error . "\n", FILE_APPEND );
			}
		} else {
			if ( $debug_level > 0 ) {
				file_put_contents( __DIR__ . '/.maildebug.log', "Message sent!" . "\n", FILE_APPEND );
			}
		}

	} catch ( PHPMailer\PHPMailer\Exception $e ) {
		$error = $e->errorMessage();
		if ( $debug_level > 0 ) {
			file_put_contents( __DIR__ . '/.maildebug.log', $e->errorMessage() . "\n", FILE_APPEND );
		}
	} catch ( Exception $e ) {
		$error = $e->getMessage();
		if ( $debug_level > 0 ) {
			file_put_contents( __DIR__ . '/.maildebug.log', $e->getMessage() . "\n", FILE_APPEND );
		}
	}

	if ( strcmp( $error, "" ) ) {
		$now = gmdate( "Y-m-d H:i:s" );

		$sql = "UPDATE mail_queue SET status='error', retry_count=retry_count+1,  error_msg='" . mysqli_real_escape_string( $GLOBALS['connection'], $error ) . "', `date_stamp`='$now' WHERE mail_id=" . intval($mail_row['mail_id']);
		//echo $sql;
		mysqli_query( $GLOBALS['connection'], $sql ) or q_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );

	} else {

		$now = gmdate( "Y-m-d H:i:s" );

		$sql = "UPDATE mail_queue SET status='sent', `date_stamp`='$now' WHERE mail_id=" . intval($mail_row['mail_id']);
		mysqli_query( $GLOBALS['connection'], $sql ) or q_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );

	}

	return $error;
}

############################

function send_phpmail( $mail_row ) {

	$debug_level = 0;
	if ( defined( "EMAIL_DEBUG" ) && EMAIL_DEBUG == 'YES' ) {
		$debug_level = 2;
	}

	$mail = new PHPMailer\PHPMailer\PHPMailer;

	$error = "";
	try {
		$mail->CharSet = "UTF-8";
		$mail->setFrom( $mail_row['from_address'], mds_specialchars_decode( $mail_row['from_name'] ) );
		$mail->addReplyTo( $mail_row['from_address'], mds_specialchars_decode( $mail_row['from_name'] ) );
		$mail->addAddress( $mail_row['to_address'], mds_specialchars_decode( $mail_row['to_name'] ) );
		$mail->Subject = mds_specialchars_decode( $mail_row['subject'] );

		$html = mds_specialchars_decode( $mail_row['html_message'] );
		$text = mds_specialchars_decode( $mail_row['message'] );
		if(!empty($html)) {
			$mail->msgHTML($html);
		} else {
			$mail->msgHTML(nl2br($text));
		}
		$mail->AltBody = $text;

		if ( ! $mail->send() ) {
			$error = $mail->ErrorInfo;
			if ( $debug_level > 0 ) {
				file_put_contents( __DIR__ . '/.maildebug.log', "Mailer Error: " . $error . "\n", FILE_APPEND );
			}
		} else {
			if ( $debug_level > 0 ) {
				file_put_contents( __DIR__ . '/.maildebug.log', "Message sent!" . "\n", FILE_APPEND );
			}
		}

	} catch ( PHPMailer\PHPMailer\Exception $e ) {
		$error = $e->errorMessage();
		if ( $debug_level > 0 ) {
			file_put_contents( __DIR__ . '/.maildebug.log', $e->errorMessage() . "\n", FILE_APPEND );
		}
	} catch ( Exception $e ) {
		$error = $e->getMessage();
		if ( $debug_level > 0 ) {
			file_put_contents( __DIR__ . '/.maildebug.log', $e->getMessage() . "\n", FILE_APPEND );
		}
	}

	if ( strcmp( $error, "" ) ) {
		$now = gmdate( "Y-m-d H:i:s" );

		$sql = "UPDATE mail_queue SET status='error', retry_count=retry_count+1,  error_msg='" . mysqli_real_escape_string( $GLOBALS['connection'], $error ) . "', `date_stamp`='$now' WHERE mail_id=" . intval($mail_row['mail_id']);
		//echo $sql;
		mysqli_query( $GLOBALS['connection'], $sql ) or q_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );

	} else {

		$now = gmdate( "Y-m-d H:i:s" );

		$sql = "UPDATE mail_queue SET status='sent', `date_stamp`='$now' WHERE mail_id=" . intval($mail_row['mail_id']);
		mysqli_query( $GLOBALS['connection'], $sql ) or q_mail_error( mysqli_error( $GLOBALS['connection'] ) . $sql );

	}

	return $error;
}

// From WordPress /wp-includes/formatting.php
/**
 * Converts a number of HTML entities into their special characters.
 *
 * Specifically deals with: &, <, >, ", and '.
 *
 * $quote_style can be set to ENT_COMPAT to decode " entities,
 * or ENT_QUOTES to do both " and '. Default is ENT_NOQUOTES where no quotes are decoded.
 *
 * @since 2.8.0
 *
 * @param string     $string The text which is to be decoded.
 * @param string|int $quote_style Optional. Converts double quotes if set to ENT_COMPAT,
 *                                both single and double if set to ENT_QUOTES or
 *                                none if set to ENT_NOQUOTES.
 *                                Also compatible with old _wp_specialchars() values;
 *                                converting single quotes if set to 'single',
 *                                double if set to 'double' or both if otherwise set.
 *                                Default is ENT_NOQUOTES.
 * @return string The decoded text without HTML entities.
 */
function mds_specialchars_decode( $string, $quote_style = ENT_NOQUOTES ) {
	$string = (string) $string;

	if ( 0 === strlen( $string ) ) {
		return '';
	}

	// Don't bother if there are no entities - saves a lot of processing
	if ( strpos( $string, '&' ) === false ) {
		return $string;
	}

	// Match the previous behaviour of _wp_specialchars() when the $quote_style is not an accepted value
	if ( empty( $quote_style ) ) {
		$quote_style = ENT_NOQUOTES;
	} elseif ( ! in_array( $quote_style, array( 0, 2, 3, 'single', 'double' ), true ) ) {
		$quote_style = ENT_QUOTES;
	}

	// More complete than get_html_translation_table( HTML_SPECIALCHARS )
	$single      = array(
		'&#039;' => '\'',
		'&#x27;' => '\'',
	);
	$single_preg = array(
		'/&#0*39;/'   => '&#039;',
		'/&#x0*27;/i' => '&#x27;',
	);
	$double      = array(
		'&quot;' => '"',
		'&#034;' => '"',
		'&#x22;' => '"',
	);
	$double_preg = array(
		'/&#0*34;/'   => '&#034;',
		'/&#x0*22;/i' => '&#x22;',
	);
	$others      = array(
		'&lt;'   => '<',
		'&#060;' => '<',
		'&gt;'   => '>',
		'&#062;' => '>',
		'&amp;'  => '&',
		'&#038;' => '&',
		'&#x26;' => '&',
	);
	$others_preg = array(
		'/&#0*60;/'   => '&#060;',
		'/&#0*62;/'   => '&#062;',
		'/&#0*38;/'   => '&#038;',
		'/&#x0*26;/i' => '&#x26;',
	);

	if ( $quote_style === ENT_QUOTES ) {
		$translation      = array_merge( $single, $double, $others );
		$translation_preg = array_merge( $single_preg, $double_preg, $others_preg );
	} elseif ( $quote_style === ENT_COMPAT || $quote_style === 'double' ) {
		$translation      = array_merge( $double, $others );
		$translation_preg = array_merge( $double_preg, $others_preg );
	} elseif ( $quote_style === 'single' ) {
		$translation      = array_merge( $single, $others );
		$translation_preg = array_merge( $single_preg, $others_preg );
	} elseif ( $quote_style === ENT_NOQUOTES ) {
		$translation      = $others;
		$translation_preg = $others_preg;
	}

	// Remove zero padding on numeric entities
	$string = preg_replace( array_keys( $translation_preg ), array_values( $translation_preg ), $string );

	// Replace characters according to translation table
	return strtr( $string, $translation );
}

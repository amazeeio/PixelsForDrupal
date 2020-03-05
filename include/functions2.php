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

class functions2 {

	function get_doc(){
		$doc = '<!DOCTYPE html>
<html>
<head>
	<title> ' . SITE_NAME . '</title>
	<meta name="Description" content="' . SITE_SLOGAN . '">
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>';
		return $doc;
	}

	/**
	 * check that banner id is numeric and if so return it
	 * otherwise return 1 (1 = default banner id)
	 */
	function bid($var) {
		if(isset($var)) {
			if(is_numeric($var) && $var > 0) {
				return $var;
			} elseif ($var == 'all') {
				return 'all';
			}
		}
		return "1";
	}

	/**
	 * filters variables
	 * string:	FILTER_SANITIZE_STRING
	 * int:		FILTER_VALIDATE_INT
	 * email:	FILTER_VALIDATE_EMAIL
	 * url:		FILTER_VALIDATE_URL
	 * FILTER_SANITIZE_URL
	 * Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
	 *
	 * Default:FILTER_SANITIZE_STRING
	 *
	 * example:
	 * $BID = $f2->filter($_REQUEST['BID'], "BID");
	 *
	 * more info: http://www.php.net/manual/en/filter.filters.php
	 */
	function filter($var, $filter=FILTER_SANITIZE_STRING){

		// check for BID filter
		if($filter == "BID") {
			if(isset($var)) {
				if(is_numeric($var) && $var > 0) {
					return $var;
				}
			}
			return "1";
		}

		// check for Y or N filter
		if($filter == "YN") {
			if(isset($var)) {
				if($var == strtoupper("Y") || $var == strtoupper("N")) {
					return $var;
				} else {
					echo("Invalid input");
					die();
				}
			}
		}

		// check if var is empty first
		if(empty($var)){return $var;}

		//echo $var . "<br />" . $filter . "<br />";

		// filter
		$var = filter_var($var, $filter);

		// if filter_var returns false error out
		if($var === false) {
			echo("Invalid input");
			die();
		}
		return $var;
	}

	/**
	 * Format for output.
	 *
	 * @param $value
	 * @param bool $stripslashes
	 *
	 * @return string
	 */
	function value( $value, $stripslashes = false ) {
		$value = htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );

		if ( $stripslashes ) {
			$value = stripslashes( $value );
		}

		return $value;
	}

	function write_log($text) {
		if(DEBUG===true) {
			$output_file = fopen( MDS_LOG_FILE, 'a' );
			fwrite( $output_file, $text . "\n" );
			fclose( $output_file );
		}
	}

	/** debug */
	function debug($line="null", $label="debug") {

		// Firebug console debug
		if(DEBUG===true) {
			echo "<script>console.log('".$label."[".$line."]');</script>";
		}

		// log file
		if (MDS_LOG===true && file_exists(MDS_LOG_FILE)) {
			$entry_line =  "[" . date('r') . "]	" . $line . "\r\n";
			$log_fp = fopen(MDS_LOG_FILE, "a");
			fputs($log_fp, $entry_line);
			fclose($log_fp);

		}

	}

	function nl2html( $input ) {
		return str_replace( array( "\n", "\r" ), array( '<br />', '' ), htmlspecialchars( $input ) );
	}

	function nl2htmlraw( $input ) {
		return str_replace( array( "\n", "\r" ), array( '<br />', '' ), $input );
	}

	function rmnl( $input ) {
		return str_replace( array( "\n", "\r" ), '', htmlspecialchars( $input ) );
	}

	function rmnlraw( $input ) {
		return str_replace( array( "\n", "\r" ), '', $input );
	}

}

function get_banner_dir() {
	if ( BANNER_DIR == 'BANNER_DIR' ) {

		$base = BASE_PATH;
		if ( empty(BASE_PATH) || $base == 'BASE_PATH' ) {
			$base = __DIR__;
		}
		$dest = $base . '/banners/';

		if ( file_exists( $dest ) ) {
			$BANNER_DIR = 'banners/';
		} else {
			$BANNER_DIR = 'pixels/';
		}
	} else {
		$BANNER_DIR = BANNER_DIR;
	}

	return $BANNER_DIR;

}

class MDSException extends Exception { }

<?php
/**
 * @version        $Id: code_functions.php 91 2011-01-03 22:47:15Z ryan $
 * @package        mds
 * @copyright    (C) Copyright 2010 Ryan Rhode, All rights reserved.
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
 *        http://www.milliondollarscript.com/
 *
 */

/**
 * @param $field_id
 */
function format_codes_translation_table( $field_id ) {
	global $AVAILABLE_LANGS;

	$field_id = intval( $field_id );

	$sql = "SELECT * FROM codes WHERE `field_id`=$field_id ";
	$f_result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( $sql . mysqli_error( $GLOBALS['connection'] ) );
	while ( $f_row = mysqli_fetch_array( $f_result ) ) {

		$code         = mysqli_real_escape_string( $GLOBALS['connection'], $f_row['code'] );
		$row_field_id = mysqli_real_escape_string( $GLOBALS['connection'], $f_row['field_id'] );
		$description  = mysqli_real_escape_string( $GLOBALS['connection'], $f_row['description'] );

		foreach ( $AVAILABLE_LANGS as $key => $val ) {
			$key = mysqli_real_escape_string( $GLOBALS['connection'], $key);

			$sql = "SELECT t2.code, t2.field_id, t2.description AS FLABEL, lang FROM codes_translations as t1, codes as t2 WHERE t2.code=t1.code AND t2.code='" . $code . "' AND t2.field_id=" . $row_field_id . " AND lang='$key' ";
			$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );
			if ( mysqli_num_rows( $result ) == 0 ) {
				$sql = "REPLACE INTO `codes_translations` (`field_id`, `code`, `lang`, `description`) VALUES ('" . mysqli_real_escape_string( $GLOBALS['connection'], $f_row['field_id']) . "', '" . mysqli_real_escape_string( $GLOBALS['connection'], $f_row['code']) . "', '" . $key . "', '" . $description . "')";
				mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) );
			}
		}
	}
}

#################################################
# Changes the code id, and updates *all* the records in the database
# with the given field id with the new code_id
function change_code_id( $field_id, $code, $new_code ) {
	$field_id = intval( $field_id );
	$code     = mysqli_real_escape_string( $GLOBALS['connection'], $code );
	$new_code = mysqli_real_escape_string( $GLOBALS['connection'], $new_code );

	// find which form the field_id is from

	$sql = "SELECT form_id FROM form_fields WHERE field_id='" . $field_id . "' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	$row     = mysqli_fetch_array( $result );
	$form_id = $row['form_id'];

	$sql = "UPDATE codes SET code='$new_code' where field_id='$field_id' and code='$code' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	$sql = "UPDATE codes_translations SET code='$new_code' where field_id='$field_id' and code='$code' ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );

	switch ( $form_id ) {
		case '1': // ads form
			$table = 'ads';
			$id    = 'ad_id';
			$sql   = "select ad_id as ID, `$field_id` FROM ads WHERE `$field_id` LIKE '%$code%' ";
			break;
	}

	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	while ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) {

		$new_codes = array();
		$codes     = explode( ',', $row[ $field_id ] );

		foreach ( $codes as $c ) {
			if ( $c == $code ) {
				$new_codes[] = $new_code;
			} else {
				$new_codes[] = $c;
			}
		}

		$codes = implode( ',', $new_codes );
		$codes = mysqli_real_escape_string( $GLOBALS['connection'], $codes );

		$sql = "UPDATE $table SET `$field_id`='" . $codes . "' WHERE $id = '" . intval( $row['ID'] ) . "' ";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}
}

######################################################################

function getCodeDescription( $field_id, $code ) {
	$field_id = intval( $field_id );
	$code     = mysqli_real_escape_string( $GLOBALS['connection'], $code );

	if ( get_lang() != '' ) {
		$sql = "SELECT `description` FROM `codes_translations` WHERE field_id='$field_id' AND `code` = '$code' and lang='" . get_lang() . "' ";
	} else {
		$sql = "SELECT `description` FROM `codes` WHERE field_id='$field_id' AND `code` = '$code'";
	}

	global $f2;
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );
	if ( $row = mysqli_fetch_array( $result ) ) {
		return $row['description'];
	}

	return "";
}

###################################################

function insert_code( $field_id, $code, $description ) {
	$field_id    = intval( $field_id );
	$code        = mysqli_real_escape_string( $GLOBALS['connection'], $code );
	$description = mysqli_real_escape_string( $GLOBALS['connection'], $description );

	$sql = "SELECT `code` FROM `codes` WHERE field_id='$field_id' AND `code` = '$code'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );

	if ( mysqli_num_rows( $result ) > 0 ) {
		echo '<font color="#FF0000">';
		echo "CANNOT INSERT a new Code: $code already exists in the database!<p>";
		echo '</font>';

		return;
	}

	$sql = "INSERT INTO `codes` ( `field_id` , `code` , `description` )  VALUES ('$field_id', '$code', '$description')";

	mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );

	if ( $_SESSION['MDS_LANG'] != '' ) {

		$sql = "INSERT INTO `codes_translations` ( `field_id` , `code` , `description`, `lang` )  VALUES ('$field_id', '$code', '$description', '" . get_lang() . "')";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );

	}

	format_codes_translation_table( $field_id );
}

################################################################
function modify_code( $field_id, $code, $description ) {
	$field_id    = intval( $field_id );
	$code        = mysqli_real_escape_string( $GLOBALS['connection'], $code );
	$description = mysqli_real_escape_string( $GLOBALS['connection'], $description );

	$sql = "UPDATE `codes` SET `description` = '$description' " .
	       "WHERE `field_id` = '$field_id' AND `code` = '$code'";
	mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );

	if ( get_lang() != '' ) {
		$sql = "UPDATE `codes_translations` SET `description` = '$description' " .
		       "WHERE `field_id` = '$field_id' AND `code` = '$code' AND `lang`='" . get_lang() . "' ";
		mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );
	}
}

#####################################################
/*
   This is the reverse of function getCodeDescription();
*/
function getCodeFromDescription( $field_id, $description ) {
	$field_id    = intval( $field_id );
	$description = mysqli_real_escape_string( $GLOBALS['connection'], $description );

	$sql = "SELECT `code` FROM `codes` WHERE field_id='$field_id' AND `description` = '$description'";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die( $sql . mysqli_error( $GLOBALS['connection'] ) );
	if ( $row = mysqli_fetch_array( $result ) ) {
		return $row['code'];
	}

	return "";
}

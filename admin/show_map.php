<?php
/**
 * @version        $Id: show_map.php 137 2011-04-18 19:48:11Z ryan $
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

define( 'NO_HOUSE_KEEP', 'YES' );

require( "../config.php" );

require( 'admin_common.php' );

$imagine = new Imagine\Gd\Imagine();

if ( $f2->bid( $_REQUEST['BID'] ) != '' ) {
	$BID = $f2->bid( $_REQUEST['BID'] );
} else {
	$BID = 1;

}

load_banner_constants( $BID );

$images = $blocks = array();

#
# Preload all block

if ( isset( $_REQUEST['user_id'] ) && $_REQUEST['user_id'] != '' ) {
	$sql = "select block_id, status, user_id, image_data FROM blocks where status='sold' AND user_id=" . $_REQUEST['user_id'] . " AND banner_id=$BID ";
} else {
	$sql = "select block_id, status, user_id, image_data FROM blocks where banner_id=$BID ";
}

$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
while ( $row = mysqli_fetch_array( $result ) ) {
	$blocks[ $row['block_id'] ] = $row['status'];

	if ( $row['image_data'] != '' ) {
		$images[ $row['block_id'] ] = $imagine->load( base64_decode( $row['image_data'] ) );
	}
}

$block = $imagine->load( GRID_BLOCK );

$selected_block = $imagine->load( USR_SEL_BLOCK );
$sold_block     = $imagine->load( USR_SOL_BLOCK );

$file_path = SERVER_PATH_TO_ADMIN;

// grid size
$size = new Imagine\Image\Box( G_WIDTH * BLK_WIDTH, G_HEIGHT * BLK_HEIGHT );

// create empty grid
$map = $imagine->create( $size );

// load block and resize it
$block = $imagine->load( GRID_BLOCK );
$block->resize( new Imagine\Image\Box( BLK_WIDTH, BLK_HEIGHT ) );

$selected_block = $imagine->load( USR_SEL_BLOCK );
$sold_block     = $imagine->load( USR_SOL_BLOCK );

// initialise the map, tile it with blocks
$cell = $x_pos = $y_pos = 0;
for ( $i = 0; $i < G_HEIGHT; $i ++ ) {
	for ( $j = 0; $j < G_WIDTH; $j ++ ) {

		if ( isset( $images[ $cell ] ) && $images[ $cell ] != '' ) {
			$map->paste( $images[ $cell ], new Imagine\Image\Point( $x_pos, $y_pos ) );

		} elseif ( isset( $blocks[ $cell ] ) && $blocks[ $cell ] != '' ) {
			switch ( $blocks[ $cell ] ) {
				case 'reserved':
					$map->paste( $selected_block, new Imagine\Image\Point( $x_pos, $y_pos ) );
					break;

				case 'sold':
				default:
					$map->paste( $sold_block, new Imagine\Image\Point( $x_pos, $y_pos ) );
					break;
			}

		} else {
			$map->paste( $block, new Imagine\Image\Point( $x_pos, $y_pos ) );
		}

		$cell ++;

		$x_pos += BLK_WIDTH;
	}
	$x_pos = 0;
	$y_pos += BLK_HEIGHT;

}

// show
if ( ( OUTPUT_JPEG == 'Y' ) && ( function_exists( "imagejpeg" ) ) ) {
	if ( INTERLACE_SWITCH == 'YES' ) {
		$map->interlace( Imagine\Image\ImageInterface::INTERLACE_LINE );
	}

	$map->show( "jpeg", array( 'jpeg_quality' => JPEG_QUALITY ) );

} elseif ( OUTPUT_JPEG == 'N' ) {

	if ( INTERLACE_SWITCH == 'YES' ) {
		$map->interlace( Imagine\Image\ImageInterface::INTERLACE_LINE );
	}

	$map->show( "png", array( 'png_compression_level' => 9 ) );

} elseif ( OUTPUT_JPEG == 'GIF' ) {

	if ( INTERLACE_SWITCH == 'YES' ) {
		$map->interlace( Imagine\Image\ImageInterface::INTERLACE_LINE );
	}

	$map->show( "gif" );
}

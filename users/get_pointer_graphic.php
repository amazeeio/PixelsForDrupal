<?php
/**
 * @version        $Id: get_pointer_graphic.php 137 2011-04-18 19:48:11Z ryan $
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

session_start();
define( 'NO_HOUSE_KEEP', 'YES' );

require( '../config.php' );

$imagine = new Imagine\Gd\Imagine();

if ( isset( $_REQUEST['BID'] ) && $f2->bid( $_REQUEST['BID'] ) != '' ) {
	$BID = $f2->bid( $_REQUEST['BID'] );
} else {
	$BID = 1;
}

load_banner_constants( $BID );

$imagine = new Imagine\Gd\Imagine();

$user_id  = $_SESSION['MDS_ID'];
$filename = get_tmp_img_name();
if ( file_exists( $filename ) ) {
	$image = $imagine->open( $filename );
} else {
	$image = $imagine->load( __DIR__ . '/pointer.png' );
}

// autorotate
$imagine->setMetadataReader( new \Imagine\Image\Metadata\ExifMetadataReader() );
$filter = new Imagine\Filter\Transformation();
$filter->add( new Imagine\Filter\Basic\Autorotate() );
$filter->apply( $image );

// image size
$box = $image->getSize();

// make it smaller
if ( MDS_RESIZE == 'YES' ) {
	$new_size = get_required_size( $box->getWidth(), $box->getHeight() );
	$resize   = new Imagine\Image\Box( $new_size[0], $new_size[1] );
	//$out->resize( $resize );
	$image->resize( $resize );
}

// show
$image->show( "png", array( 'png_compression_level' => 9 ) );

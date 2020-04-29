<?php
/**
 * @package        mds
 * @copyright    (C) Copyright 2020 Ryan Rhode, All rights reserved.
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

// set the root path
define( "MDSROOT", dirname( __FILE__ ) );

// include the config file
include_once( MDSROOT . "/config.php" );

// include the header
include_once( MDSROOT . "/html/header.php" );

global $label;
?>
<div class="container text-left">

<p><strong>What is this?</strong></p>
<p>You might remember the <a href='http://www.milliondollarhomepage.com/'>Million Dollar Homepage</a> from way back when. You could buy pixels and use them to post whatever you wanted - and the guy who started it made a million dollars. We thought it would be fun to make a Half Million Dollar homepage to help the Drupal Association reach their goal. Donors can purchase pixels to support the DA. You’ll get 100 pixels for every $5 you donate. You can post images and links to your pixels.  </p>

<p><strong>What do I need to do?</strong></p>
<p>Donate to #DrupalCares at <a href='https://www.drupal.org/association/donate'>https://www.drupal.org/association/donate</a>.</p>

<p>Create an account on <a href='https://pixelsfordrupal.com/'>pixelsfordrupal.com</a>.</p>

<p>You’ll receive a verification email. Log in and verify your account. </p>

<p>You can now upload your pixels. You don’t need to do this all at once. You can upload some now, some later - you can use the whole amount at once or divide it up - it’s all up to you! You can also donate more to increase the amount of pixels you can upload. After you upload your pixels, you’ll be asked for the voucher code that was sent to you. </p>

<p><strong>What can I upload?</strong></p>
<p>It’s up to you! Uploads and links are subject to the <a href='https://www.drupal.org/dcoc'>Drupal Code of Conduct</a>, so keep it professional and kind. Upload a picture of yourself, your pet, your company logo - be creative! We will have a moderation team quickly reviewing each submission.</p>

<p><strong>How do I get pixels?</strong></p>
<p>Go to <a href="https://www.drupal.org/association/donate">https://www.drupal.org/association/donate</a> and donate!</p>

<p><strong>Whose idea was this?</strong></p>
<p>The folks at <a href="https://amazee.io">amazee.io</a> came up with the idea and hosted it. The site is <a href="https://milliondollarscript.com/">based on this script</a>.</p>

<p><strong>Who built this?</strong></p>
<p>The Drupal community pitched in to build this - it was a group effort!</p>

<p><strong>What is #DrupalCares and why does the Drupal Association need support?</strong></p>
<p>The short version is that DrupalCon will not be held as usual, and it’s the DA’s main source of revenue for the year. You can <a href="https://www.drupal.org/association/blog/drupalcares-sustaining-the-da-through-the-covid-19-crisis">read more in their blog post</a>. </p>

<p><strong>I found a bug!</strong></p>
<p>Thanks for catching that! Please post an issue in <a href="https://github.com/amazeeio/PixelsForDrupal">our GitHub issue queue</a>.</p>
</div>
<?php
include_once( MDSROOT . "/html/footer.php" );
?>
<?php
/**
 * @version		$Id: config_form.php 154 2012-09-10 22:10:51Z ryan $
 * @package		mds
 * @copyright	(C) Copyright 2010 Ryan Rhode, All rights reserved.
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
 * 		http://www.milliondollarscript.com/
 *
 */
?>
<form method="POST" name="form1">
  <p><input type="submit" value="Save Configuration" name="save"></p>
  <input name="version_info" type="hidden" value="<?php echo VERSION_INFO; ?>" />
  <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr >
      <td colspan="2" bgcolor="#e6f2ea">
      <p ><span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>General Settings</b></span></td>
    </tr>
    <tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Site Name</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="site_name" size="49" value="<?php echo stripslashes(htmlentities(SITE_NAME)); ?>"/></span></td>
    </tr>
    <tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Site Slogan</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="site_slogan" size="49" value="<?php echo stripslashes(htmlentities(SITE_SLOGAN)); ?>"/></span></td>
    </tr>
    <tr>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Site Logo URL</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="site_logo_url" size="49" value="<?php echo stripslashes(htmlentities(SITE_LOGO_URL)); ?>"/><br>(http://www.example.com/images/logo.gif)</span></td>
    </tr>
    <tr>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Site Contact Email</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="site_contact_email" size="49" value="<?php echo stripslashes(htmlentities(SITE_CONTACT_EMAIL)); ?>"> (Please ensure that this email address has a POP account for extra email delivery reliability.)</span></td>
    </tr>
	<tr>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Admin Password</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="password" name="admin_password" size="49" value="<?php echo stripslashes(htmlentities(ADMIN_PASSWORD)); ?>"></span></td>
    </tr>
  </table>
  <?php 
  
  //print_r($_SERVER);

  $host = $_SERVER['SERVER_NAME']; // hostname
  $http_url = $_SERVER['PHP_SELF']; // eg /ojo/admin/edit_config.php
  $http_url = explode ("/", $http_url);
  array_pop($http_url); // get rid of filename
  array_pop($http_url); // get rid of /admin
  $http_url = implode ("/", $http_url);
 // echo "<b> $http_url </b>";
  $file_path = $_SERVER['SCRIPT_FILENAME']; // eg e:/apache/htdocs/ojo/admin/edit_config.php
  $file_path = explode ("/", $file_path);
  array_pop($file_path); // get rid of filename
  array_pop($file_path); // get rid of /admin
  $file_path = implode ("/", $file_path);
 // echo "<b> $file_path </b>";

  if ( ! defined( "BASE_PATH" ) ) {
	  define( "BASE_PATH", $file_path );
  }

  ?>
  <p>&nbsp;</p>
  <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2" bgcolor="#e6f2ea">
      <p ><span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>Paths and Locations</b><br></span></td>
    </tr>
    <tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Site's HTTP URL (address)</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="base_http_path" size="55" value="<?php echo htmlentities(BASE_HTTP_PATH); ?>"><br>Recommended: <b>http://<?php echo $host.$http_url."/"; ?></b></span></td>
    </tr>

      <tr>
          <td bgcolor="#e6f2ea">
              <span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Server Path to MDS Root Directory</span>
          </td>
          <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="base_path" size="55" value="<?php echo htmlentities( BASE_PATH ); ?>"><br>Recommended: <b><?php echo $file_path; ?></b></span>
          </td>
      </tr>

      <tr>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Server Path to Admin</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="server_path_to_admin" size="55" value="<?php echo htmlentities(SERVER_PATH_TO_ADMIN); ?>" ><br>Recommended: <b><?php echo str_replace('\\', '/', getcwd());?>/</b></span></td>
    </tr>
	<?php
	
	if (!defined('UPLOAD_PATH')) {
		define ('UPLOAD_PATH', $file_path."/upload_files/");

	}
	if (!defined('UPLOAD_HTTP_PATH')) {
		define ('UPLOAD_HTTP_PATH', "http://".$host.$http_url."/upload_files/");

	}

	
	
	?>
	<tr>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Path to upload directory</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="upload_path" size="55" value="<?php echo htmlentities(UPLOAD_PATH); ?>" ><br>Recommended: <b><?php echo str_replace('\\', '/', $file_path."/upload_files/");?></b></span></td>
    </tr>
	<tr>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">HTTP URL to upload directory</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="upload_http_path" size="55" value="<?php echo htmlentities(UPLOAD_HTTP_PATH); ?>" ><br>Recommended: <b>http://<?php echo str_replace('\\', '/', $host.$http_url."/upload_files/");?></b></span></td>
    </tr>
	<tr>
	<td colspan="2">
	<span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
NOTES<br>
 - Server Path to Admin is the full path to your admin directory, <span style="color: red; ">including a slash at the end</span><br>
 - The Site's HTTP URL must include a<span style="color: red; "> slash at the end</span><br>
 - Use the recommended settings unless you are sure otherwise<br>
 Also, don't forget to set the permissions of the admin/temp/ directory to 777.<br> The script must be able to write  to temp/ dir in the admin<br>
 The script also needs to be able to write to the pixels/ directory (chmod 777) <br>
 </span>
	</td>
	</tr>
  </table>
  <p>&nbsp;</p>
  <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2"  bgcolor="#e6f2ea">
      <span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>MySQL Settings</b></span></td>
    </tr>
    <tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">MySQL Database Username</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="mysql_user" size="29" value="<?php echo MYSQL_USER; ?>"></span></td>
    </tr>
	 <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">MySQL Database Password</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="password" name="mysql_pass" size="29" value="<?php echo MYSQL_PASS; ?>"></span></td>
    </tr>
    <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">MySQL Database Name</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="mysql_db" size="29" value="<?php echo MYSQL_DB; ?>"></span></td>
    </tr>
    <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">MySQL Server Hostname</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="mysql_host" size="29" value="<?php echo MYSQL_HOST; ?>"></span></td>
    </tr>
    <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">MySQL Server Port</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="mysql_port" size="29" value="<?php echo MYSQL_PORT; ?>"></span></td>
    </tr>
    <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">MySQL Server Socket (optional)</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="mysql_socket" size="29" value="<?php echo MYSQL_SOCKET; ?>"></span></td>
    </tr>
	<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "> 
       Use MySQL 'LOCK TABLES' feature?</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	   <input type="radio" name="use_lock_tables" size="49" value="Y" <?php if (USE_LOCK_TABLES=='Y') { echo " checked "; } ?> >Yes (recommended)<br><input type="radio" name="use_lock_tables" value="N" <?php if (USE_LOCK_TABLES!='Y') { echo " checked "; } ?> >No</span></td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <?php

	

	if (!defined('DATE_INPUT_SEQ')) {
		define ('DATE_INPUT_SEQ', 'YMD');
	}

	if (!defined('DATE_FORMAT')) {
		define ('DATE_FORMAT', 'Y-M-d');
	}

	if (!defined('GMT_DIF')) {
		define ('GMT_DIF', '10.00');
	}
	
	
	?>
  <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2" bgcolor="#e6f2ea">
      <p ><span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>Localization - Time and Date</b></span></td>
    </tr>
    <tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Display Date Format</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="date_format" size="49" value="<?php echo htmlentities(DATE_FORMAT); ?>"><br> Note: Only works for the mouseover add(see http://www.php.net/date for formatting info)</span></td>
    </tr>
	
	<tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Input Date Sequence</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	   <input type="text" name="date_input_seq" size="49" value="<?php echo (DATE_INPUT_SEQ); ?>"> Eg. YMD for the international date standard (ISO 8601). The sequence should always contain one D, one M and one Y only, in any order.
	  </span></td>
	 </tr>
	
	<tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">GMT Difference</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <select name="gmt_dif" value="<?php echo htmlentities(GMT_DIF); ?>">
	  <option value="-12" <?php if (GMT_DIF=='-12.00') { echo " selected "; } ?> >-12:00</option>
	  <option value="-11" <?php if (GMT_DIF=='-11.00') { echo " selected "; } ?> >-11:00</option>
	  <option value="-10" <?php if (GMT_DIF=='-10.00') { echo " selected "; } ?> >-10:00</option>
	  <option value="-9" <?php if (GMT_DIF=='-9.00') { echo " selected "; } ?> >-9:00</option>
	  <option value="-8" <?php if (GMT_DIF=='-8.00') { echo " selected "; } ?> >-8:00</option>
	  <option value="-7" <?php if (GMT_DIF=='-7.00') { echo " selected "; } ?> >-7:00</option>
	  <option value="-6" <?php if (GMT_DIF=='-6.00') { echo " selected "; } ?> >-6:00</option>
	  <option value="-5" <?php if (GMT_DIF=='-5.00') { echo " selected "; } ?> >-5:00</option>
	  <option value="-4" <?php if (GMT_DIF=='-4.00') { echo " selected "; } ?> >-4:00</option>
	  <option value="-3.5" <?php if (GMT_DIF=='-3.5') { echo " selected "; } ?> >-3:30</option>
	  <option value="-3" <?php if (GMT_DIF=='-3.00') { echo " selected "; } ?> >-3:00</option>
	  <option value="-2" <?php if (GMT_DIF=='-2.00') { echo " selected "; } ?> >-2:00</option>
	  <option value="-1" <?php if (GMT_DIF=='-1.00') { echo " selected "; } ?> >-1:00</option>
	  <option value="0" <?php if (GMT_DIF=='0') { echo " selected "; } ?> >0:00</option>
	  <option value="1" <?php if (GMT_DIF=='1.00') { echo " selected "; } ?> >+1:00</option>
	  <option value="2" <?php if (GMT_DIF=='2.00') { echo " selected "; } ?> >+2:00</option>
	  <option value="3" <?php if (GMT_DIF=='3.00') { echo " selected "; } ?> >+3:00</option>
	  <option value="3.5" <?php if (GMT_DIF=='3.50') { echo " selected "; } ?> >+3:30</option>
	  <option value="4" <?php if (GMT_DIF=='4.00') { echo " selected "; } ?> >+4:00</option>
	  <option value="4.5" <?php if (GMT_DIF=='4.5') { echo " selected "; } ?> >+4:30</option>
	  <option value="5" <?php if (GMT_DIF=='5.00') { echo " selected "; } ?> >+5:00</option>
	  <option value="5.5" <?php if (GMT_DIF=='5.50') { echo " selected "; } ?> >+5:30</option>
	  <option value="5.75" <?php if (GMT_DIF=='5.75') { echo " selected "; } ?> >+5:45</option>
	  <option value="6" <?php if (GMT_DIF=='6.00') { echo " selected "; } ?> >+6:00</option>
	  <option value="6.5" <?php if (GMT_DIF=='6.5') { echo " selected "; } ?> >+6:30</option>
	  <option value="7" <?php if (GMT_DIF=='7.00') { echo " selected "; } ?> >+7:00</option>
	  <option value="8" <?php if (GMT_DIF=='8.00') { echo " selected "; } ?> >+8:00</option>
	  <option value="9" <?php if (GMT_DIF=='9.00') { echo " selected "; } ?> >+9:00</option>
	  <option value="9.5" <?php if (GMT_DIF=='9.5') { echo " selected "; } ?> >+9:30</option>
	  <option value="10" <?php if (GMT_DIF=='10.00') { echo " selected "; } ?> >+10:00</option>
	  <option value="11" <?php if (GMT_DIF=='11.00') { echo " selected "; } ?> >+11:00</option>
	  <option value="12" <?php if (GMT_DIF=='12.00') { echo " selected "; } ?> >+12:00</option>
	  <option value="13" <?php if (GMT_DIF=='13.00') { echo " selected "; } ?> >+13:00</option>

	  </select> from GMT
	  <br></span></td>
    </tr>
	
	
	
	
	</table>

   <p>&nbsp;</p>
    <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr >
      <td colspan="2" bgcolor="#e6f2ea">
      <p ><span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>Grid Image Settings</b></span></td>
    </tr>
	<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "> 
       Output Grid Image(s) As</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	   <input type="radio" name="output_jpeg" size="49" value="Y" <?php if (OUTPUT_JPEG=='Y') { echo " checked "; } ?> >JPEG (Lossy compression). JPEG Quality: <input type="text" name='jpeg_quality' value="<?php echo JPEG_QUALITY; ?>" size="2">% <br><input type="radio" name="output_jpeg" value="N" <?php if (OUTPUT_JPEG=='N') { echo " checked "; } ?> >PNG (Non-lossy compression, always highest possible quality)<br>
	   <input type="radio" name="output_jpeg" value="GIF" <?php if (OUTPUT_JPEG=='GIF') { echo " checked "; } ?> >GIF (8-bit, 256 color. Supported on newer versions of GD / PHP)
	   </span></td>
    </tr>
		<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "> 
       Interlace Grid Image?</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	   <input type="radio" name="interlace_switch" size="49" value="YES" <?php if (INTERLACE_SWITCH=='YES') { echo " checked "; } ?> > Yes (Parts of the grid are 'previewed' while loading) Only works well for PNG or GIF. IE has a bug for JPG files)<br>
	   <input type="radio" name="interlace_switch" value="NO" <?php if (INTERLACE_SWITCH=='NO') { echo " checked "; } ?> >No
	   </span></td>
    </tr>
	
	<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "> 
       Display a grid in the background?</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	   <input type="radio" name="display_pixel_background" size="49" value="YES" <?php if (DISPLAY_PIXEL_BACKGROUND=='YES') { echo " checked "; } ?> > Yes. (A blank pixel grid is loaded almost instantly, and then the real pixel grid is loaded on top)<br>
	   <input type="radio" name="display_pixel_background" value="NO" <?php if (DISPLAY_PIXEL_BACKGROUND=='NO') { echo " checked "; } ?> >No
	   </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Pixel Selection Method</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
	  <input type="radio" name="use_ajax" value="SIMPLE"  <?php if (USE_AJAX=='SIMPLE') { echo " checked "; } ?> >Simple (Upload whole image at a time, users can start ordering without logging in. Uses AJAX. Recommended.) <br>
      <input type="radio" name="use_ajax" value="YES"  <?php if (USE_AJAX=='YES') { echo " checked "; } ?> >Advanced (Select individual blocks. Uses AJAX) <br>
	  <input type="radio" name="use_ajax" value="NO"  <?php if (USE_AJAX=='NO') { echo " checked "; } ?> >Advanced, no AJAX<br>
	  </span></td>
    </tr>
	<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "> 
       Resize uploaded pixels automatically?</td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	   <input type="radio" name="mds_resize" size="49" value="YES" <?php if (MDS_RESIZE=='YES') { echo " checked "; } ?> > Yes. Uploaded pixels will be resized to fit in to the blocks<br>
	   <input type="radio" name="mds_resize" value="NO" <?php if (MDS_RESIZE=='NO') { echo " checked "; } ?> >No
	   </span></td>
    </tr>
	<?php

	if (BANNER_DIR=='BANNER_DIR') {	

		$BANNER_DIR = 'banners/';

	} else {

		if (function_exists('get_banner_dir')) {

			$BANNER_DIR = get_banner_dir();
		} else {
			$BANNER_DIR = BANNER_DIR;

		}

	}

	?>
	<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "> 
       Output processed images to:</span></td>
      <td bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	  <select name="banner_dir" size="3" >
	   <option value='pixels/' <?php if ($BANNER_DIR=='pixels/') { echo ' selected '; } ?>>pixels/ (recommended)</option>
	  <option value='banners/' <?php if ($BANNER_DIR=='banners/') { echo ' selected '; } ?> >banners/ </option>
	  <option value='mdsimages/' <?php if ($BANNER_DIR=='mdsimages/') { echo ' selected '; } ?>>mdsimages/</option>
	  </select><br>
	 
	   (Be aware that some AdBlocker software blindly blocks anything coming from banners/ directory. Please make sure that this directory exists in the main directory of the script and has write permissions, chmod 777)
	   </span></td>
    </tr>


	

	<tr>
	<td colspan="2">
NOTES<br>
 If you have just installed your script or changed the above output directory, you
  will need to process your grids(s) from the Pixel Admin section or else your images will 
  not appear.
	</td>
</table>
 
<p>&nbsp;<p>
   <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2"  bgcolor="#e6f2ea">
      <span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>Email Settings</b>
	 
	  
	</span></td>

    </tr>
    <tr>
      <td width="20%"  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Advertiser when an order is Confirmed?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
     <input type="radio" name="email_user_order_confirmed" value="YES"  <?php if (EMAIL_USER_ORDER_CONFIRMED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_user_order_confirmed" value="NO"  <?php if (EMAIL_USER_ORDER_CONFIRMED=='NO') { echo " checked "; } ?> >No
	 </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Admin when an order is Confirmed?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_admin_order_confirmed" value="YES"  <?php if (EMAIL_ADMIN_ORDER_CONFIRMED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_admin_order_confirmed" value="NO"  <?php if (EMAIL_ADMIN_ORDER_CONFIRMED=='NO') { echo " checked "; } ?> >No
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Advertiser when an order is Completed?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_user_order_completed" value="YES"  <?php if (EMAIL_USER_ORDER_COMPLETED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_user_order_completed" value="NO"  <?php if (EMAIL_USER_ORDER_COMPLETED=='NO') { echo " checked "; } ?> >No
	  </span></td>
    </tr>
     <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Admin when an order is Completed?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_admin_order_completed" value="YES"  <?php if (EMAIL_ADMIN_ORDER_COMPLETED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_admin_order_completed" value="NO"  <?php if (EMAIL_ADMIN_ORDER_COMPLETED=='NO') { echo " checked "; } ?> >No<br>
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Advertiser when an order is Pended?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_user_order_pended" value="YES"  <?php if (EMAIL_USER_ORDER_PENDED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_user_order_pended" value="NO"  <?php if (EMAIL_USER_ORDER_PENDED=='NO') { echo " checked "; } ?> >No
	  </span></td>
    </tr>
     <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Admin when an order is Pended?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_admin_order_pended" value="YES"  <?php if (EMAIL_ADMIN_ORDER_PENDED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_admin_order_pended" value="NO"  <?php if (EMAIL_ADMIN_ORDER_PENDED=='NO') { echo " checked "; } ?> >No<br>
	  </span></td>
    </tr>
	 <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Advertiser when an order is Expired?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_user_order_expired" value="YES"  <?php if (EMAIL_USER_ORDER_EXPIRED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_user_order_expired" value="NO"  <?php if (EMAIL_USER_ORDER_EXPIRED=='NO') { echo " checked "; } ?> >No<br>
	  </span></td>
     <tr>
	  <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Email Admin when an order is Expired?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_admin_order_expired" value="YES"  <?php if (EMAIL_ADMIN_ORDER_EXPIRED=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_admin_order_expired" value="NO"  <?php if (EMAIL_ADMIN_ORDER_EXPIRED=='NO') { echo " checked "; } ?> >No<br>
	  </span></td>
     <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Send validation email to user?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="em_needs_activation" value="YES"  <?php if (EM_NEEDS_ACTIVATION=='YES') { echo " checked "; } ?> >Yes - users need to validate their account before loging in.<br>
	  <input type="radio" name="em_needs_activation" value="AUTO"  <?php if (EM_NEEDS_ACTIVATION=='AUTO') { echo " checked "; } ?> >No<br>
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Send validation email to Admin?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_admin_activation" value="YES"  <?php if (EMAIL_ADMIN_ACTIVATION=='YES') { echo " checked "; } ?> >Yes. When a user signs up, a copy of the validation email is sent to admin.<br>
	  <input type="radio" name="email_admin_activation" value="NO"  <?php if (EMAIL_ADMIN_ACTIVATION=='NO') { echo " checked "; } ?> >No<br>
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Pixels Modified: Send a notification email to Admin?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="email_admin_publish_notify" value="YES"  <?php if (EMAIL_ADMIN_PUBLISH_NOTIFY=='YES') { echo " checked "; } ?> >Yes. When a user modifies their pixels, a notification email is sent to admin.<br>
	  <input type="radio" name="email_admin_publish_notify" value="NO"  <?php if (EMAIL_ADMIN_PUBLISH_NOTIFY=='NO') { echo " checked "; } ?> >No<br>
	  </span></td>
    </tr>
	<!--
	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1" color='red'><b>NEW! </b></font><font face="Verdana" size="1">Send Expiration reminders?</font></td>
      <td  bgcolor="#e6f2ea"><font size="1" face="Verdana">
      <input type="radio" name="email_user_expire_warning" value="YES"  <?php if (EMAIL_USER_EXPIRE_WARNING=='YES') { echo " checked "; } ?> >Yes. Send 1 expiration warning to advertiser at least 5 days in advance.<br>
	  <input type="radio" name="email_user_expire_warning" value="NO"  <?php if (EMAIL_USER_EXPIRE_WARNING=='NO') { echo " checked "; } ?> >No<br>
	  </font></td>
   
	 </tr>
	 -->
   
    </table>
<p>&nbsp;</p>
	<table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td  colspan="2" width="360" bgcolor="#e6f2ea">
      <p ><span style="font-family: Verdana,sans-serif; font-size: x-small; "><b>SMTP Settings</b><br>
	  <input type="checkbox" name="use_smtp" value="YES"  <?php if (USE_SMTP=='YES') { echo " checked "; } ?> >Enable SMTP Server. (All outgoing email will be sent via authenticated SMTP server connection. By default, the email is sent using the PHP mail() function, and there is no need to turn this option on. Please make sure to fill in all the fields if you enable this option. POP port setting is used to verify that the script can connect to a POP account to check if the username and password was correctly filled in when the test button is clicked. )<br>
	  </span>
	  </p></td>
    </tr>
    <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">SMTP Server address</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="email_smtp_server" size="33" value="<?php echo EMAIL_SMTP_SERVER; ?>"><br>Eg. mail.example.com</span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">POP3 Server address</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="email_pop_server" size="33" value="<?php echo EMAIL_POP_SERVER; ?>"><br>Eg. mail.example.com, usually the same as the SMTP server.</span></td>
    </tr>
     <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">SMTP/POP3 Username</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="email_smtp_user" size="33" value="<?php echo EMAIL_SMTP_USER; ?>"></span></td>
    </tr>
     <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">SMTP/POP3 Password</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="password" name="email_smtp_pass"  size="33" value="<?php echo EMAIL_SMTP_PASS; ?>"></span></td>
    </tr>
    <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">SMTP Authentication Hostname</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="email_smtp_auth_host" size="33" value="<?php echo EMAIL_SMTP_AUTH_HOST; ?>">(This is usually the same as your SMTP Server address)</span></td>
    </tr>
    <tr>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">SMTP Port</span></td>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
                <input type="text" name="smtp_port" size="33" value="<?php echo SMTP_PORT; ?>">(Leave blank to default to 465)</span></td>
    </tr>
    <tr>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">POP3 Port</span></td>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
                <input type="text" name="pop3_port" size="33" value="<?php echo POP3_PORT; ?>">(Leave blank to default to 995)</span></td>
    </tr>
    <tr>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Enable email TLS/SSL</span></td>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
        <input type="radio" name="email_tls" value="1"  <?php if (EMAIL_TLS=='1') { echo " checked "; } ?> >Yes<br>
        <input type="radio" name="email_tls" value="0"  <?php if (EMAIL_TLS=='0') { echo " checked "; } ?> >No
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">My SMTP server uses the POP-before-SMTP mechanism</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
     <input type="radio" name="email_pop_before_smtp" value="YES"  <?php if (EMAIL_POP_BEFORE_SMTP=='YES') { echo " checked "; } ?> >Yes<br>
	  <input type="radio" name="email_pop_before_smtp" value="NO"  <?php if (EMAIL_POP_BEFORE_SMTP=='NO') { echo " checked "; } ?> >No - Default setting, correct 99% of cases
	 </span></td>
    </tr>
    <tr>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Enable email debug (saves file in /mail/.maildebug.log)</span></td>
        <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
                <input type="radio" name="email_debug" value="YES"  <?php if (EMAIL_DEBUG=='YES') { echo " checked "; } ?> >Yes<br>
                <input type="radio" name="email_debug" value="NO"  <?php if (EMAIL_DEBUG=='NO') { echo " checked "; } ?> >No
    </tr>

	<?php
	
	$new_window = "onclick=\"test_email_window(); return false;\"";

	if (!defined('EMAILS_PER_BATCH')) {
		define ('EMAILS_PER_BATCH', 10);
	}

	if (!defined('EMAILS_MAX_RETRY')) {
		define ('EMAILS_MAX_RETRY', 15);
	}

	if (!defined('EMAILS_ERROR_WAIT')) {
		define ('EMAILS_ERROR_WAIT', 20);
	}

	?>
	 <tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Outgoing email queue settings</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      Send a maxiumum of <input type="text" name="emails_per_batch" size="3" value="<?php echo EMAILS_PER_BATCH; ?>">emails per batch (enter a number > 0)<br>
	  On error, retry <input type="text" name="emails_max_retry" size="3" value="<?php echo EMAILS_MAX_RETRY; ?>"> times before giving up. (recommened: 15)<br>
	  On error, wait at least <input type="text" name="emails_error_wait" size="3" value="<?php echo EMAILS_ERROR_WAIT; ?>">minutes before retry. (20 minutes recommended)<br>
	  Keep sent emails for <input type="text" name="emails_days_keep" size="3" value="<?php  if ((EMAILS_DAYS_KEEP=='EMAILS_DAYS_KEEP')) { define (EMAILS_DAYS_KEEP, '0'); } echo EMAILS_DAYS_KEEP; ?>">days. (0 = keep forever)<br> 
	  </span>
	  Note: You can view the outgoing queue under the 'Report' menu<br>
	  </td>
    </tr>
</table>

	<p>&nbsp;</p>
	 <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2"  bgcolor="#e6f2ea">
      <span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>Misc Settings</b>
	 
	  
	</span></td>

    </tr>
	 <tr>
      <td width="20%" bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">How many days to keep Expired orders before cancellation?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="days_renew" size="2" value="<?php echo DAYS_RENEW; ?>">(Enter a number. 0 = Do not cancel)</span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">How many days to keep Confirmed (but not paid) orders before cancellation?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="days_confirmed" size="2" value="<?php echo DAYS_CONFIRMED; ?>">(Enter a number. 0 = never cancel)</span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">How many <b>hours</b> to keep Unconfirmed orders before deletion?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="hours_unconfirmed" size="2" value="<?php echo HOURS_UNCONFIRMED; ?>">(Enter a number. 0 = never delete)</span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">How many days to keep Cancelled orders before deletion?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="days_cancel" size="2" value="<?php echo DAYS_CANCEL; ?>">(Enter a number. 0 = never delete. Note: If deleted, the order will stay in the database, and only the status will simply  change to 'deleted'. The blocks will be freed)</span></td>
    </tr>
	
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Enable URL cloaking?<br>(Supposedly, when enabled, the advertiser's link will get a better advantage from search engines.)</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="enable_cloaking" value="YES"  <?php if (ENABLE_CLOAKING=='YES') { echo " checked "; } ?> >Yes - All links will point directly to the Advertiser's URL. Click tracking will be managed by a JavaScript.) <br>
	  <input type="radio" name="enable_cloaking" value="NO"  <?php if (ENABLE_CLOAKING=='NO') { echo " checked "; } ?> >No - All links will be re-directed click.php <br>
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Validate URLs by connecting to them?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="validate_link" value="YES"  <?php if (VALIDATE_LINK=='YES') { echo " checked "; } ?> >Yes - The script will try to connect to the Advertiser's url to make sure that the link is correct.) <br>
	  <input type="radio" name="validate_link" value="NO"  <?php if (VALIDATE_LINK=='NO') { echo " checked "; } ?> >No <br>
	  </span></td>
    </tr>
	<!--
	<tr>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">Maximum blocks that can be selected per order</font></td>
      <td  bgcolor="#e6f2ea"><font face="Verdana" size="1">
      <input type="text" name="max_blocks" size="2" value="<?php echo MAX_BLOCKS; ?>">(Enter a number. A zero or blank value means unlimited)</font></td>
    </tr>
	<tr>
	-->
	
	 <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Memory Limit</span></td>
 <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
	 <input type='radio' name='memory_limit' value='8M' <?php if (MEMORY_LIMIT=='8M') { echo ' checked '; } ?> > 8MB  | <input type='radio' name='memory_limit' value='12M' <?php if (MEMORY_LIMIT=='12M') { echo ' checked '; } ?> > 12MB (default) | <input type='radio' name='memory_limit' value='16M' <?php if (MEMORY_LIMIT=='16M') { echo ' checked '; } ?> > 16MB  | <input type='radio' name='memory_limit' value='32M' <?php if (MEMORY_LIMIT=='32M') { echo ' checked '; } ?> > 32MB | <input type='radio' name='memory_limit' value='64M' <?php if (MEMORY_LIMIT=='64M') { echo ' checked '; } ?> > 64MB | <input type='radio' name='memory_limit' value='128M' <?php if (MEMORY_LIMIT=='128M') { echo ' checked '; } ?> > 128M | <input type='radio' name='memory_limit' value='256M' <?php if (MEMORY_LIMIT=='256M') { echo ' checked '; } ?> > 256M (Note: If your script is reporting a 'memory exhausted' error, please check to make sure that you have currectly defined your grid size)

	</span></td>

	</tr>
	<tr>
      <td bgcolor="#e6f2ea" width="20%"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Error Reporting</span></td>
      <td bgcolor="#e6f2ea">
		<span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
			<input type="text" name="error_reporting" size="40" value="<?php echo ERROR_REPORTING; ?>"> (PHP error_reporting value)<br />
			More information: <a target="_blank" href="http://php.net/manual/en/function.error-reporting.php">http://php.net/manual/en/function.error-reporting.php</a>
		</span>
	  </td>
    </tr>


<?php

	if (REDIRECT_URL=='REDIRECT_URL') {
		$REDIRECT_URL="http://";

	} else {
		$REDIRECT_URL = REDIRECT_URL;
	}

?>
	
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Redirect when a user clicks on available block? </span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="redirect_switch" value="YES"  <?php if (REDIRECT_SWITCH=='YES') { echo " checked "; } ?> >Yes - When an available block is clicked, redirect to: <input type="text" name="redirect_url" size="30" value="<?php echo $REDIRECT_URL; ?>"> <br><b>NOTE:</b> This option will only wrok for grids that are on the same domain as the script (browser security). If the grid is placed on another domain, IE may report a 'permission denied' JavaScript error. <br>
	  <input type="radio" name="redirect_switch" value="NO"  <?php if (REDIRECT_SWITCH=='NO') { echo " checked "; } ?> >No (default)<br>
	  Note: You will need to process your grids after changing this option.
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Advanced Click Count? </span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="advanced_click_count" value="YES"  <?php if (ADVANCED_CLICK_COUNT=='YES') { echo " checked "; } ?> >Yes - Clicks will be counted by day <br>
	  <input type="radio" name="advanced_click_count" value="NO"  <?php if (ADVANCED_CLICK_COUNT=='NO') { echo " checked "; } ?> >No (default)<br>
	
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Use PayPal Subscription features? </span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="use_paypal_subscr" value="YES"  <?php if (USE_PAYPAL_SUBSCR=='YES') { echo " checked "; } ?> >Yes - When customer places pixels for rent, paypal will subscribe them and re-bill automatically. This feature is in Beta right now, not recommended.  <br>
	  <input type="radio" name="use_paypal_subscr" value="NO"  <?php if (USE_PAYPAL_SUBSCR=='NO') { echo " checked "; } ?> >No (default)<br>
	
	  </span></td>
    </tr>
	<tr>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Force the browser to cache the HTML image map? </span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="mds_agressive_cache" value="YES"  <?php if (MDS_AGRESSIVE_CACHE=='YES') { echo " checked "; } ?> >Yes - The script will tell the browser to cache the page to save download time. This feature may only work on Apache based servers. Disable if your grid does not refresh, even though you processed the pixels. <br>
	  <input type="radio" name="mds_agressive_cache" value="NO"  <?php if (MDS_AGRESSIVE_CACHE=='NO') { echo " checked "; } ?> >No (default)<br>
	
	  </span></td>
    </tr>
</table>
<p>&nbsp;</p>
	 <table border="0" cellpadding="5" cellspacing="2" style="border-style:groove" id="AutoNumber1" width="100%" bgcolor="#FFFFFF">
    <tr>
      <td colspan="2"  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; "><b>Mouseover Effects</b></span>
	  </td>
	  </tr>
	  <tr>
      <td  bgcolor="#e6f2ea" width="20%" ><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Show a box when the positioning mouse over a block?</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
      <input type="radio" name="enable_mouseover" value="YES"  <?php if (ENABLE_MOUSEOVER=='YES') { echo " checked "; } ?> >Yes - Animation Speed: <input type='radio' name='animation_speed' value='100' <?php if (ANIMATION_SPEED=='100') { echo ' checked '; } ?> >Slow | <input type='radio' name='animation_speed' value='50' <?php if (ANIMATION_SPEED=='50') { echo ' checked '; } ?>> Normal | <input type='radio' name='animation_speed' value='10' <?php if (ANIMATION_SPEED=='10') { echo ' checked '; } ?>> Fast | <input type='radio' name='animation_speed' value="1" <?php if (ANIMATION_SPEED=='1') { echo ' checked '; } ?> > Very Fast <br>
	  <input type="radio" name="enable_mouseover" value="POPUP"  <?php if (ENABLE_MOUSEOVER=='POPUP') { echo " checked "; } ?> >Yes - Simple popup box, with no animation<br>
	  <input type="radio" name="enable_mouseover" value="NO"  <?php if (ENABLE_MOUSEOVER=='NO') { echo " checked "; } ?> >No, turn off<br>
	  </span></td>
    </tr>
<tr>
<?php
if (!defined('HIDE_TIMEOUT')) {
	define ('HIDE_TIMEOUT', '500');
}
?>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">Delay before hiding</span></td>
      <td  bgcolor="#e6f2ea"><span style="font-family: Verdana,sans-serif; font-size: xx-small; ">
      <input type="text" name="hide_timeout" size="2" value="<?php echo HIDE_TIMEOUT; ?>">milliseconds (eg. enter 500 to wait 500 milliseconds before hiding the box)</span></td>
    </tr>
</table>
	
  <p><span style="font-size: xx-small; font-family: Verdana,sans-serif; ">
  <input type="submit" value="Save Configuration" name="save"></span></p>
</form>
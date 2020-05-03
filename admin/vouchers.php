<?php
/**
 * @package        mds
 * @copyright      (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author         Ryan Rhode, ryan@milliondollarscript.com
 * @license        This program is free software; you can redistribute it and/or modify
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

require("../config.php");
require ('admin_common.php');

?>
<?php echo $f2->get_doc(); ?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        font-size:10pt;
    }
    th {
        text-align: left;
    }
    th.limited {
        word-wrap: break-word;
        width: 150px;
    }
    thead th {
        background-color: #e2e3e5;
    }
    tbody td {
        background-color: #ffffff;
    }
    .form-submit-button-danger {
        background: #a20100;
        color: white;
        border-color: #a20037;
    }
</style>

<script type="text/javascript">
    function confirmLink(theLink, theConfirmMsg) {
        if (theConfirmMsg == '' || typeof(window.opera) != 'undefined') {
           return true;
        }

        var is_confirmed = confirm(theConfirmMsg + '\n');
        if (is_confirmed) {
           theLink.href += '&is_js_confirmed=1';
        }

        return is_confirmed;
    }
    function copyTextToClipboard(name, code) {
      var textArea = document.createElement("textarea");

      //
      // *** This styling is an extra step which is likely not required. ***
      //
      // Why is it here? To ensure:
      // 1. the element is able to have focus and selection.
      // 2. if element was to flash render it has minimal visual impact.
      // 3. less flakyness with selection and copying which **might** occur if
      //    the textarea element is not visible.
      //
      // The likelihood is the element won't even render, not even a
      // flash, so some of these are just precautions. However in
      // Internet Explorer the element is visible whilst the popup
      // box asking the user for permission for the web page to
      // copy to the clipboard.
      //

      // Place in top-left corner of screen regardless of scroll position.
      textArea.style.position = 'fixed';
      textArea.style.top = 0;
      textArea.style.left = 0;

      // Ensure it has a small width and height. Setting to 1px / 1em
      // doesn't work as this gives a negative w/h on some browsers.
      textArea.style.width = '2em';
      textArea.style.height = '2em';

      // We don't need padding, reducing the size if it does flash render.
      textArea.style.padding = 0;

      // Clean up any borders.
      textArea.style.border = 'none';
      textArea.style.outline = 'none';
      textArea.style.boxShadow = 'none';

      // Avoid flash of white box if rendered for any reason.
      textArea.style.background = 'transparent';

      textArea.value = "Hi " + name + ", thank you for donating to #DrupalCares! We’ve started a fun new campaign called Pixels for Drupal, and since you’ve already donated, we’re sending you a voucher code to claim your pixels. \n" +
        "\n" +
        "How does it work?\n" +
        "-----------------\n" +
        "You might remember the Million Dollar Homepage (http://www.milliondollarhomepage.com/) from way back when. You could buy pixels and use them to post whatever you wanted - and the guy who started it made a million dollars. We thought it would be fun to make a Half Million Dollar homepage to help the Drupal Association reach their goal. Donors can purchase pixels to support the DA. You’ll get 100 pixels for every $5 you donate. You can post images and links to your pixels. \n" +
        "\n" +
        "What do I need to do?\n" +
        "---------------------\n" +
        "1. Create an account on https://pixelsfordrupal.com/.\n" +
        "2. You’ll receive a verification email. Log in and verify your account. \n" +
        "3. Click “Upload Pixels” and enter your voucher code. \n" +
        "\n" +
        "Your voucher code is: " + code + "\n" +
        "\n" +
        "You’ll see how many pixels you can upload based on the amount of your donation. \n" +
        "\n" +
        "You can now upload your pixels. You don’t need to do this all at once. You can upload some now, some later - you can use the whole amount at once or divide it up - it’s all up to you! You can also donate more to increase the amount of pixels you can upload. \n" +
        "\n" +
        "What can I upload?\n" +
        "------------------\n" +
        "It’s up to you! Uploads and links are subject to the Drupal Code of Conduct (https://www.drupal.org/dcoc), so keep it professional and kind. Upload a picture of yourself, your pet, your company logo, a DrupalCon memory - be creative! We will have a moderation team quickly reviewing each submission. \n"

      document.body.appendChild(textArea);
      textArea.focus();
      textArea.select();

      try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copying text command was ' + msg);
      } catch (err) {
        console.log('Oops, unable to copy');
      }

      document.body.removeChild(textArea);
    }
</script>
</head>

<body>


<?php


function validate_input () {
    $error = '';

	if ($_REQUEST['price_discount']=='' && $_REQUEST['blocks_discount']=='') {

		$error .= "- You muster enter a price OR blocks discount <br>";

	}

	if($_REQUEST['price_discount'] and !is_numeric($_REQUEST['price_discount'])) {
		$error .= "- Price discount must be a number <br>";
	}

	if($_REQUEST['blocks_discount'] and !is_numeric($_REQUEST['blocks_discount'])) {
		$error .= "- Blocks discount must be a number <br>";
	}

	return $error;



}

if ($_REQUEST['action']=='activate') {

	$sql = "UPDATE vouchers set active=1 where voucher_id='".mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voucher'])."' ";
	mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));

}

if ($_REQUEST['action']=='deactivate') {

	$sql = "UPDATE vouchers set active=0 where voucher_id='".mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voucher'])."' ";
	mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));

}

if ($_REQUEST['action']=='delete') {

	$sql = "DELETE FROM vouchers WHERE voucher_id='".mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voucher'])."' ";
	mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));

}


if ($_REQUEST['submit']!='') {

	$error = validate_input();

	if ($error == '') {

		$voucher_id = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voucher']);
		$code = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['code']);
		$price_discount = $_REQUEST['price_discount'] == '' ? 'NULL' : '"' . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['price_discount']) . '"';
		$blocks_discount = $_REQUEST['blocks_discount'] == '' ? 'NULL' : '"' . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['blocks_discount']) . '"';
		$name = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['name']);
		$do_username = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['do_username']);
		$banner_id = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['banner_id']);
		$notes = mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['notes']);
		$single_use = $_REQUEST['single_use'] == 1 ? 1 : 0;
		$active = $_REQUEST['active'] == 1 ? 1 : 0;

		if ($_REQUEST['action']=='edit') {

			$sql = <<<SQL
			UPDATE vouchers
			SET code = "$code",
			price_discount = $price_discount,
			blocks_discount = $blocks_discount,
			name = "$name",
			do_username = "$do_username",
			banner_id = $banner_id,
			notes = "$notes",
			single_use = $single_use,
			active = $active
			WHERE voucher_id=$voucher_id
SQL;

		} else {
		
			$sql = <<<SQL
			INSERT INTO vouchers (
				code,
				price_discount,
				blocks_discount,
				name,
				do_username,
				banner_id,
				notes,
				single_use,
				active
			)
			VALUES (
				"$code",
				$price_discount,
				$blocks_discount,
				"$name",
				"$do_username",
				$banner_id,
				"$notes",
				$single_use,
				$active
			)
SQL;
		}
		

		mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);

		$_REQUEST['new']='';

	}

}



if (($_REQUEST['new']!='') || ($_REQUEST['action']=='edit')) {

	if ($_REQUEST['new']=='1') {
		echo "<h4>New Voucher:</h4>";
	}
	if ($_REQUEST['action']=='edit') {
		echo "<h4>Edit Voucher:</h4>";

		$sql = "SELECT * FROM vouchers WHERE `voucher_id`='".mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['voucher'])."' ";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		
		$_REQUEST['code'] = $row['code'];
		$_REQUEST['price_discount'] = $row['price_discount'];
		$_REQUEST['blocks_discount'] = $row['blocks_discount'];
		$_REQUEST['name'] = $row['name'];
		$_REQUEST['do_username'] = $row['do_username'];
		$_REQUEST['banner_id'] = $row['banner_id'];
		$_REQUEST['notes'] = $row['notes'];
		$_REQUEST['single_use'] = $row['single_use'];
		$_REQUEST['active'] = $row['active'];

		$single_use_checked = $_REQUEST['single_use'] == 1 ? ' checked' : '';
		$active_checked = $_REQUEST['active'] == 1 ? ' checked' : '';
	}

?>
<form enctype="multipart/form-data" method="post">
<input type="hidden" value="<?php echo $_REQUEST['action']?>" name="action" >
<input type="hidden" value="<?php echo $_REQUEST['voucher']?>" name="voucher" >
<table border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9">
<tr >
	<td>Code *:</td>
	<td><input size="30" type="text" name="code" required value="<?php echo $_REQUEST['code']; ?>"></td>
</tr>
<tr >
	<td>$ Discount:</td>
	<td><input size="4" type="text" name="price_discount" value="<?php echo $_REQUEST['price_discount']; ?>"></td>
</tr>
<tr >
	<td>Block Discount:</td>
	<td><input size="4" type="text" name="blocks_discount" value="<?php echo $_REQUEST['blocks_discount']; ?>"></td>
</tr>
<tr >
	<td>Name:</td>
	<td><input size="30" type="text" name="name" value="<?php echo $_REQUEST['name']; ?>"></td>
</tr>
<tr >
	<td>D.O Username:</td>
	<td><input size="30" type="text" name="do_username" value="<?php echo $_REQUEST['do_username']; ?>"></td>
</tr>
<tr >
	<td>Banner *:</td>
	<td>
		<select name="banner_id" required>
		<?php
			$sql = "SELECT banner_id, name FROM banners";
			$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));
			$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

			foreach ($rows as $banner) {
				$selected = $_REQUEST['banner_id'] == $banner['banner_id'] ? ' selected' : '';
				echo '<option value="' . $banner['banner_id'] . '" ' . $selected . '>' . $banner['name'] . '</option>';
			}
		?>
		</select>
	</td>
</tr>
<tr >
	<td>Notes:</td>
	<td><textarea name="notes"><?php echo $_REQUEST['notes']; ?></textarea></td>
</tr>
<tr >
	<td>Single Use:</td>
	<td><input type="checkbox" name="single_use" value="1" <?php echo $single_use_checked; ?>></td>
</tr>
<tr >
	<td>Active:</td>
	<td><input type="checkbox" name="active" value="1" <?php echo $active_checked; ?>></td>
</tr>
</table>
<input type="submit" name="submit" value="Submit">
</form>
<hr />
<?php

	if ($error !='') {
		echo "<b><font color='red'>ERROR:</b> Cannot save voucher into database.<br>";
		echo $error;
	}

}

?>

    <input type="button" value="New Voucher..." onclick="window.location='vouchers.php?new=1'">
    <table border="0" cellSpacing="1" cellPadding="3" bgColor="#d9d9d9"
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>$ Discount</th>
                <th>Block Discount</th>
                <th class="limited">Name</th>
                <th>D.O Username</th>
                <th>Banner</th>
                <th>Notes</th>
                <th>Single Use</th>
                <th>Active</th>
                <th>Tools</th>
            </tr>
        </thead>
        <tbody>

        <?php
            $result = mysqli_query($GLOBALS['connection'], "select v.*, b.name as banner_name FROM vouchers v LEFT JOIN banners b on v.banner_id = b.banner_id order by voucher_id desc") or die (mysqli_error($GLOBALS['connection']));
            while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        ?>
            <tr>
                <td><?php echo $row['voucher_id']; ?></td>
                <td><?php echo $row['code']; ?></td>
                <td><?php echo $row['price_discount']; ?></td>
                <td><?php echo $row['blocks_discount']; ?></td>
                <td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><a href="https://www.drupal.org/u/<?php echo htmlspecialchars($row['do_username'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($row['do_username'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                <td><a href="inventory.php?action=edit&BID=<?php echo $row['banner_id']; ?>"><?php echo $row['banner_name']; ?></a></td>
                <td><?php echo htmlspecialchars($row['notes'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $row['single_use'] ? 'Y' : 'N'; ?></td>
                <td>
                    <?php if ($row['active']) { ?> <img src="active.gif" width="16" height="16" border="0" alt=""><?php } else { ?><img src="notactive.gif" width="16" height="16" border="0" alt=""><?php } ;?>
                    <?php if (!$row['active']) {?>
                    [<a href="<?php echo $_SERVER['PHP_SELF'];?>?action=activate&voucher=<?php echo $row['voucher_id'];?>">Activate</a>]
                    <?php } if ($row['active']) {?>
                    [<a href="<?php echo $_SERVER['PHP_SELF'];?>?action=deactivate&voucher=<?php echo $row['voucher_id'];?>">Deactivate</a>]
                    <?php }?>
                </td>
                <td>
                    [<a href="<?php echo $_SERVER['PHP_SELF'];?>?action=edit&voucher=<?php echo $row['voucher_id'];?>">Edit</a>]
                    [<a onclick="return confirmLink(this, 'Delete, are you sure?')" href="<?php echo $_SERVER['PHP_SELF'];?>?action=delete&voucher=<?php echo $row['voucher_id'];?>">Delete</a>]
                    [<a title="Copy the voucher plain text invite" onclick="copyTextToClipboard('<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $row['code']; ?>'); return false" href="#">Text</a>]
                </td>
            </tr>
        <?php
            }
        ?>
        </tbody>
    </table>

</body>
</html>

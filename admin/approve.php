<?php
/**
 * @package        mds
 * @copyright      (C) Copyright 2020 Ryan Rhode, All rights reserved.
 * @author         Ryan Rhode, ryan@milliondollarscript.com
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

require( "../config.php" );

require( 'admin_common.php' );

// edit this file to change the style of the mouseover box!
require( BASE_PATH . '/mouseover_box.htm' );

echo '<script>';
require( BASE_PATH . '/include/mouseover_js.inc.php' );
echo '</script>';

$BID = ( isset( $_REQUEST['BID'] ) && $f2->bid( $_REQUEST['BID'] ) != '' ) ? $f2->bid( $_REQUEST['BID'] ) : $BID = 1;

$bid_sql = " AND banner_id=$BID ";
if ( ( $BID == 'all' ) || ( $BID == '' ) ) {
	$BID     = '';
	$bid_sql = "  ";
}

if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'approve' ) {
	$sql = "UPDATE blocks set approved='Y', published='N' WHERE order_id=" . intval( $_REQUEST['order_id'] ) . " {$bid_sql}";
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	$sql = "UPDATE orders set approved='Y', published='N' WHERE order_id=" . intval( $_REQUEST['order_id'] ) . " {$bid_sql}";
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	echo "Order Approved.<br>";
}

if ( isset( $_REQUEST['mass_approve'] ) && $_REQUEST['mass_approve'] != '' ) {
	if ( isset( $_REQUEST['orders'] ) && sizeof( $_REQUEST['orders'] ) > 0 ) {

		foreach ( $_REQUEST['orders'] as $order_id ) {
			$sql = "UPDATE blocks set approved='Y', published='N' WHERE order_id='" . intval( $order_id ) . "' {$bid_sql}";
			mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
			$sql = "UPDATE orders set approved='Y', published='N' WHERE order_id='" . intval( $order_id ) . "' {$bid_sql}";
			mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		}

		echo "Orders Approved.<br>";
	}
}

if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'disapprove' ) {
	$sql = "UPDATE blocks set approved='N' WHERE user_id='" . intval( $_REQUEST['user_id'] ) . "' {$bid_sql}";
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	$sql = "UPDATE orders set approved='N' WHERE user_id='" . intval( $_REQUEST['user_id'] ) . "' {$bid_sql}";
	mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	echo "Order Disapproved.<br>";
}

if ( isset( $_REQUEST['mass_disapprove'] ) && $_REQUEST['mass_disapprove'] != '' ) {
	if ( isset( $_REQUEST['orders'] ) && sizeof( $_REQUEST['orders'] ) > 0 ) {

		foreach ( $_REQUEST['orders'] as $order_id ) {
			$sql = "UPDATE blocks set approved='N' WHERE order_id=" . intval( $order_id ) . " {$bid_sql}";
			mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
			$sql = "UPDATE orders set approved='N' WHERE order_id=" . intval( $order_id ) . " {$bid_sql}";
			mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		}

		echo "Orders Disapproved.<br>";
	}
}

if ( isset( $_REQUEST['do_it_now'] ) && $_REQUEST['do_it_now'] == 'true' ) {

	// process all grids
	$sql = "select * from banners ";
	$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	while ( $row = mysqli_fetch_array( $result ) ) {
		echo process_image( $row['banner_id'] );
		publish_image( $row['banner_id'] );
		process_map( $row['banner_id'] );
	}
}

?>
<?php echo $f2->get_doc(); ?>

<script>
	function confirmLink(theLink, theConfirmMsg) {
		if (theConfirmMsg === '') {
			return true;
		}

		var is_confirmed = confirm(theConfirmMsg + '\n');
		if (is_confirmed) {
			theLink.href += '&is_js_confirmed=1';
		}

		return is_confirmed;
	}

	function checkBoxes(checkbox, name) {
		var state, boxes, count, i;
		state = checkbox.checked;
		boxes = eval("document.form1.elements['" + name + "']");
		count = boxes.length;
		for (i = 0; i < count; i++) {
			boxes[i].checked = state;
		}
	}
</script>

</head>

<h3>Remember to process your Grid Image(s) <a href="process.php">here</a></h3>

<form name="bidselect" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="old_order_id" value="<?php //echo $order_id; ?>">
    <input type="hidden" value="<?php echo $_REQUEST['app']; ?>" name="app">
    <label>
        Select Grid:
        <select name="BID" onchange="document.bidselect.submit()">
            <option value='all'
				<?php if ( $f2->bid( $_REQUEST['BID'] ) == 'all' ) {
					echo 'selected';
				} ?>>Show All
            </option>
			<?php

			$sql = "Select * from banners ";
			$res = mysqli_query( $GLOBALS['connection'], $sql );

			while ( $row = mysqli_fetch_array( $res ) ) {

				if ( ( $row['banner_id'] == $BID ) && ( isset( $_REQUEST['BID'] ) && $f2->bid( $_REQUEST['BID'] ) != 'all' ) ) {
					$sel = 'selected';
				} else {
					$sel = '';

				}

				echo '
                    <option
                    ' . $sel . ' value=' . $row['banner_id'] . '>' . $row['name'] . '</option>';

			}
			?>
        </select>
    </label>
</form>

<?php

if ( $_REQUEST['save_links'] != '' ) {
	if ( sizeof( $_REQUEST['urls'] ) > 0 ) {
		$i = 0;

		foreach ( $_REQUEST['urls'] as $url ) {
			$sql = "UPDATE blocks SET url='" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['new_urls'][ $i ] ) . "', alt_text='" . mysqli_real_escape_string( $GLOBALS['connection'], $_REQUEST['new_alts'][ $i ] ) . "' WHERE user_id='" . intval( $_REQUEST['user_id'] ) . "' and url='" . mysqli_real_escape_string( $GLOBALS['connection'], $url ) . "' and banner_id='" . $f2->bid( $_REQUEST['BID'] ) . "'  ";
			//echo $sql."<br>";
			mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
			$i ++;
		}
	}
}

if ( $_REQUEST['edit_links'] != '' ) {

	?>
    <h3>Edit Links:</h3>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="offset" value="<?php echo $_REQUEST['offset']; ?>">
        <input type="hidden" name="BID" value="<?php echo $f2->bid( $_REQUEST['BID'] ); ?>">
        <input type="hidden" name="user_id" value="<?php echo $_REQUEST['user_id']; ?>">
        <input type="hidden" value="<?php echo $_REQUEST['app']; ?>" name="app">
        <table>
            <tr>
                <td><b>URL</b></td>
                <td><b>Alt Text</b></td>
            </tr>

			<?php

			$sql      = "SELECT alt_text, url, count(alt_text) AS COUNT, banner_id FROM blocks WHERE user_id=" . intval( $_REQUEST['user_id'] ) . "  $bid_sql group by url ";
			$m_result = mysqli_query( $GLOBALS['connection'], $sql );

			$i = 0;
			while ( $m_row = mysqli_fetch_array( $m_result ) ) {
				$i ++;
				if ( $m_row['url'] != '' ) {
					echo "<tr><td>
				<input type='hidden' name='urls[]' value='" . htmlspecialchars( $m_row['url'] ) . "'>
				<input type='text' name='new_urls[]' size='40' value=\"" . escape_html( $m_row['url'] ) . "\"></td>
						<td><input name='new_alts[]' type='text' size='80' value=\"" . escape_html( $m_row['alt_text'] ) . "\"></td></tr>";
				}
			}

			?>

        </table>
        <input type="submit" value="Save Changes" name="save_links">

    </form>

	<?php

}

$bid_sql2 = " AND blocks.banner_id=$BID ";
if ( ( $BID == 'all' ) || ( $BID == '' ) ) {
	$BID      = '';
	$bid_sql2 = "";
}

// whitelist $_REQUEST['app'] value
$Y_or_N = 'Y';
if(isset($_REQUEST['app'])) {
	if ($_REQUEST['app'] == 'N') {
		$Y_or_N = 'N';
	}
}

$sql = "
SELECT orders.order_date, orders.order_id, blocks.approved, blocks.status, blocks.user_id, blocks.banner_id, blocks.ad_id, ads.1, ads.2, users.FirstName, users.LastName, users.Username, users.Email 
    FROM ads, blocks, orders, users 
    WHERE orders.approved='" . $Y_or_N . "' 
      AND orders.user_id=users.ID 
      AND orders.order_id=blocks.order_id 
      AND blocks.order_id=ads.order_id 
      {$bid_sql2}
    GROUP BY orders.order_id 
    ORDER BY orders.order_date
";
$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
$count = mysqli_num_rows( $result );

$offset = intval( $_REQUEST['offset'] );

$records_per_page = 20;
if ( $count > $records_per_page ) {
	mysqli_data_seek( $result, $offset );
}

$pages    = ceil( $count / $records_per_page );
$cur_page = $offset / $records_per_page;
$cur_page ++;

if ( $count > $records_per_page ) {
	// calculate number of pages & current page

	echo "<center>";
	$label["navigation_page"] = str_replace( "%CUR_PAGE%", $cur_page, $label["navigation_page"] );
	$label["navigation_page"] = str_replace( "%PAGES%", $pages, $label["navigation_page"] );

	$q_string = $q_string . "&app=" . $_REQUEST['app'];
	$nav      = nav_pages_struct( $q_string, $count, $records_per_page );
	$LINKS    = 40;
	render_nav_pages( $nav, $LINKS, $q_string );
	echo "</center>";
}
?>
<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
    <input type="hidden" name="offset" value="<?php echo $_REQUEST['offset']; ?>">
    <input type="hidden" name="BID" value="<?php echo $f2->bid( $_REQUEST['BID'] ); ?>">
    <input type="hidden" name="app" value="<?php echo $_REQUEST['app']; ?>">
    <input type="hidden" name="all_go" value="">
    <table width="100%" cellSpacing="1" cellPadding="3" align="center" bgColor="#d9d9d9" border="0">
        <tr>
            <td colspan="12">
                With selected: <input type="submit" value='Approve' style="font-size: 9px; background-color: #33FF66 " onclick="if (!confirmLink(this, 'Approve for all selected, are you sure?')) return false" name='mass_approve'>
                <input type="submit" value='Disapprove' style="font-size: 9px; background-color: #FF6600" onclick="if (!confirmLink(this, 'Disapprove all selected, are you sure?')) return false" name='mass_disapprove'>
                <input type="checkbox" name="do_it_now" <?php if ( ( $_REQUEST['do_it_now'] == 'true' ) ) {
					echo ' checked ';
				} ?> value="true"> Process Grid Images immediately after approval / disapproval <br>
            </td>
        </tr>
        <tr>
        <tr>
            <td><b><input type="checkbox" onClick="checkBoxes(this, 'orders[]');"></td>
            <td><b>Order ID</b></td>
            <td><b>Order Date</b></td>
            <td><b>Customer Name</b></td>
            <td><b>Username & ID</b></td>
            <td><b>Email</b></td>
            <td><b>Grid</b></td>
            <td><b>Image</b></td>
            <td><b>Link Text(s) & Link URL(s)</b></td>
            <td><b>Action</b></td>
        </tr>
		<?php

		// TODO: use form editor field keys

		$i = 0;
		while ( ( $row = mysqli_fetch_array( $result, MYSQLI_ASSOC ) ) && ( $i < $records_per_page ) ) {
			$i ++;
			?>
            <tr onmouseover="old_bg=this.getAttribute('bgcolor');this.setAttribute('bgcolor', '#FBFDDB', 0);" onmouseout="this.setAttribute('bgcolor', old_bg, 0);" bgColor="#ffffff">
                <td><input type="checkbox" name="orders[]" value="<?php echo $row['order_id']; ?>"></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php echo $row['order_id']; ?></span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php echo $row['order_date']; ?></span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php echo $row['FirstName'] . " " . $row['LastName']; ?></span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php echo $row['Username']; ?> (#<?php echo $row['user_id']; ?>)</span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php echo $row['Email']; ?></span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php
						$sql      = "SELECT name from banners where banner_id=" . intval( $row['banner_id'] );
						$t_result = mysqli_query( $GLOBALS['connection'], $sql );
						$t_row    = mysqli_fetch_array( $t_result );
						echo $t_row['name']; ?></span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><img src="get_order_image.php?BID=<?php echo $row['banner_id']; ?>&aid=<?php echo $row['ad_id']; ?>" alt=""/></span></td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php

						if ( $row['2'] != '' ) {
							$js_str = " onmousemove=\"sB(event,'" . htmlspecialchars( str_replace( "'", "\'", ( $row['1'] ) ) ) . "',this, " . $row['ad_id'] . ")\" onmouseout=\"hI()\" ";

							echo "<span style=\"font-size: xx-small; \">" . $row['2'] . " - <a $js_str href='" . $row['2'] . "' target='_blank' >" . $row['1'] . "</a></span><br>";
						}

						echo "<a target='_blank' href='show_map.php?user_id=" . $row['user_id'] . "&BID=" . $row['banner_id'] . "'>[View Pixels...]</a>";
						?></span>
                </td>
                <td><span style="font-family: Arial,serif; font-size: x-small; "><?php
						if ( $row['approved'] == 'N' ) {
							?>
                            <input type="button" style="font-size: 9px; background-color: #33FF66" value="Approve" onclick=" window.location='<?php echo $_SERVER['PHP_SELF']; ?>?action=approve&BID=<?php echo $row['banner_id']; ?>&user_id=<?php echo $row['user_id']; ?>&order_id=<?php echo $row['order_id']; ?>&offset=<?php $_REQUEST['offset']; ?>&app=<?php echo $_REQUEST['app']; ?>&do_it_now='+document.form1.do_it_now.checked "><?php
						}

						if ( $row['approved'] != 'N' ) {
							?>
                            <input type="button" style="font-size: 9px;" value="Disapprove" onclick=" window.location='<?php echo $_SERVER['PHP_SELF']; ?>?action=disapprove&BID=<?php echo $row['banner_id']; ?>&user_id=<?php echo $row['user_id']; ?>&order_id=<?php echo $row['order_id']; ?>&offset=<?php $_REQUEST['offset']; ?>&app=<?php echo $_REQUEST['app']; ?>&do_it_now='+document.form1.do_it_now.checked "><?php
						}

						?>
	 </span></td>
            </tr>
			<?php
		}
		?>
    </table>
</form>
<?php
if ( $count > $records_per_page ) {
	// calculate number of pages & current page
	echo "<center>";
	$label["navigation_page"] = str_replace( "%CUR_PAGE%", $cur_page, $label["navigation_page"] );
	$label["navigation_page"] = str_replace( "%PAGES%", $pages, $label["navigation_page"] );
	//	echo "<span > ".$label["navigation_page"]."</span> ";
	$nav   = nav_pages_struct( $q_string, $count, $records_per_page );
	$LINKS = 40;
	render_nav_pages( $nav, $LINKS, $q_string );
	echo "</center>";
}
?>


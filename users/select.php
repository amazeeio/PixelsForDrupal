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

session_start();
include( "../config.php" );

include( "login_functions.php" );

process_login();

if ( isset( $_REQUEST['BID'] ) && ! empty( $_REQUEST['BID'] ) ) {
	if ( $f2->bid( $_REQUEST['BID'] ) != '' ) {
		$BID             = $f2->bid( $_REQUEST['BID'] );
		$_SESSION['BID'] = $BID;
	} else {
		$BID = $_SESSION['BID'];
	}
} else {
	$BID = 1;
}

if ( ! is_numeric( $BID ) ) {
	die();
}

$banner_data = load_banner_constants( $BID );

//Begin -J- Edit: Force New Order on load of page unless user clicked "Edit" button on confirm/complete page (indicated by $_GET['jEditOrder'])
//Important: This chunk was moved from below the "load order from php" section

if ( isset( $_REQUEST['banner_change'] ) && ! empty( $_REQUEST['banner_change'] ) ) {

	$sql = "SELECT * FROM orders where status='new' and banner_id='$BID' and user_id='" . intval( $_SESSION['MDS_ID'] ) . "'";

	$res = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

	while ( $row = mysqli_fetch_array( $res, MYSQLI_ASSOC ) ) {

		$sql = "delete from orders where order_id=" . intval( $row['order_id'] );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$sql = "delete from blocks where order_id=" . intval( $row['order_id'] );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}
}

# load order from php
# only allowed 1 new order per banner

$sql = "SELECT * from orders where user_id='" . intval( $_SESSION['MDS_ID'] ) . "' and status='new' and banner_id='$BID' ";

$order_result = mysqli_query( $GLOBALS['connection'], $sql );
$order_row    = mysqli_fetch_array( $order_result );

if ( ( $order_row['user_id'] != '' ) && $order_row['user_id'] != $_SESSION['MDS_ID'] ) { // do a test, just in case.
	die( 'you do not own this order!' );
}

if ( ( $_SESSION['MDS_order_id'] == '' ) || ( USE_AJAX == 'YES' ) ) { // guess the order id
	$_SESSION['MDS_order_id'] = $order_row['order_id'];
}

/*
old_order_id comes the form which allows users to change banners.

When the users change the grid, the order that was in-progress is deleted
from the system. The user can start making a new order for the new banner.

(Only one order-in-progress is allowed)

*/

if ( isset( $_REQUEST['banner_change'] ) && ! empty( $_REQUEST['banner_change'] ) ) {

	$_SESSION['MDS_order_id'] = ''; // clear the current order

	$sql = "SELECT * FROM orders where status='new' and banner_id='$BID' and user_id='" . intval( $_SESSION['MDS_ID'] ) . "'";
	$res = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );

	while ( $row = mysqli_fetch_array( $res, MYSQLI_ASSOC ) ) {
		$sql = "delete from orders where order_id=" . intval( $row['order_id'] );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
		$sql = "delete from blocks where order_id=" . intval( $row['order_id'] );
		$result = mysqli_query( $GLOBALS['connection'], $sql ) or die ( mysqli_error( $GLOBALS['connection'] ) . $sql );
	}

}

if ( isset( $_REQUEST['select'] ) && ! empty( $_REQUEST['select'] ) ) {

	if ( $_REQUEST['sel_mode'] == 'sel4' ) {

		$max_x = $banner_data['G_WIDTH'] * $banner_data['BLK_WIDTH'];
		$max_y = $banner_data['G_HEIGHT'] * $banner_data['BLK_HEIGHT'];

		$cannot_sel = select_block( $_REQUEST['map_x'], $_REQUEST['map_y'] );
		if ( ( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'] <= $max_x ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'], $_REQUEST['map_y'] );
		}
		if ( ( $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] <= $max_y ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'], $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] );
		}
		if ( ( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'] <= $max_x ) && ( $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] <= $max_y ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'], $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] );
		}

	} elseif ( $_REQUEST['sel_mode'] == 'sel6' ) {

		$max_x = $banner_data['G_WIDTH'] * $banner_data['BLK_WIDTH'];
		$max_y = $banner_data['G_HEIGHT'] * $banner_data['BLK_HEIGHT'];

		$cannot_sel = select_block( $_REQUEST['map_x'], $_REQUEST['map_y'] );

		if ( ( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'] <= $max_x ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'], $_REQUEST['map_y'] );
		}
		if ( ( $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] <= $max_y ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'], $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] );
		}
		if ( ( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'] <= $max_x ) && ( $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] <= $max_y ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'] + $banner_data['BLK_WIDTH'], $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] );
		}

		if ( ( $_REQUEST['map_x'] + ( $banner_data['BLK_WIDTH'] * 2 ) <= $max_x ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'] + ( $banner_data['BLK_WIDTH'] * 2 ), $_REQUEST['map_y'] );
		}

		if ( ( $_REQUEST['map_x'] + ( $banner_data['BLK_WIDTH'] * 2 ) <= $max_x ) && ( $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] <= $max_y ) ) {
			$cannot_sel = select_block( $_REQUEST['map_x'] + ( $banner_data['BLK_WIDTH'] * 2 ), $_REQUEST['map_y'] + $banner_data['BLK_HEIGHT'] );
		}

	} else {

		$cannot_sel = select_block( $_REQUEST['map_x'], $_REQUEST['map_y'] );

	}

}

require( "header.php" );

?>
    <div id="blocks"></div>

    <script>
		var selectedBlocks = [];
		var selBlocksIndex = 0;

		window.onresize = refreshSelectedLayers;
		window.onload = load_order;

		var grid_width = <?php echo $banner_data['G_WIDTH']?>;
		var grid_height = <?php echo $banner_data['G_HEIGHT']?>;

		var BLK_WIDTH = <?php echo $banner_data['BLK_WIDTH']?>;
		var BLK_HEIGHT = <?php echo $banner_data['BLK_HEIGHT']?>;
		var GRD_WIDTH = BLK_WIDTH * grid_width;
		var GRD_HEIGHT = BLK_HEIGHT * grid_height;

		function load_order() {
			<?php
			// get data for blocks in this order
			$order_blocks = array();

			if(isset( $order_row['blocks'] ) && $order_row['blocks'] != "") {

			$block_ids = explode( ',', $order_row['blocks'] );
			foreach ( $block_ids as $block_id ) {
				$pos            = get_block_position( $block_id, $BID );
				$order_blocks[] = array(
					'block_id' => $block_id,
					'x'        => $pos['x'],
					'y'        => $pos['y'],
				);
			}

			// load any existing blocks for this order
			?>
			var blocks = JSON.parse('<?php echo json_encode( $order_blocks ); ?>');
			for (var i = 0; i < blocks.length; i++) {
				add_block(blocks[i].block_id, blocks[i].x, blocks[i].y);
			}
			<?php
			}
			?>

			const form1 = document.getElementById('form1');
			form1.addEventListener('submit', form1Submit);
		}

		function update_order() {
			document.form1.selected_pixels.value = block_str;
		}

		function reserve_block(clicked_block, OffsetX, OffsetY) {
			var blocks;
			if (block_str !== '') {
				blocks = block_str.split(",");

			} else {
				blocks = [];
			}

			var len = blocks.length;
			len++;
			blocks[len] = clicked_block;
			block_str = implode(blocks);
		}

		function unreserve_block(clicked_block, OffsetX, OffsetY) {

			var blocks;
			if (block_str !== '') {
				blocks = block_str.split(",");

			} else {
				blocks = [];

			}
			var new_blocks = [];

			for (var i = 0; i < blocks.length; i++) {
				if (blocks[i] !== clicked_block) {
					new_blocks[i] = blocks[i];
				}
			}

			block_str = implode(new_blocks);
		}

		//Begin -J- Edit: Custom functions for resize bug
		//Taken from http://www.quirksmode.org/js/findpos.html; but modified
		function findPosX(obj) {
			var curleft = 0;
			if (obj.offsetParent) {
				while (obj.offsetParent) {
					curleft += obj.offsetLeft;
					obj = obj.offsetParent;
				}
			} else if (obj.x)
				curleft += obj.x;
			return curleft;
		}

		//Taken from http://www.quirksmode.org/js/findpos.html; but modified
		function findPosY(obj) {
			var curtop = 0;
			if (obj.offsetParent) {
				while (obj.offsetParent) {
					curtop += obj.offsetTop;
					obj = obj.offsetParent;
				}
			} else if (obj.y)
				curtop += obj.y;
			return curtop;
		}

		function refreshSelectedLayers() {
			var grid = document.getElementById("pixelimg"); //Get image grid element
			var gridLeft = findPosX(grid); //Get image grid's new X position
			var gridTop = findPosY(grid); //Get image grid's new Y position
			var layer; //Used to hold layer elements below
			for (var i = 0; i < selectedBlocks.length; i++) //Loop through selectedBlocks array
			{
				if (selectedBlocks[i] !== '') //If spot isn't empty
				{
					layer = document.getElementById("block" + selectedBlocks[i]); //Get layer element given blockID stored in selectedBlocks array
					if (layer !== null) {
						//Update layer relative to new pos of image grid
						layer.style.left = gridLeft + parseFloat(layer.getAttribute("tempLeft"));
						layer.style.top = gridTop + parseFloat(layer.getAttribute("tempTop"));
					}
				} //End of if(selectedBlockIDs[i] != ''....
			} //End for loop
		} //End testing()
		//End -J- Edit: Custom functions for resize bug

		function refresh_grid() {
			var grid = document.getElementById("pixelimg");
			var gridsrc = grid.getAttribute("src");
			grid.setAttribute("src", gridsrc);
		}

		function add_block(clicked_block, OffsetX, OffsetY) {

			var myblock = document.getElementById('blocks');
			var pixelimg = document.getElementById('pixelimg');
			//-J- Edit: Added tempTop and tempLeft values to span tag for resize bug
			myblock.innerHTML = myblock.innerHTML + "<span id='block" + clicked_block.toString() + "' tempTop=" + OffsetY + " tempLeft=" + OffsetX + " style='top: " + (OffsetY + pixelimg.offsetTop) + "px; left: " + (OffsetX + pixelimg.offsetLeft) + "px;' onclick='change_block_state(" + OffsetX + ", " + OffsetY + ");' onmousemove='show_pointer2(this, event)' ><img src='selected_block.png' width='<?php echo $banner_data['BLK_WIDTH']; ?>' height='<?php echo $banner_data['BLK_HEIGHT']; ?>'></span>";
			//Begin -J- Edit: For resize bug
			selectedBlocks[selBlocksIndex] = clicked_block;
			selBlocksIndex = selBlocksIndex + 1;
			//End -J- Edit

			reserve_block(clicked_block, OffsetX, OffsetY);
		}

		function remove_block(clicked_block, OffsetX, OffsetY) {
			var myblock = document.getElementById("block" + clicked_block.toString());
			if (myblock !== null) {
				myblock.remove();
			}

			unreserve_block(clicked_block, OffsetX, OffsetY);
		}

		function invert_block(clicked_block, OffsetX, OffsetY) {
			var myblock = document.getElementById("block" + clicked_block.toString());
			if (myblock !== null) {
				remove_block(clicked_block, OffsetX, OffsetY);
			} else {
				add_block(clicked_block, OffsetX, OffsetY);
			}
		}

		function invert_blocks(clicked_block, OffsetX, OffsetY) {

			// invert block
			invert_block(clicked_block, OffsetX, OffsetY);
		}

		// Initialize
		var block_str = "<?php echo $order_row['blocks']; ?>";
		var selecting = false;
		var trip_count = 0;

		function select_pixels(e) {
			e.preventDefault();
			e.stopPropagation();

			if (selecting) {
				return false;
			}
			selecting = true;

			// cannot select while AJAX is in action
			if (document.getElementById('submit_button1').disabled) {
				selecting = false;
				return false;
			}

			var pointer = document.getElementById('block_pointer');
			pointer.style.visibility = 'hidden';
			var OffsetX = pointer.map_x;
			var OffsetY = pointer.map_y;

			trip_count=1; // default

			if (document.getElementById('sel4').checked){
				// select 4 at a time
				trip_count=4;
				change_block_state(OffsetX, OffsetY);
				change_block_state(OffsetX+BLK_WIDTH, OffsetY);
				change_block_state(OffsetX, OffsetY+BLK_HEIGHT);
				change_block_state(OffsetX+BLK_WIDTH, OffsetY+BLK_HEIGHT);

			} else {

				if  (document.getElementById('sel6').checked) {

					trip_count=6;
					change_block_state(OffsetX, OffsetY);
					change_block_state(OffsetX+BLK_WIDTH, OffsetY);
					change_block_state(OffsetX, OffsetY+BLK_HEIGHT);
					change_block_state(OffsetX+BLK_WIDTH, OffsetY+BLK_HEIGHT);
					change_block_state(OffsetX+(BLK_WIDTH*2), OffsetY);
					change_block_state(OffsetX+(BLK_WIDTH*2), OffsetY+BLK_HEIGHT);

				} else {
					trip_count=1;
					change_block_state(OffsetX, OffsetY);
				}

			}

			selecting = false;
			return true;
		}

		/**
		 * @return {boolean}
		 */
		function IsNumeric(str) {
			var ValidChars = "0123456789";
			var IsNumber = true;
			var Char;

			for (var i = 0; i < str.length && IsNumber === true; i++) {
				Char = str.charAt(i);
				if (ValidChars.indexOf(Char) === -1) {
					IsNumber = false;
				}
			}
			return IsNumber;

		}

		function get_block_position(block_id) {

			var cell = "0";
			var ret = {};
			ret.x = 0;
			ret.y = 0;

			for (var i = 0; i < grid_height; i++) {
				for (var j = 0; j < grid_width; j++) {
					if (block_id === cell) {
						ret.x = j * BLK_WIDTH;
						ret.y = i * BLK_HEIGHT;
						return ret;

					}
					cell++;
				}
			}

			return ret;
		}

		function get_block_id_from_position(x, y) {

			var id = 0;
			for (var y2 = 0; y2 < GRD_HEIGHT; y2 += BLK_HEIGHT) {
				for (var x2 = 0; x2 < GRD_WIDTH; x2 += BLK_WIDTH) {
					if (x === x2 && y === y2) {
						return id;
					}
					id++;
				}
			}

			return id;
		}

		function change_block_state(OffsetX, OffsetY) {

			var clicked_block = ((OffsetX) / BLK_WIDTH) + ((OffsetY / BLK_HEIGHT) * (GRD_WIDTH / BLK_WIDTH));
			var pointer = document.getElementById('block_pointer');
			var pixelimg = document.getElementById('pixelimg');

			var xmlhttp = false;
			if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
				xmlhttp = new XMLHttpRequest();
			}

			xmlhttp.open("GET", "update_order.php?user_id=<?php echo $_SESSION['MDS_ID'];?>&block_id=" + clicked_block.toString() + "&BID=<?php echo $BID . "&t=" . time(); ?>", true);

			if (trip_count !== 0) { // trip_count: global variable counts how many times it goes to the server
				document.getElementById('submit_button1').disabled = true;
				document.getElementById('submit_button2').disabled = true;
				pointer.style.cursor = 'wait';
				pixelimg.style.cursor = 'wait';
			}

			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState === 4) {
					pointer = document.getElementById('block_pointer');
					pixelimg = document.getElementById('pixelimg');

					if ((xmlhttp.responseText === 'new')) {
						refresh_grid();
						invert_blocks(clicked_block, OffsetX, OffsetY);

					} else {
						if (IsNumeric(xmlhttp.responseText)) {

							// save order id
							document.form1.order_id.value = xmlhttp.responseText;

							refresh_grid();

							invert_blocks(clicked_block, OffsetX, OffsetY);

						} else {

							if (xmlhttp.responseText.indexOf('max_selected') > -1) {
								<?php
								$label['max_blocks_selected'] = str_replace( '%MAX_BLOCKS%', $banner_data['G_MAX_BLOCKS'], $label['max_blocks_selected'] );
								?>
								alert('<?php echo js_out_prep( $label['max_blocks_selected'] ); ?> ');
							} else if (xmlhttp.responseText.indexOf('max_orders') > -1) {
								alert('<?php echo js_out_prep( $label['advertiser_max_order'] )?>');
							} else if (xmlhttp.responseText.length > 0) {
								alert(xmlhttp.responseText);
							}
						}
					}

					update_order();
					trip_count--; // count down, enable button when 0

					if (trip_count <= 0) {
						document.getElementById('submit_button1').disabled = false;
						document.getElementById('submit_button2').disabled = false;
						pointer.style.cursor = 'pointer';
						pixelimg.style.cursor = 'pointer';
						trip_count = 0;
					}
				}

			};

			xmlhttp.send(null);
		}

		function implode(myArray) {

			var str = '';
			var comma = '';

			for (var i in myArray) {
				str = str + comma + myArray[i];
				comma = ',';
			}

			return str;
		}

		var pos;

		function getObjCoords(obj) {
			var pos = {x: 0, y: 0};
			var curtop = 0;
			var curleft = 0;
			if (obj.offsetParent) {
				while (obj.offsetParent) {
					curtop += obj.offsetTop;
					curleft += obj.offsetLeft;
					obj = obj.offsetParent;
				}
			} else if (obj.y) {
				curtop += obj.y;
				curleft += obj.x;
			}
			pos.x = curleft;
			pos.y = curtop;
			return pos;
		}

		function show_pointer(e) {
			var pixelimg = document.getElementById('pixelimg');

			if (!pos) {
				var pos = getObjCoords(pixelimg);
			}

			var OffsetX;
			var OffsetY;
			if (e.offsetX) {
				OffsetX = e.offsetX;
				OffsetY = e.offsetY;
			} else {
				OffsetX = e.pageX - pos.x;
				OffsetY = e.pageY - pos.y;
			}

			// drop 1/10 from the OffsetX and OffsetY, eg 612 becomes 610

			OffsetX = Math.floor(OffsetX / <?php echo $banner_data['BLK_WIDTH']; ?>) *<?php echo $banner_data['BLK_WIDTH']; ?>;
			OffsetY = Math.floor(OffsetY / <?php echo $banner_data['BLK_HEIGHT']; ?>) *<?php echo $banner_data['BLK_HEIGHT']; ?>;

			var pointer = document.getElementById('block_pointer');

			pointer.style.visibility = 'visible';
			pointer.style.display = 'block';

			pointer.style.top = (pos.y + OffsetY) + "px";
			pointer.style.left = (pos.x + OffsetX) + "px";

			pointer.map_x = OffsetX;
			pointer.map_y = OffsetY;

			return true;
		}

		function show_pointer2(block, e) {
			var pointer = document.getElementById('block_pointer');
			pointer.style.visibility = 'hidden';
		}

		function form1Submit(event) {
			event.preventDefault();
			event.stopPropagation();

			var myblocks = document.getElementById('blocks');

			if (myblocks.innerHTML.trim() === '') {
				alert("<?php echo $label['no_blocks_selected'] ?>");
				return false;
			} else {
				document.form1.submit();
			}
		}
    </script>

    <style>
        #block_pointer {
            padding: 0;
            margin: 0;
            cursor: pointer;
            position: absolute;
            left: 0;
            top: 0;
            background-color: #FFFFFF;
            visibility: hidden;
            height: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
            width: <?php echo $banner_data['BLK_WIDTH']; ?>px;
            line-height: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
            font-size: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
        }

        span[id^='block'] {
            padding: 0;
            margin: 0;
            cursor: pointer;
            position: absolute;
            background-color: #FFFFFF;
            width: <?php echo $banner_data['BLK_WIDTH']; ?>px;
            height: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
            line-height: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
            font-size: <?php echo $banner_data['BLK_HEIGHT']; ?>px;
        }
    </style>

    <span onmouseout="this.style.visibility='hidden' " id='block_pointer' onclick="select_pixels(event);"><img src='pointer.png' width="<?php echo $banner_data['BLK_WIDTH']; ?>" height="<?php echo $banner_data['BLK_HEIGHT']; ?>" alt=""></span>

    <p>
		<?php echo $label['advertiser_sel_trail']; ?>
    </p>

    <p id="select_status"><?php echo $cannot_sel; ?></p>

<?php

$sql = "SELECT * FROM banners order by `name`";
$res = mysqli_query( $GLOBALS['connection'], $sql );

if ( mysqli_num_rows( $res ) > 1 ) {
	?>
    <div class="fancy_heading" style="width:85%;"><?php echo $label['advertiser_sel_pixel_inv_head']; ?></div>
    <p>
		<?php
		$label['advertiser_sel_select_intro'] = str_replace( "%IMAGE_COUNT%", mysqli_num_rows( $res ), $label['advertiser_sel_select_intro'] );
		echo $label['advertiser_sel_select_intro'];
		?>
    </p>
    <p>
		<?php display_banner_selecton_form( $BID, $_SESSION['MDS_order_id'], $res ); ?>
    </p>
	<?php
}

if ( isset( $order_exists ) && $order_exists ) {
	echo "<p>" . $label['advertiser_order_not_confirmed'] . "</p>";
}

$has_packages = banner_get_packages( $BID );
if ( $has_packages ) {
	display_package_options_table( $BID, '', false );

} else {
	display_price_table( $BID );
}
?>
    <div class="fancy_heading" style="width:85%;"><?php echo $label['advertiser_select_pixels_head']; ?></div>
<?php
$label['advertiser_select_instructions2'] = str_replace( '%PIXEL_C%', $banner_data['BLK_HEIGHT'] * $banner_data['BLK_WIDTH'], $label['advertiser_select_instructions2'] );
$label['advertiser_select_instructions2'] = str_replace( '%BLK_HEIGHT%', $banner_data['BLK_HEIGHT'], $label['advertiser_select_instructions2'] );
$label['advertiser_select_instructions2'] = str_replace( '%BLK_WIDTH%', $banner_data['BLK_WIDTH'], $label['advertiser_select_instructions2'] );
echo $label['advertiser_select_instructions2']; ?>

    <form method="post" action="select.php" name='pixel_form'>
        <input type="hidden" name="jEditOrder" value="true">
        <p><b><?php echo $label['selection_mode']; ?></b> <input type="radio" id='sel1' name='sel_mode' value='sel1' <?php if ( ( $_REQUEST['sel_mode'] == '' ) || ( $_REQUEST['sel_mode'] == 'sel1' ) ) {
				echo " checked ";
			} ?> > <label for='sel1'><?php echo $label['select1']; ?></label> | <input type="radio" name='sel_mode' id='sel4' value='sel4' <?php if ( ( $_REQUEST['sel_mode'] == 'sel4' ) ) {
				echo " checked ";
			} ?> > <label for="sel4"><?php echo $label['select4']; ?></label> | <input type="radio" name='sel_mode' id='sel6' value='sel6' <?php if ( ( $_REQUEST['sel_mode'] == 'sel6' ) ) {
				echo " checked ";
			} ?> > <label for="sel6"><?php echo $label['select6']; ?></label>
        </p>
        <p>
            <input type="button" name='submit_button1' id='submit_button1' value='<?php echo htmlspecialchars( $label['advertiser_buy_button'] ); ?>' onclick='document.form1.submit()'>
        </p>

        <input type="hidden" value="1" name="select">
        <input type="hidden" value="<?php echo $BID; ?>" name="BID">

        <input style="cursor: pointer;outline:none;border:none;" id="pixelimg" <?php if ( USE_AJAX == 'YES' ) { ?> onmousemove="show_pointer(event)" onclick="if (select_pixels(event)) return false;" <?php } ?> type="image" name="map" value='Select Pixels.' style="width:<?php echo $banner_data['G_WIDTH'] * $banner_data['BLK_WIDTH']; ?>px;height:<?php echo $banner_data['G_HEIGHT'] * $banner_data['BLK_HEIGHT']; ?>;border:none;outline:none;" src="show_selection.php?BID=<?php echo $BID; ?>&gud=<?php echo time(); ?>" alt=""/>

        <input type="hidden" name="action" value="select">
    </form>
    <div style='background-color: #ffffff; border-color:#C0C0C0; border-style:solid;padding:10px'>
        <hr>

        <form method="post" action="order.php" name="form1">
            <input type="hidden" name="package" value="">
            <input type="hidden" name="selected_pixels" value=''>
            <input type="hidden" name="order_id" value="<?php echo $_SESSION['MDS_order_id']; ?>">
            <input type="hidden" value="<?php echo $BID; ?>" name="BID">
            <input type="submit" name='submit_button2' id='submit_button2' value='<?php echo htmlspecialchars( $label['advertiser_buy_button'] ); ?>'>
            <hr>
        </form>

        <script>
			document.form1.selected_pixels.value = block_str;
        </script>

    </div>

<?php require "footer.php"; ?>
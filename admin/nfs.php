<?php
/**
 * @version		$Id: nfs.php 170 2013-08-25 13:32:36Z ryan $
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

require("../config.php");

require ('admin_common.php');

ini_set('max_execution_time', 10000);
ini_set('max_input_vars', 10002);
if ($_REQUEST['pass']!='') {
	if ($_REQUEST['pass']==ADMIN_PASSWORD) {
		$_SESSION['ADMIN'] = '1';
	}
}
if ($_SESSION['ADMIN']=='') {
	?>
	Please input admin password:<br>
	<form method='post'>
	<input type="password" name='pass'>
	<input type="submit" value="OK">
	</form>
	<?php
	die();
}

$BID = $f2->bid($_REQUEST['BID']);

load_banner_constants($BID);

if ($_REQUEST['action']=='save') {
	//$sql = "delete from blocks where status='nfs' AND banner_id=$BID ";
	//mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);
	
	if(isset($_REQUEST['addnfs'])) {
		$addnfs = explode("~", $_REQUEST['addnfs']);
	} else {
		unset($addnfs);
	}
	if(isset($_REQUEST['remnfs'])) {
		$remnfs = explode("~", $_REQUEST['remnfs']);
	} else {
		unset($remnfs);
	}

	$cell = $x = $y = "0";
		for ($i = 0; $i < G_HEIGHT; $i++) {
			$x = "0";
			for ($j = 0; $j < G_WIDTH; $j++) {
				if (isset($addnfs) && in_array($cell, $addnfs)) {
					$sql = "REPLACE INTO blocks (block_id, status, x, y, banner_id) VALUES ($cell, 'nfs', $x, $y, $BID)";
					mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']) . $sql);
				} else if (isset($remnfs) && in_array($cell, $remnfs)) {
					$sql = "DELETE FROM blocks WHERE status='nfs' AND banner_id=$BID AND block_id=$cell";
					mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']) . $sql);
				}
				$x = $x + BLK_WIDTH;
				$cell++;
			}
			$y = $y + BLK_HEIGHT;
		}
		echo "Success!";
	exit();
}
?>
<script src="js/jquery.min.js"></script>
<script src="jquery-ui/jquery-ui.min.js"></script>
<link rel="stylesheet" href="jquery-ui/jquery-ui.min.css" type="text/css" />

<script type="text/javascript">
jQuery(function($){
	var addnfs = [];
	var remnfs = [];
	function processBlock(block) {
		if(block.hasClass("nfs")) {
			block.removeClass("nfs").addClass("free");
			var blockid = block.attr("data-block");
			remnfs.push(blockid);
			var index = addnfs.indexOf(blockid);
			if(index != -1) {
				addnfs.splice(index, 1);
			}
		} else if(block.hasClass("free")) {
			block.removeClass("free").addClass("nfs");
			var blockid = block.attr("data-block");
			addnfs.push(blockid);
			var index = remnfs.indexOf(blockid);
			if(index != -1) {
				remnfs.splice(index, 1);
			}
		} else if(!block.hasClass("free")) {
			block.removeClass("ui-selected");
		}
	};
	$('.grid').selectable({
		delay: 75,
		distance: <?php echo BLK_WIDTH; ?>,
		stop: function() {
			$( ".ui-selected", this ).each(function() {
				processBlock($(this));
			});
		}
	});
	$(".block").click(function() {
		processBlock($(this));
	});
	$('.save').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).after('<img class="loading" width="16" height="16" src="../images/ajax-loader.gif" alt="Loading..." />');
		$('.save').prop('disabled', true);
		var posting = $.post( "nfs.php", {
			BID: <?php echo $BID; ?>,
			action: "save",
			addnfs: addnfs.join('~'),
			remnfs: remnfs.join('~')
		});
		posting.done(function( data ) {
			$('.loading').hide(function() {
				$(this).remove();
				$('<span class="message">'+data+'</span>').insertAfter('.save').fadeOut(10000, function() {$(this).remove();});
			});
			$('.save').prop('disabled', false);
		});
	});
});
</script>
<style type="text/css">
	<?php
	$grid_background = "";
	if(file_exists(__DIR__ . "/temp/background$BID.png")) {
		$grid_background = 'background: url("temp/background'.$BID.'.png");';
	}
	?>
	.grid {
		<?php echo $grid_background; ?>
		z-index:0;
		width:<?php echo G_WIDTH*BLK_WIDTH; ?>px;
		height:<?php echo G_HEIGHT*BLK_HEIGHT; ?>px;
	}
	.block_row {
		clear:both;
		display:block;
	}
	.block {
		white-space:nowrap;
		width:<?php echo BLK_WIDTH; ?>px;
		height:<?php echo BLK_HEIGHT; ?>px;
		float:left;
	}
	.sold {
		background:url("../users/sold_block.png") no-repeat;
	}
	.reserved {
		background:url("../users/reserved_block.png") no-repeat;
	}
	.nfs {
		background:url("../users/not_for_sale_block.png") no-repeat;
		cursor: pointer;
	}
	.ordered {
		background:url("../users/ordered_block.png") no-repeat;
	}
	.ordered {
		background:url("../users/ordered_block.png") no-repeat;
	}
	.onorder {
		background:url("../users/not_for_sale_block.png") no-repeat;
	}
	.free {
		background:url("../users/block.png") no-repeat;
		cursor: pointer;
	}
	.grid .ui-selecting { background: #FECA40; }
</style>
</head>
<body>
<div class="outer_box">
	<p>
	Here you can mark blocks to be not for sale. You can drag to select an area. Click 'Save' when done.
	</p>
	(Note: If you have a background image, the image is blended in using the browser's built-in filter - your alpha channel is ignored on this page)
	<hr>
	<?php
	$sql = "Select * from banners ";
	$res = mysqli_query($GLOBALS['connection'], $sql);
	?>
	<form name="bidselect" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		Select grid:
		<select name="BID" onchange="document.bidselect.submit()">
			<option> </option>
			<?php
			while ($row=mysqli_fetch_array($res)) {
				if (($row['banner_id']==$BID) && ($f2->bid($_REQUEST['BID'])!='all')) {
					$sel = 'selected';
				} else {
					$sel ='';
				}
				echo '<option '.$sel.' value='.$row['banner_id'].'>'.$row[name].'</option>';
			}
			?>
		</select>
	</form>
	<hr>
	<?php
	if ($BID !='') {
		$sql = "show columns from blocks ";
		$result = mysqli_query($GLOBALS['connection'], $sql);
		while ($row=mysqli_fetch_array($result)) {
			if ($row['Field']=='status') {
				if (strpos($row['Type'], 'nfs')==0) {
					$sql = "ALTER TABLE `blocks` CHANGE `status` `status` SET( 'reserved', 'sold', 'free', 'ordered', 'nfs' ) NOT NULL ";
					 mysqli_query($GLOBALS['connection'], $sql) or die ("<p><b>CANNOT UPGRADE YOUR DATABASE!<br>Please run the follwoing query manually from PhpMyAdmin:</b><br>$sql<br>");
				}
			}
		}
		$sql = "select block_id, status, user_id FROM blocks WHERE banner_id=$BID";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);
		while ($row=mysqli_fetch_array($result)) {
			$blocks[$row["block_id"]] = $row['status'];

		}
	?>
	<div class="container">
		<input class="save" type="submit" value='Save Not for Sale' />
		<div class="grid">
			<?php
			$cell="0";
			for ($i=0; $i < G_HEIGHT; $i++) {
				echo "<div class='block_row'>";
				for ($j=0; $j < G_WIDTH; $j++) {
					switch ($blocks[$cell]) {
						case 'sold':
							echo '<span class="block sold" data-block="'.$cell.'"></span>';
							break;
						case 'reserved':
							echo '<span class="block reserved" data-block="'.$cell.'"></span>';
							break;
						case 'nfs':
							echo '<span class="block nfs" data-block="'.$cell.'"></span>';
							break;
						case 'ordered':
							echo '<span class="block ordered" data-block="'.$cell.'"></span>';
							break;
						case 'onorder':
							echo '<span class="block onorder" data-block="'.$cell.'"></span>';
							break;
						case 'free':
						case '':
							echo '<span class="block free" data-block="'.$cell.'"></span>';
					}
					$cell++;
				}
				echo '</div>
				';
			}
			?>
		</div>
		<input class="save" type="submit" value='Save Not for Sale' />
	</div>
</div>
<?php
}

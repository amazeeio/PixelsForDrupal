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

require_once ('lists.inc.php');
require_once ('dynamic_forms.php');
global $ad_tag_to_field_id;
global $ad_tag_to_search;
	$ad_tag_to_search = tag_to_search_init(1);
	$ad_tag_to_field_id = ad_tag_to_field_id_init();


#####################################

function ad_tag_to_field_id_init () {
	global $label;

	//$sql = "SELECT *, t2.field_label AS NAME FROM `form_fields` as t1, form_field_translations as t2 where t1.field_id = t2.field_id AND t2.lang='".$_SESSION['MDS_LANG']."' AND form_id=1 ORDER BY list_sort_order ";
	$sql = "SELECT * FROM `form_fields`, form_field_translations WHERE form_fields.field_id = form_field_translations.field_id AND form_field_translations.lang='".get_lang()."' AND form_id=1 ORDER BY list_sort_order ";
	$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));
	# do a query for each field
	while ($fields = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

		//$form_data = $row[]
		$tag_to_field_id[$fields['template_tag']]['field_id'] = $fields['field_id'];
		$tag_to_field_id[$fields['template_tag']]['field_type'] = $fields['field_type'];
		$tag_to_field_id[$fields['template_tag']]['field_label'] = $fields['field_label'];
	}

	$tag_to_field_id["ORDER_ID"]['field_id'] = 'order_id';
	$tag_to_field_id["ORDER_ID"]['field_label'] = 'Order ID';
	//$tag_to_field_id["ORDER_ID"]['field_label'] = $label["employer_resume_list_date"];

	$tag_to_field_id["BID"]['field_id'] = 'banner_id';
	$tag_to_field_id["BID"]['field_label'] = 'Grid ID';

	$tag_to_field_id["USER_ID"]['field_id'] = 'user_id';
	$tag_to_field_id["USER_ID"]['field_label'] = 'User ID';

	$tag_to_field_id["AD_ID"]['field_id'] = 'ad_id';
	$tag_to_field_id["AD_ID"]['field_label'] = 'Ad ID';

	$tag_to_field_id["DATE"]['field_id'] = 'ad_date';
	$tag_to_field_id["DATE"]['field_label'] = 'Date';

	return $tag_to_field_id;

}

######################################################################

function load_ad_values ($ad_id) {

	global $f2;

	$prams = array();

	$ad_id = intval($ad_id);

	$sql = "SELECT * FROM `ads` WHERE ad_id='$ad_id'   ";

	$result = mysqli_query($GLOBALS['connection'], $sql) or die ($sql. mysqli_error($GLOBALS['connection']));

	if ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		
		$prams['ad_id'] = $ad_id;
		$prams['user_id'] = $row['user_id'];
		$prams['order_id'] = $row['order_id'];
		$prams['banner_id'] = $row['banner_id'];

		$sql = "SELECT * FROM form_fields WHERE form_id=1 AND field_type != 'SEPERATOR' AND field_type != 'BLANK' AND field_type != 'NOTE' ";
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));
		while ($fields = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

			$prams[$fields['field_id']] =  $row[$fields['field_id']];

			if ($fields['field_type']=='DATE')  {
				$day = $_REQUEST[$row['field_id']."d"];
				$month = $_REQUEST[$row['field_id']."m"];
				$year = $_REQUEST[$row['field_id']."y"];

				$prams[$fields['field_id']] = "$year-$month-$day";

			} elseif (($fields['field_type']=='MSELECT') || ($fields['field_type']=='CHECK'))  {
				if (is_array($_REQUEST[$row['field_id']])) {	
					$prams[$fields['field_id']] = implode (",", $_REQUEST[$fields['field_id']]);
				} else {
					$prams[$fields['field_id']] = $_REQUEST[$fields['field_id']];
				}
				
			}

		}
		return $prams;
	} else {
		return false;
	}

}

#########################################################

function assign_ad_template($prams) {

	global $label;

	$str = $label['mouseover_ad_template'];

	$sql = "SELECT * FROM form_fields WHERE form_id='1' AND field_type != 'SEPERATOR' AND field_type != 'BLANK' AND field_type != 'NOTE' ";
		//echo $sql;
	$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		
		if ($row['field_type']=='IMAGE') {
			if ((file_exists(UPLOAD_PATH.'images/'.$prams[$row['field_id']]))&&($prams[$row['field_id']])) {
				$str = str_replace('%'.$row['template_tag'].'%', '<img alt="" src="'. UPLOAD_HTTP_PATH."images/".$prams[$row['field_id']].'" >', $str);
			} else {
				//$str = str_replace('%'.$row['template_tag'].'%',  '<IMG SRC="'.UPLOAD_HTTP_PATH.'images/no-image.gif" WIDTH="150" HEIGHT="150" BORDER="0" ALT="">', $str);
				$str = str_replace('%'.$row['template_tag'].'%',  '', $str);
			}
		} else {
			$str = str_replace('%'.$row['template_tag'].'%', get_template_value($row['template_tag'],1), $str);
		} 
 
		$str = str_replace('$'.$row['template_tag'].'$', get_template_field_label($row['template_tag'],1), $str);
		
	}
	return $str;
}

#########################################################

function display_ad_form ($form_id, $mode, $prams) {

	global $f2, $label, $error, $BID;

	if ($prams == '' ) {
        $prams = array();
		$prams['mode'] = (isset($_REQUEST['mode']) ? $_REQUEST['mode'] : "");
		$prams['ad_id']= (isset($_REQUEST['ad_id']) ? $_REQUEST['ad_id'] : "");
		$prams['banner_id'] = $BID;
		$prams['user_id'] = (isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : "");

		$sql = "SELECT * FROM form_fields WHERE form_id='".intval($form_id)."' AND field_type != 'SEPERATOR' AND field_type != 'BLANK' AND field_type != 'NOTE' ";
		//echo $sql;
		$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {

			//$prams[$row[field_id]] = $_REQUEST[$row[field_id]];

			if ($row['field_type']=='DATE')  {
				$day = $_REQUEST[$row['field_id']."d"];
				$month = $_REQUEST[$row['field_id']."m"];
				$year = $_REQUEST[$row['field_id']."y"];
				$prams[$row['field_id']] = "$year-$month-$day";

			} elseif (($row['field_type']=='MSELECT') || ($row['field_type']=='CHECK'))  {
				if (is_array($_REQUEST[$row['field_id']])) {	
					$prams[$row['field_id']] = implode (",", $_REQUEST[$row['field_id']]);
				} else {
					$prams[$row['field_id']] = $_REQUEST[$row['field_id']];
				}
				
			} else {
				$prams[$row['field_id']] = stripslashes (isset($_REQUEST[$row['field_id']]) ? $_REQUEST[$row['field_id']] : "");
			}
		}
 	}

	$mode = (isset($mode) && in_array($mode, array("edit", "user"))) ? $mode : "";
	$ad_id = isset($prams['ad_id']) ? intval($prams['ad_id']) : "";
	$user_id = isset($prams['user_id']) ? intval($prams['user_id']) : "";
	$order_id = isset($prams['order_id']) ? intval($prams['order_id']) : "";
	$banner_id = isset($prams['banner_id']) ? intval($prams['banner_id']) : "";
	?>
	<form method="POST"  action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" name="form1" onsubmit=" form1.savebutton.disabled=true;" enctype="multipart/form-data">
	
	<input type="hidden" name="mode" size="" value="<?php echo $mode; ?>">
	<input type="hidden" name="ad_id" size="" value="<?php echo $ad_id; ?>">
	<input type="hidden" name="user_id" size="" value="<?php echo $user_id; ?>">
	<input type="hidden" name="order_id" size="" value="<?php echo $order_id; ?>">
	<input type="hidden" name="BID" size="" value="<?php echo $banner_id; ?>">

	<?php  if (($error != '' ) && ($mode!='edit')) { ?>
	<?php  echo "<div class='alert alert-danger'><i>".$label['ad_save_error']."</i><br><b>".$error."</b></div>";  ?>
	<?php } ?>
	<?php if ($mode == "edit") {
					echo "[Ad Form]";
				}
		 // section 1
		display_form ($form_id, $mode, $prams, 1);
	?>

		<input type="hidden" name="save" id="save101" value="">
		<?php if ($mode=='edit' || $mode == 'user') { ?>
		    <div class="text-right mt-2">
		        <input class="btn btn-primary" type="submit" name="savebutton" value="<?php echo $label['ad_save_button'];?>" onClick="save101.value='1';">
		    </div>
		<?php } ?>
	</form>

	<?php

}

###########################################################################

function list_ads ($admin=false, $order="", $offset=0, $list_mode='ALL', $user_id='') {

	## Globals
	global $BID, $f2, $label, $tag_to_field_id, $ad_tag_to_field_id, $action;
	$tag_to_field_id = ad_tag_to_field_id_init();

    $records_per_page = 40;

   // process search result
   $q_string = "";
   $where_sql = "";
	if ($_REQUEST['action'] == 'search') {
		$q_string = generate_q_string(1);  	   
		$where_sql = generate_search_sql(1);
	}

	$order = $_REQUEST['order_by'];

	if ($_REQUEST['ord']=='asc') {
		$ord = 'ASC';
	} elseif ($_REQUEST['ord']=='desc') {
		$ord = 'DESC';
	} else {
		$ord = 'DESC'; // sort descending by default
	}

	if ($order == null || $order == '') {
		$order = " `ad_date` ";           
	} else {
		$order = " `".mysqli_real_escape_string( $GLOBALS['connection'], $order)."` ";
	}

	if ($list_mode == 'USER' ) {

		if (!is_numeric($user_id)) {
			$user_id = $_SESSION['MDS_ID'];
		} 

		$sql = "Select *  FROM `ads`, `orders` WHERE ads.ad_id=orders.ad_id AND ads.order_id > 0 AND ads.banner_id='".intval($BID)."' AND ads.user_id='".intval($user_id)."' AND (orders.status IN ('pending','completed','confirmed','new','expired','renew_wait','renew_paid')) $where_sql ORDER BY $order $ord ";

	} elseif ($list_mode =='TOPLIST') {

	//	$sql = "SELECT *, DATE_FORMAT(MAX(order_date), '%Y-%c-%d') as max_date, sum(quantity) AS pixels FROM orders, ads where ads.order_id=orders.order_id AND status='completed' and orders.banner_id='$BID' GROUP BY orders.user_id, orders.banner_id order by pixels desc ";

	} else {
		
		//$sql = "Select *  FROM `ads` as t1, `orders` AS t2 WHERE t1.ad_id=t2.ad_id AND t1.banner_id='$BID' and t1.order_id > 0 $where_sql ORDER BY $order $ord ";
		$sql = "Select *  FROM `ads`, `orders` WHERE ads.ad_id=orders.ad_id AND ads.banner_id='".intval($BID)."' and ads.order_id > 0 AND orders.status != 'deleted' $where_sql ORDER BY $order $ord ";

	}

	//echo "[".$sql."]";

	$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));
	############
	# get the count
	$count = mysqli_num_rows($result);

	if ($count > $records_per_page) {

		mysqli_data_seek($result, $offset);

	}

	if ($count > 0 )  {

		if ($list_mode!='USER') {

			$pages = ceil($count / $records_per_page);
			$cur_page = $_REQUEST['offset'] / $records_per_page;
			$cur_page++;

			echo "<center>";
			//echo "Page $cur_page of $pages - ";
			$label["navigation_page"] =  str_replace ("%CUR_PAGE%", $cur_page, $label["navigation_page"]);
			$label["navigation_page"] =  str_replace ("%PAGES%", $pages, $label["navigation_page"]);
			echo "<span > ".$label["navigation_page"]."</span> ";
			$nav = nav_pages_struct($q_string, $count, $records_per_page);
			$LINKS = 10;
			render_nav_pages($nav, $LINKS, $q_string);
			echo "</center>";

		}

		?>
        <table class="table">
        <thead>
		<tr>
		<?php
		if ($admin == true ) {
			 echo '<th scope="col">&nbsp;</th>';
		}

		if ($list_mode == 'USER' ) {
			echo '<th scope="col">&nbsp;</th>';
		}

		echo_list_head_data(1, $admin);

		if (($list_mode == 'USER' ) || ($admin)) {
			echo '<th scope="col">'.$label['ads_inc_pixels_col'].'</th>';
			echo '<th scope="col">'.$label['ads_inc_expires_col'].'</th>';
			echo '<th scope="col">'.$label['ad_list_status'].'</th>';
		}

		?>
		    </tr>
        </thead>
        <tbody>
		<?php
		$i=0; global $prams;
		while (($prams = mysqli_fetch_array($result, MYSQLI_ASSOC)) && ($i < $records_per_page)) {

			$i++;

	
		 ?>
			  <tr>
	
			  <?php
		  
		 if ($admin == true ) {
			 echo '<td>';

			 ?>
			 <!--<input style="font-size: 8pt" type="button" value="Delete" onClick="if (!confirmLink(this, 'Delete, are you sure?')) {return false;} window.location='<?php echo htmlentities($_SERVER['PHP_SELF']);?>?action=delete&ad_id=<?php echo $prams['ad_id']; ?>'"><br>!-->
				<input type="button" class="btn btn-info" value="<?php echo $label['ads_inc_edit']; ?>" onClick="window.location='<?php echo htmlentities($_SERVER['PHP_SELF']);?>?action=edit&ad_id=<?php echo $prams['ad_id']; ?>'">

				<?php
			 
			 echo '</td>';
		 }

		 if ($list_mode == 'USER' ) {
			 echo '<td>';

			 ?>
			 <!--<input style="font-size: 8pt" type="button" value="Delete" onClick="if (!confirmLink(this, 'Delete, are you sure?')) {return false;} window.location='<?php echo htmlentities($_SERVER['PHP_SELF']);?>?action=delete&ad_id=<?php echo $prams['ad_id']; ?>'"><br>-->
				<input type="button" class="btn btn-info" value="<?php echo $label['ads_inc_edit']; ?>" onClick="window.location='<?php echo htmlentities($_SERVER['PHP_SELF']);?>?ad_id=<?php echo $prams['ad_id']; ?>'">

				<?php
			 
			 echo '</td>';
			 
		 }

		 echo_ad_list_data($admin);

		 if (($list_mode == 'USER' ) || ($admin)) {
			 /////////////////
			echo '<td><img src="get_order_image.php?BID='.$BID.'&aid='.$prams['ad_id'].'"></td>';
			//////////////////
			echo '<td>';
			if ($prams['days_expire'] > 0) {


				if ($prams['published']!='Y') {
					$time_start = strtotime(gmdate('r'));
				} else {
					$time_start = strtotime($prams['date_published']." GMT");
				}

				$elapsed_time = strtotime(gmdate('r')) - $time_start;
				$elapsed_days = floor ($elapsed_time / 60 / 60 / 24);
				
				$exp_time =  ($prams['days_expire']  * 24 * 60 * 60);

				$exp_time_to_go = $exp_time - $elapsed_time;
				$exp_days_to_go =  floor ($exp_time_to_go / 60 / 60 / 24);

				$to_go = elapsedtime($exp_time_to_go);

				$elapsed = elapsedtime($elapsed_time);
				
				
				if  ($prams['status']=='expired') {
					$days = "<a href='orders.php'>".$label['ads_inc_expied_stat']."</a>";
				} elseif ($prams['date_published']=='') {
					$days = $label['ads_inc_nyp_stat'];
				} else {
					$days = str_replace ('%ELAPSED%', $elapsed, $label['ads_inc_elapsed_stat']);
					$days = str_replace ('%TO_GO%', $to_go, $days);
					//$days = "$elapsed elapsed<br> $to_go to go ";
				}

				//$days = $elapsed_time; 
				//print_r($prams);

			} else {

				$days = $label['ads_inc_nev_stat'];

			}
			echo $days;
			echo '</td>';
			/////////////////
			if ($prams['published']=='Y') {
				$pub =$label['ads_inc_pub_stat'];
			} else {
				$pub = $label['ads_inc_npub_stat'];
				
			}
			if ($prams['approved']=='Y') {
				$app = $label['ads_inc_app_stat'].', ';
			} else {
				$app = $label['ads_inc_napp_stat'].', ';
			}
			//$label['ad_list_st_'.$prams['status']]." 
			echo '<td>'.$app.$pub."</td>";
		}

		  ?>


		</tr>
		  <?php
			 //$prams[file_photo] = '';
			// $new_name='';
		}

		echo "</tbody></table>";
   
   } else {

      echo "<center><font size='2' face='Arial'><b>".$label["ads_not_found"].".</b></font></center>";

   }

   return $count;


}

########################################################
function delete_ads_files ($ad_id) {

	$sql = "SELECT * FROM form_fields WHERE form_id=1 ";
	$result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']));

	while ($row=mysqli_fetch_array($result, MYSQLI_ASSOC)) {

		$field_id = $row['field_id'];
		$field_type = $row['field_type'];

		if (($field_type == "FILE")) {
			
			deleteFile("ads", "ad_id", $ad_id, $field_id);
			
		}

		if (($field_type == "IMAGE")){
			
			deleteImage("ads", "ad_id", $ad_id, $field_id);
			
		}
		
	}


}

####################

function delete_ad ($ad_id) {

	 delete_ads_files ($ad_id);
  

   $sql = "DELETE FROM `ads` WHERE `ad_id`='".intval($ad_id)."' ";
   $result = mysqli_query($GLOBALS['connection'], $sql) or die (mysqli_error($GLOBALS['connection']).$sql);


}

function generate_ad_id () {

   $query ="SELECT max(`ad_id`) FROM `ads`";
   $result = mysqli_query($GLOBALS['connection'], $query) or die(mysqli_error($GLOBALS['connection']));
   $row = mysqli_fetch_row($result);
   $row[0]++;
   return $row[0];

}

function insert_ad_data() {
	global $f2;

    $admin = false;
	if (func_num_args() > 0) {
		$admin = func_get_arg(0); // admin mode.
	}

	$user_id = $_SESSION['MDS_ID'];
	if ($user_id=='') {
		$user_id = addslashes(session_id());
	}

	$order_id = (isset($_REQUEST['order_id']) && !empty($_REQUEST['order_id'])) ? $_REQUEST['order_id'] : (isset($_SESSION['MDS_order_id']) ? $_SESSION['MDS_order_id'] : 0);
	$BID = ( isset( $_REQUEST['BID'] ) && $f2->bid( $_REQUEST['BID'] ) != '' ) ? $f2->bid( $_REQUEST['BID'] ) : 1;

	if (isset($_REQUEST['ad_id']) && (empty($_REQUEST['ad_id']))) {

		$ad_id = generate_ad_id ();
		$now = (gmdate("Y-m-d H:i:s"));

		$extra_values = get_sql_insert_values(1, "ads", "ad_id", $_REQUEST['ad_id'], $user_id);
		$values = $ad_id . ", '" . $user_id . "', '" . mysqli_real_escape_string($GLOBALS['connection'], $now) . "', " . $order_id . ", $BID" . $extra_values;

		$sql = "REPLACE INTO ads VALUES (" . $values . ");";

	} else {
		
		$ad_id = intval($_REQUEST['ad_id']);

		if (!$admin) {
		    // make sure that the logged in user is the owner of this ad.

			if (!is_numeric($_REQUEST['user_id'])) {
				if ($_REQUEST['user_id']!=session_id()) {
				    return false;
				}

			} else {
			    // user is logged in
				$sql = "SELECT user_id FROM `ads` WHERE ad_id='".intval($_REQUEST['ad_id'])."'";
				$result = mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));
				$row = @mysqli_fetch_array($result);

				if ($_SESSION['MDS_ID']!==$row['user_id']) {
					// not the owner, hacking attempt!
					return false;
				}
			}
		}

		$now = (gmdate("Y-m-d H:i:s"));
		$sql = "UPDATE ads SET ad_date='$now'".get_sql_update_values(1, "ads", "ad_id", $ad_id, $user_id)." WHERE ad_id='".$ad_id."'";
		$f2->write_log($sql);
	}
	
	mysqli_query($GLOBALS['connection'], $sql) or die("<br />SQL:[$sql]<br />ERROR:[".mysqli_error($GLOBALS['connection'])."]<br />");

	return $ad_id;
}

function validate_ad_data($form_id) {

	return validate_form_data(1);
}

function update_blocks_with_ad($ad_id, $user_id) {
	global $prams, $f2;
	$prams = load_ad_values($ad_id);
	
	if ($prams['order_id']>0) {
		$sql = "UPDATE blocks SET alt_text='".mysqli_real_escape_string( $GLOBALS['connection'], get_template_value('ALT_TEXT', 1))."', url='".mysqli_real_escape_string( $GLOBALS['connection'], get_template_value('URL', 1))."'  WHERE order_id='".intval($prams['order_id'])."' AND user_id='".intval($user_id)."' ";
		mysqli_query($GLOBALS['connection'], $sql) or die(mysqli_error($GLOBALS['connection']));
		$f2->debug("Updated blocks with ad URL, ALT_TEXT", $sql);
	}

}
?>
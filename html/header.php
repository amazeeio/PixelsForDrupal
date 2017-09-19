<?php
// MillionDollarScript header.php

/*

Cache controls:

We have to make sure that this html page is cashed by the browser.
If the banner was not modified, then send out a HTTP/1.0 304 Not Modified and exit
otherwise output the HTML to the browser.

*/

if (MDS_AGRESSIVE_CACHE=='YES') {

	// cache all requests, browsers must respect this php script
	header('Cache-Control: public, must-revalidate'); 
	$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
	$gmdate_mod = gmdate('D, d M Y H:i:s', $b_row['time_stamp']) . ' GMT';
	if ($if_modified_since == $gmdate_mod) {
		header("HTTP/1.0 304 Not Modified");
		exit;	
	}
	header("Last-Modified: $gmdate_mod");

}

$BID = (isset($_REQUEST['BID']) && $f2->bid($_REQUEST['BID'])!='') ? $f2->bid($_REQUEST['BID']) : $BID = 1;

?><!DOCTYPE html>
<html>
<head>
	<title><?php echo SITE_NAME; ?></title>
	<meta name="Description" content="<?php echo SITE_SLOGAN; ?>">
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<link rel=StyleSheet type="text/css" href="main.css" >
</head>
<body class="main">

	<div class="outer">
		<div class="inner">
		
			<div class="heading">
			
				<?php
				$logourl = SITE_LOGO_URL;
				if(!empty($logourl)) { ?>
				<!-- logo image -->
				<div class="logo">
					<a href="index.php">
						<img src="<?php echo $logourl; ?>" style="border:0px;" alt="" />
					</a>
				</div>
				<?php } ?>
				
				<?php
				$slogan = SITE_SLOGAN;
				if(!empty($slogan)) { ?>
				<!-- slogan -->
				<div class="slogan">
					<?php echo $slogan; ?>
				</div>
				<?php } ?>
				
				<!-- stats iframe -->
				<div class="status_outer">
					<iframe width="150" height="50" frameborder=0 marginwidth=0 marginheight=0 VSPACE=0 HSPACE=0 SCROLLING=no  src="display_stats.php?BID=1" allowtransparency="true"></iframe>
				</div>
				
				<div class="clear"></div>
			</div>
			
			<!-- menu links -->
			<div class="menu_outer">
				<div class="menu_bar">
					<ul class="menu">
						<li><a href='index.php'>Home</a></li>
						<li><a href='users/'>Buy Pixels</a></li>
						<li><a href='list.php'>Ads List</a></li>
					</ul>
					<div class="clear"></div>
				</div>
			</div>
			<div class="container">
			
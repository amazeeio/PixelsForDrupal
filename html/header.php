<?php
// MillionDollarScript header.php

/*

Cache controls:

We have to make sure that this html page is cashed by the browser.
If the banner was not modified, then send out a HTTP/1.0 304 Not Modified and exit
otherwise output the HTML to the browser.

*/

if (MDS_AGRESSIVE_CACHE == 'YES') {

    // cache all requests, browsers must respect this php script
    header('content-type: text/html; charset=utf-8');
    header('Cache-Control: public, must-revalidate');
    $if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
    $gmdate_mod = gmdate('D, d M Y H:i:s', $b_row['time_stamp']) . ' GMT';
    if ($if_modified_since == $gmdate_mod) {
        header("HTTP/1.0 304 Not Modified");
        exit;
    }
    header("Last-Modified: $gmdate_mod");

}

$BID = (isset($_REQUEST['BID']) && $f2->bid($_REQUEST['BID']) != '') ? $f2->bid($_REQUEST['BID']) : $BID = 1;

$logourl = SITE_LOGO_URL;
?><!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME; ?></title>
    <meta name="Description" content="<?php echo SITE_SLOGAN; ?>">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!--	<link rel="stylesheet" type="text/css" href="main.css?ver=-->
    <?php //echo filemtime(BASE_PATH . "/main.css"); ?><!--" >-->
</head>
<body>

<!--<a class="navbar-brand" href="index.php">-->
<!--    --><?php //if (!empty($logourl)) { ?>
<!--       <img src="--><?php ////echo htmlentities($logourl); ?><!--" class="d-inline-block align-top" alt="">-->
<!--    --><?php //} ?>
<!--    $500,000 Drupal Page-->
<!--</a>-->

<div class="jumbotron mb-0">
    <div class="container text-center">
        <h1>$500,000 Drupal Homepage</h1>
    </div>
</div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href='index.php'>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='users/'>Buy Pixels</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='list.php'>Ads List</a>
                </li>
            </ul>
            <?php echo get_stats_html_code($BID); ?>
        </div>
    </div>
</nav>

<div class="container text-center">

    <?php
    $slogan = SITE_SLOGAN;
    if (!empty($slogan)) { ?>
        <!-- slogan -->
        <div class="slogan">
            <?php // echo htmlentities($slogan); ?>
        </div>
    <?php } ?>

			
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

//require( 'config.php' );
//require_once('include/mouseover_js.inc.php');
$BID = $f2->bid($_REQUEST['BID']);
$banner_data = load_banner_constants($BID);
?>
<script>
	var h_padding=10;
	var v_padding=10;

	var winWidth = 0;
	var winHeight = 0;
		
	var pos = 'right';

	var strCache = [];

	var lastStr;
	var trip_count = 0;

	function initialize() {
		bubblebox();
		initFrameSize();
	}

	function bubblebox() {
		window.bubblebox = document.getElementById('bubble');
	}

	if (document.readyState === "loading") {
		document.addEventListener("DOMContentLoaded", initialize);
	} else {
		initialize();
	}

	function initFrameSize() {

		winWidth =<?php echo $banner_data['G_WIDTH'] * $banner_data['BLK_WIDTH']; ?>;
		winHeight =<?php echo $banner_data['G_HEIGHT'] * $banner_data['BLK_HEIGHT']; ?>;
	}

	function is_right_available(e) {
		if ((window.bubblebox.clientWidth + e.clientX + h_padding) >= winWidth) {
// not available
			return false;
		}
		return true;
	}

	function is_top_available(e) {
		if ((e.clientY - window.bubblebox.clientHeight - v_padding) < 0) {
			return false;
		}
		return true;

	}

	function is_bot_available(e) {
		if ((e.clientY + window.bubblebox.clientHeight + v_padding) > winHeight) {
			return false;
		}
		return true;
	}

	function is_left_available(e) {
		if ((e.clientX - window.bubblebox.clientWidth - h_padding) < 0) {
			return false;
		}
		return true;

	}

	function boxFinishedMoving() {
		var y = window.bubblebox.offsetTop;
		var x = window.bubblebox.offsetLeft;

//window.status="x:"+x+" y:"+y+" box.ypos:"+box.ypos+" box.xpos:"+box.xpos;
		if ((y < window.bubblebox.ypos) || (y > window.bubblebox.ypos) || (x < window.bubblebox.xpos) || (x > window.bubblebox.xpos)) {
			return false;
		} else {
			return true;
		}
	}
	function moveBox() {
		var y = window.bubblebox.offsetTop;
		var x = window.bubblebox.offsetLeft;

		var diffx = Math.abs(x - window.bubblebox.xpos);
		var diffy = Math.abs(y - window.bubblebox.ypos);

		if (!boxFinishedMoving()) {
			if (y < window.bubblebox.ypos) {
				y += Math.round(diffy * (0.01)) + 1; // calculate acceleration
				window.bubblebox.style.top = y + "px";
			}

			if (y > window.bubblebox.ypos) {
				y-=Math.round(diffy*(0.01))+1;
				window.bubblebox.style.top = y + "px";
			}

			if (x < window.bubblebox.xpos) {
				x+=Math.round(diffx*(0.01))+1; 
				window.bubblebox.style.left = x + "px";
			}

			if (x > window.bubblebox.xpos) {
				x -= Math.round(diffx * (0.01)) + 1;
				window.bubblebox.style.left = x + "px";
			}
			  }
		} 

	///////////////

	// This function is used for the instant pop-up box
	function moveBox2() {

		var y = window.bubblebox.offsetTop;
		var x = window.bubblebox.offsetLeft;

		var diffx = Math.abs(x - window.bubblebox.xpos);
		var diffy = Math.abs(y - window.bubblebox.ypos);

		if (!boxFinishedMoving()) {
			if (y < window.bubblebox.ypos) {
				y=y+diffy;
				window.bubblebox.style.top = y + "px";
			}

			if (y > window.bubblebox.ypos) {
				y=y-diffy;
				window.bubblebox.style.top = y + "px";
			}

			if (x < window.bubblebox.xpos) {
				x=x+diffx;
				window.bubblebox.style.left = x + "px";
			}

			if (x > window.bubblebox.xpos) {
				x=x-diffx;
				window.bubblebox.style.left = x + "px";
			  }
		} 
	}

	function isBrowserCompatible() {

// check if we can XMLHttpRequest

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
		try {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
		xmlhttp = false;
		}
		}
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		  xmlhttp = new XMLHttpRequest();
		}

		if (!xmlhttp) {
			return false
		}
		return true;

	}

	////////////////////

	function fillAdContent(aid, bubble) {

		if (!isBrowserCompatible()) {
			return false;
		}

		if (strCache[aid])
		{
			bubble.innerHTML = strCache[aid];
			return true;
		}

//////////////////////////////////////////////////
// AJAX Magic.
//////////////////////////////////////////////////

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
		try {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
		xmlhttp = false;
		}
		}
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
		  xmlhttp = new XMLHttpRequest();
		}

		xmlhttp.open("GET", "ga.php?AID="+aid+"<?php echo "&t=".time(); ?>", true);

//alert("before trup_count:"+trip_count);

		if (trip_count !== 0) { // trip_count: global variable counts how many times it goes to the server
// waiting state...

		}


		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState===4) {
//


//alert(xmlhttp.responseText);


				if (xmlhttp.responseText.length > 0) {
					bubble.innerHTML = xmlhttp.responseText;
					strCache[''+aid] = xmlhttp.responseText
				} else {
					
					bubble.innerHTML = bubble.innerHTML.replace('<img src="<?php echo BASE_HTTP_PATH;?>periods.gif" border="0">','');
				}
				trip_count--;

//document.getElementById('submit_button1').disabled=false;
//document.getElementById('submit_button2').disabled=false;

//var pointer = document.getElementById('block_pointer');
//pointer.style.cursor='pointer';
//var pixelimg = document.getElementById('pixelimg');
//pixelimg.style.cursor='pointer';

			}

		};

		xmlhttp.send(null)
	}

	////////////////

	function sB(e, str, area, aid) {
		window.clearTimeout(timeoutId);

		if(window.bubblebox === undefined) {
			window.bubblebox = document.getElementById('bubble');
		}

		var relTarg;
		if (!e) e = window.event;
		if (e.relatedTarget) relTarg = e.relatedTarget;
		else if (e.fromElement) relTarg = e.fromElement;

		if (lastStr !== str) {

			lastStr=str;
			
			hideBubble(e);

//window.status="x:"+x+" y:"+y+" box.ypos:"+box.ypos+" box.xpos:"+box.xpos;
//	window.status="e.clientX"+e.clientX+" e.clientY:"+e.clientY;
//str=str+"hello: "+bubble.clientWidth;
//b.filter="progid:DXImageTransform.Microsoft.Blinds(Duration=0.5)";

			document.getElementById('content').innerHTML=str;
			trip_count++;
			
			fillAdContent(aid, document.getElementById('content'));

//alert(document.getElementById('bubble').innerHTML);
		}

		var mytop = is_top_available(e);
		var mybot = is_bot_available(e);
		var myright = is_right_available(e);
		var myleft = is_left_available(e);

//window.status="e.clientX"+e.clientX+" e.clientY:"+e.clientY+" mytop:"+mytop+" mybot:"+mybot+" myright:"+myright+" myleft:"+myleft+" | clientWidth:"+bubble.clientWidth+" clientHeight:"+bubble.clientHeight+" ww:"+winWidth+" wh:"+winHeight;

		if (mytop) {
// move to the top
//b.top=e.clientY-bubble.clientHeight-v_padding;
			window.bubblebox.ypos = e.clientY - window.bubblebox.clientHeight - v_padding;
//alert(bubble.xpos);
		}

		if (myright) {
// move to the right
//b.left=e.clientX+h_padding;//+bubble.clientWidth;
			window.bubblebox.xpos = e.clientX + h_padding;
		}

		if (myleft) {
// move to the left
//b.left=e.clientX-bubble.clientWidth-h_padding ;
			window.bubblebox.xpos = e.clientX - window.bubblebox.clientWidth - h_padding;
		}


		if (mybot) {
// move to the bottom
//b.top=e.clientY+v_padding;
			window.bubblebox.ypos = e.clientY + v_padding;
		}

		window.bubblebox.style.visibility = 'visible';

//ChangeBgd(bubble);

		<?php if (ENABLE_MOUSEOVER == 'POPUP') { ?>
			moveBox2();
		<?php } else { ?>
			moveBox();
		<?php } ?>
	}

	function hBTimeout(e) {
		lastStr='';
		hideBubble(e);
	}

	function hideBubble(e) {
		window.clearTimeout(timeoutId);
		window.bubblebox.style.visibility = 'hidden';
	}

	var timeoutId=0;

	function hI() {

		if (timeoutId===0) {

			timeoutId = window.setTimeout('hBTimeout()', '<?php echo HIDE_TIMEOUT; ?>')
		}
	}

	function cI() {

		if (timeoutId!==0) {
			timeoutId=0;
		}
	}

	function po(block_id) {

	  block_clicked=true;
	  window.open('click.php?block_id=' + block_id + '&BID=<?php echo $BID; ?>','','');
	  return false;
	}
	<?php if (REDIRECT_SWITCH=='YES') { ?>
	p = parent.window;
	<?php } ?>

	var block_clicked = false; // did the user click a sold block?
</script>
<?php if( !isset($_SESSION[NANO_CMS_ADMIN_LOGGED]) ) die("Access Denied"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
<title>NanoCMS v0.2 -  Admin Area</title>
<style type="text/css">
body { font-family:Arial, Helvetica, sans-serif; background:#F4F4F4; text-align:center;}
#wrapper { width:800px; padding:10px; border:2px solid #999999; background:#FFFFFF; text-align:left; margin:auto; }
#header {  background:#333333; color:#FFFFFF; border-bottom:1px solid #999999; padding:20px;}
#header h1 { font-weight:lighter; margin:0; }
#header h2 { font-weight:lighter; margin:0; font-size:14px; }
#main { font-size:76%; }
#main h2 { font-size:18px; font-family:Georgia; border-bottom:1px solid #FF6600; }
#right{ margin:0px 10px 10px 10px; padding:10px; }
a { color:#CC6600; font-weight:bold }
a:hover { color:#CC3300; }
#topnav { background:#333333; padding:5px;}
#topnav a { color:#FF9933; margin-right:9px; font-size:12px }
#topnav a:hover { color:#FFCC66; }
#footer { clear:both; background:#333333; color:#FFFFFF; padding:5px; vertical-align:middle; font-size:12px; }
#footer a { color:#FF9933; margin-right:9px; font-size:12px; }
#footer a:hover { color:#FFCC66; }
form { display:inline; }
.areabox { overflow:auto; border:1px solid #999999; }
.th { background:#333333; color:#fff; }
</style>
</head>
<body>
<div id="wrapper">
  <div id="header">
    <h1>NanoCMS v0.2 - Admin </h1>
    <h2>The tinyest cms you can find</h2>
  </div>
  <div id="topnav">
	<a href="?">Home</a> | 
	<a href="?action=addpage">Add Page</a> | 
	<a href="?action=showpages">Show Pages and Options</a> |
	<a href="?action=showareas">Show Content Areas</a> |
	<a href="?logout">Admin Logout</a>
  </div>
  <div id="main">
    <div id="right">
<?php showedit(); ?>
<?php showadd(); ?>
<?php doDelete(); ?>
<?php doAreaEdit(); ?>
<?php 
	if( $_GET[action] == 'showpages' )
		showpageslist();
	if( $_GET[action] == 'showareas' )
		showareas();	
	if( !isset($_GET[action]) )
		require_once "intro.php";	
?>
    </div>
 </div>    
 <div id="footer">&copy; <a href="http://www.KalyanChakravarthy.net">Kalyan Chakravarthy</a> All rights reserved<br />
	powered by <A href='http://NanoCMS.KalyanChakravarthy.net'>NanoCMS</a>
 </div>
 
</div>
</body>
</html>
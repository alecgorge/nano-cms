<?php include "setting.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
<title><?php show_title(); ?></title>
<style type="text/css">
body { font-family:Arial, Helvetica, sans-serif; background:#F4F4F4; text-align:center;}
#wrapper { width:800px; padding:10px; border:2px solid #999999; background:#FFFFFF; text-align:left; margin:auto; }

#header {  background:#333333; color:#FFFFFF; border-bottom:1px solid #999999; padding:20px;}
#header h1 { font-weight:lighter; margin:0; }
#header h2 { font-weight:lighter; margin:0; font-size:14px; }

#main { font-size:76%; }
#main h2 { font-size:18px; font-family:Georgia; border-bottom:1px solid #FF6600; }

#left { width:25%; float:left; margin:10px 0px 10px 0px; padding:10px; }
#right{ width:69%; float:right; margin:10px 0px 10px 0px; padding:10px; }

a { color:#CC6600; font-weight:bold }
a:hover { color:#CC3300; }

#topnav { background:#333333; padding:5px; color:#ff9933 }
#topnav a { color:#FF9933; margin-right:9px; font-size:12px }
#topnav a:hover { color:#FFCC66; }

#footer { clear:both; background:#333333; color:#FFFFFF; padding:5px; vertical-align:middle; font-size:12px; }
#footer a { color:#FF9933; margin-right:9px; font-size:13px; }
#footer a:hover { color:#FFCC66; }

li { margin-bottom:6px; }

</style>
</head>
<body>
<div id="wrapper">
  <div id="header">
    <h1>NanoCMS</h1>
    <h2>The tinyest cms you can find</h2>
  </div>
  <div id="topnav">
<!--Welcome to the website powered by NanoCMS-->
	<a href="index.php?slug=home">Home</a>
	<a href="index.php?slug=download">Download NanoCMS</a>
	<a href="index.php?slug=mailing-list">Mailing List</a>
  </div>
  <div id="main">
    <div id="left">
      <h2>Navigation</h2>
	  <?php show_sidebar(); ?>
    </div>
    <div id="right">
	  <?php show_content(); ?>
    </div>
 </div>    
 <div id="footer">&copy; <a href="#">Kalyan Chakravarthy</a> All rights reserved<br>
	powered by <A href='http://NanoCMS.KalyanChakravarthy.net'>NanoCMS</a>
 </div>
 
</div>
</body>
</html>

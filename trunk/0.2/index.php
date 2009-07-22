<?php require_once("setting.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
<head>
<title><?php show_content_area('WebSite Name'); ?> &raquo; <?php show_title(); ?></title>
<meta name="description" content="NanoCMS is the smallest text file based cms written in php. As the nano name suggests the cms is really tiny, small,elegant, easy to use interface. You can create saperate pages and also sidebar content pages. The sidebar links are added automatically" />
<meta name="keywords" content="NanoCMS, nano, cms, tiny, small, easy to use, easy, free, opensource, easy, interface, pages, static, dynamic content, beginners" />
<meta name="author" content="Kalyan Chakravarthy" />
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="wrapper">
  <div id="header">
    <h1><?php show_content_area('WebSite Name'); ?></h1>
    <h2><?php show_content_area('WebSite slogan'); ?></h2>
  </div>
  <div id="topnav">
	  <?php show_content_area('Top Navigation'); ?>
  </div>
  <div id="main">
    <div id="left">
      <h2>Navigation</h2>
	  <div id="leftnav">
		  <?php show_sidebar(); ?>
		  <?php show_content_area('Below Navigation'); ?>
	  </div>
    </div>
    <div id="right">
	  <?php show_content_slug(); ?>
    </div>
 </div>    
 <div id="footer">
	<?php show_content_area('Copyright Notice'); ?>
	powered by <a href='http://NanoCMS.KalyanChakravarthy.net'>NanoCMS</a>
 </div>
 
</div>
<!--
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<script type="text/javascript">
_uacct = "UA-904895-6";
urchinTracker();
</script>
-->
</body>
</html>
<?php require_once("setting.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>NanoCMS (v0.31) -  Admin Panel</title>
<link rel="stylesheet" media="all" type="text/css" href="stuff/pro_dropdown_3/pro_dropdown_3.css" />
<link rel="stylesheet" href="stuff/style.css" />
<script language='javascript' src="stuff/toggle.js"></script>
<script src="stuff/pro_dropdown_3/stuHover.js" type="text/javascript"></script>
<style type="text/css"></style>
<?php runTweak( 'admin-head-content' ); ?>
</head>
<body>
<div id="pagewrapper">
<div id="page">
  <div id="header">
  	<div class="viewsitelink"><a href="<?php echo NANO_CMS_PAGE; ?>" target="_blank">View Site</a> | <a href="?logout" >Logout</a></div>
	   <h1><span class="bcol">Nano</span>CMS - Admin Panel</h1> 
	   <h2>The tinyest CMS you can ever find - v0.31</h2> 
	     </div>
  <div id="topnav">

			<ul id="nav">
			
				<li class="top"><a href="?" class="top_link"><span>Home</span></a></li>
				<li class="top"><a href="?action=addpage" class="top_link">Create Page</a></li>
				<li class="top"><a href="?action=showpages" class="top_link">Pages & Options</a></li>
				<li class="top"><a href="?action=showareas" class="top_link">Content Areas</a></li>
				</li>
				<li class="top">
				<a href="?action=tweakers" title="Tweakers are Plugin like things which lets you extend NanoCms" class="top_link"><span class="down">Tweakers</span></a>
					<ul class="sub">
						<li><a href="?action=tweakers">View All tweaks</a></li>
						<?php listoutInterfaces(); ?>
					</ul>
				</li>
			</ul>

  </div>
  <div id="main">
    

	<div id="body">
<?php
	if( $_GET[action] == 'addpage' )
		addpage();

	elseif( $_GET[action] == 'delete' )
		doDelete();	

	elseif( $_GET[action] == 'edit' )
		performEdit();//edit pages

	elseif( $_GET[action] == 'showpages' )
		showpageslist();

	elseif( $_GET[action] == 'editarea' )
		doAreaEdit();//edit content areas		

	elseif( $_GET[action] == 'showareas' )
		showareas();

	elseif( $_GET[action] == 'tweakers' )
		showTweakers();

	//will be added...
	//elseif( $_GET[action] == 'tweakersUpload' )
	//	showTweakersUpload();

	elseif( isset($_GET[tweak]) )
		showTweaksInterface();

	elseif( !isset($_GET[action]) ) {
		$introPage = "intro.php";
		runTweak( 'intro-page', array( &$introPage ) );
		require_once $introPage;
	}
		
	runTweak( 'admin-body' );
?>

	</div>

  </div><!-- END OF MAIN DIV TAG -->
  
 <div id="footer">
	 &copy; <a href="http://www.kalyanchakravarthy.net">Kalyan Chakravarthy</a>
 </div>
  
</div>
</div>

</body>
</html>

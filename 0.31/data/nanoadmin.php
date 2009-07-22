<?php
/*
	NanoCMS v0.31 © 2007-2008 Kalyan Chakravarthy ( www.KalyanChakravarthy.net )
	( Stable )
*/

session_start();
require_once "setting.php";

runTweak('after-settings-load');

//General functions
require_once( "libs/general.lib.php" );

//Admin Login Lib
require_once( "libs/admin.login.lib.php" );

//Pages ( create, edit, ordering etc )
require_once( "libs/admin.pages.lib.php" );

//Content areas handler
require_once( "libs/admin.contentareas.lib.php" );

//Tweaker handling functions
require_once( "libs/admin.tweakers.lib.php" );


performMove();
doTweakToggle();

// include the template of the admin area :)  ///////////////////////////////////////////////////

	$adminPageName = "admindesign.php";
	//debug($adminPageName,0);
	runTweak( 'admin-page', array( &$adminPageName ) );
	//debug($adminPageName,1);
	require_once $adminPageName;
?>
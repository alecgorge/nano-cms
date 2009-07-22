<?php

//login
runTweak( 'before-login-check' );
if( !isset( $_SESSION[ NANO_CMS_ADMIN_LOGGED ] ) ) {
	if( isset($_POST[user]) ) {
		if( $_POST[user] == NANO_CMS_ADMIN_USER and $_POST[pass] == NANO_CMS_ADMIN_PASS ) {
			$_SESSION[ LOGIN_TIME_STAMP ] = $ts = time();
			$_SESSION[ NANO_CMS_ADMIN_LOGGED ] = md5(NANO_CMS_ADMIN_PASS.$ts); //die('done');
			runTweak( 'after-logged-in' );
		} else {
			$loginbox_msg = lt( "Error : wrong Username or Password" );
		}		
	}
}
//logout
if( isset( $_GET[ logout ] ) )	{
	$loginbox_msg = lt( "You were successfully logged out" );
	unset( $_SESSION[NANO_CMS_ADMIN_LOGGED] );
}
//the login form
if( $_SESSION[NANO_CMS_ADMIN_LOGGED]!=md5(NANO_CMS_ADMIN_PASS.$_SESSION[ LOGIN_TIME_STAMP ]) or !isset( $_SESSION[NANO_CMS_ADMIN_LOGGED] ) )
{
	session_destroy();
	runTweak( 'before-login-form' );

	$form = "
		<html>
		<head>
		<title>NanoCMS Admin login</title>
		<style type='text/css'>
		body{ font:12px verdana; background:#FFFFEA; text-align:center; }
		table { border-collapse:collapse; background:#FFFFEA; }
		.cinfo { font-size:9px; }
		.cinfo a { color:#FF9933; }
		.cinfo a:hover { text-decoration:none; }
		</style>
		</head>
		<body>
			<br />	<br />	<br />	<br />	<br />	<br />	<br />
			<p align='center'>$loginbox_msg</p>
			<table align='center' border='1' cellpadding='5px' bordercolor='#FF9933'>
			<form action='?' method='post'>
			<tr class='th'><td colspan=2 align='center'>NanoCMS Login</td></tr>
			<tr><td>Username</td><td><input type='text' name='user'></td></tr>
			<tr><td>Password</td><td><input type='password' name='pass'></td></tr>
			<tr><td colspan='2' align='right'><input type='submit' value='Login'></td></tr>
			</form>
			</table>
			<p class='cinfo'>&copy; <a href='http://KalyanChakravarthy.net/'>Kalyan Chakravarthy</a></p>
		</body>
		</html>
";

	runTweak( 'login-form', array( &$form ) );

	echo $form;
	exit();
}

?>
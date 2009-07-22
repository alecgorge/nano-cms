<?php
if( !function_exists('file_put_contents') ) {
function file_put_contents($n,$d) {
	$f=@fopen($n,"w");
	if (!$f) {
		return false;
	} else {
		fwrite($f,$d);
		fclose($f);
		return true;
	}
}
}

error_reporting( 0 );
if( !is_callable( 'gzuncompress' ) ) {
	die( "Fatal Error : gz compression not available. Unable to unpack updater file" );
}
$updater_file = "update2v0.4_final";
if( file_exists( $updater_file.".gz" ) ){
	$str = file_get_contents("$updater_file.gz");
	$str = gzuncompress( $str );
} 
else
if( file_exists( $updater_file.".txt" ) )
	$str = file_get_contents("$updater_file.txt");
else
	die( "Fatal Error : Updater File was not found" );

if( isset( $_GET['next'] ) ) {
	if( $_GET['next'] == 'rm_and_login' ) {
		@unlink('nano_upgrade.php');
		@unlink($updater_file.'.gz');
		@unlink($updater_file.'.txt');
	}
	header( "location:nanoadmin.php" );
}

$arr = unserialize( $str );
$is_ok = 0;
$is_form = 1;
$msg='';

if( isset($_POST['start_upgrade']) ) {
	include "config.php";
	$username = $NanoCMS['admin_username'];
	$password = $NanoCMS['admin_password'];

	$pagesdata = unserialize( file_get_contents( 'pagesdata.txt' ) );

	if( ( isset($pagesdata['username']) and $pagesdata['username']==$_POST['user'] and $pagesdata['password']==md5($_POST['pass']) ) or ( isset($NanoCMS['admin_username']) and $username==$_POST['user'] and $password==$_POST['pass'] )) {
		$is_form=0;
		$is_ok = 1;
	} else {
		$msg = "<tr><td colspan=2 align='center'><font color='red'>Error : Wrong username / password</font></td></tr>";
	}
}
?>
<html>
<head>
<title>NanoCMS v0.4 Final - Auto Updater</title>
<style type='text/css'>
<?php echo $arr['files']['all']['admin-design/stuff/admin.css']; ?>
#login_page { width:<?php echo $is_ok ? '700px' : '350px' ?>; margin:auto; margin-top:<?php echo $is_ok ? '80px' : '170px' ?>; }
table { background:#fff; border:2px solid #C3D9FF; }
th { padding:5px; }
td { padding:5px; }
pre.info { height:300px; overflow:auto; margin:10px; padding:5px; border:2px solid #c3d9ff; }
</style>
</head>
<body>
    <div id='login_page'>
    <table align='center' border='1' cellpadding='8'>
    <form action='?' method='post'>
    <tr class='th'><th colspan=2 align='center'>NanoCMS v0.4 - Final - Auto Upgrader</th></tr>
<?php
	if( $is_form ) {
?>
    <tr><td colspan="2">
        <p>Be sure to give full read/write permissions ( CHMOD 777 ) to ./data directory</p>
        <p>Username & password will be reset to <b>admin</b> , <b>demo</b> after completing the upgrade</p>
    </td></tr>
    <tr><th colspan="2">Enter username / password to begin</td></tr>
     <?php echo $msg; ?>
    <tr><td>Username</td><td><input type='text' name='user'></td></tr>
    <tr><td>Password</td><td><input type='password' name='pass'></td></tr>
    <tr><td colspan='2' align='right'><input type='submit' name="start_upgrade" value='Start Upgrade'></td></tr>
<?php
	}
if( $is_ok ) {
	echo "<tr><td colspan=2>";
	echo "<pre>   Upgraded Started.<br />   Username &amp; Password will be reset to <b>admin</b>, <b>demo</b> after completing the upgrade</p></pre>";
	echo "<pre class='info'>";
	
	foreach( $arr['files']['unlink'] as $f ) {
		@unlink( $f );
		echo "removed - $f\n";
	}
	
	foreach( $arr['dirs']['unlink'] as $dir ) {
		@rmdir( $dir );
		echo "removed - $dir\n";
	}
	
	foreach( $arr['dirs']['new'] as $dir ) {
		@mkdir( $dir );
		echo "created - $dir\n";
	}
	
	foreach( $arr['files']['all'] as $fn=>$fcon ) {
		if( $fn == 'pagesdata.txt' )
			continue;
		@file_put_contents( $fn, $fcon );
		echo "created - $fn\n";
	}
	
	$pagesdata = unserialize( file_get_contents("pagesdata.txt") );
	foreach( $arr['pagesdata.txt']['set'] as $set=>$val )
		$pagesdata[ $set ] = $val;
	foreach( $arr['pagesdata.txt']['unset'] as $set=>$val )
		unset( $pagesdata[ $set ] );
	
	$pagesdata['username'] = 'admin';
	$pagesdata['password'] = md5('demo');
	$pagesdata['version'] = 'v_4f';

	file_put_contents( "pagesdata.txt", serialize( $pagesdata ) );

	echo "</pre>";
	echo "<pre>   What would you like to do\n\t&bull; <a href='?next=login'>Proceed to Login</a>\n\t&bull; <a href='?next=rm_and_login'>Remove Updater files and proceed to login</a></pre>";
	echo "</td></tr>";
	
	
}

?>
    
    </form>
    </table>
    <small>&copy; <a href='http://nanocms.in/'>NanoCMS</a>, <a href='http://KalyanChakravarthy.net/'>Kalyan</a></small>
    </div>
</body>
</html>

<?php

?>

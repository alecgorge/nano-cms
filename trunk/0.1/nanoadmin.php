<?php 
//NanoCMS v0.1 - the tinyest flatfile cms in php
//Copyright (C) 2007  Kalyan Chakravarthy
//
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation, either version 3 of the License, or
//any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.  If not, see <http://www.gnu.org/licenses/>


session_start();
require_once "setting.php";

//login
if( !isset( $_SESSION[NANO_CMS_ADMIN_LOGGED] ) ) {
	if( isset($_POST[user]) ) {
		if( $_POST[user] == NANO_CMS_ADMIN_USER and $_POST[pass] == NANO_CMS_ADMIN_PASS ) {
			$_SESSION[ NANO_CMS_ADMIN_LOGGED ] = true;
		}
	}
}
//logout
if( isset( $_GET[ logout ] ) )	{
	echo('logged out<br>');
	unset( $_SESSION[NANO_CMS_ADMIN_LOGGED] );
}
//the login form
if( !isset( $_SESSION[NANO_CMS_ADMIN_LOGGED] ) )
	{
		echo "<html><head><title>NanoCMS login</title></head><body style='font-family:courier'>";
		echo "<form action='?' method='post'>username <input type=text name='user'><br>password <input type=password name='pass'><br>
			  <input type=submit value='Login'>";
		exit();
	}


function edit()
{
	global $CurrentActivePage;
	$slug = $_GET[slug];
	
	if( isset( $_POST[save] ) )
	{
		if( put2file( sitedir($slug), stripslashes( $_POST[content] ) ) ) 
			echo "success"; 
		else 
			echo "save failed";
	}
	
	echo "<form action='?action=edit&slug=$slug' method=post>";
	echo "<h2>Edit Content : ".getTitle( $CurrentActivePage )."</h2>";
	echo "<textarea name=content rows=30 cols=70>";
	if( file_exists( sitedir( $slug ) ) ) {
		echo htmlentities( file_get_contents( sitedir( $slug ) ) );
	}
	else
	echo "no content yet";
	echo "</textarea>";
	echo "<br><input type=submit value=save name=save>";
	echo "</form>";
}

function showedit() {
	if( isset($_GET[action]) and $_GET[action]=='edit' )
		edit();
}

function addpage()
{
	global $CurrentActivePage,$WebPagesList;
	$slug = $_GET[slug];
	$insidebar = true;
	
	if( isset( $_POST[save] ) )
	{
		$content = $_POST[content];
		$insidebar = $_POST[insidebar]=='1'?true:false;
		$title = $_POST[title];
		
		if( $title != '' and $content !='' )
		{
			//die( "<b>$title.$content</b>" );
			$pdetails = newPage($title,'',$insidebar );
			$WebPagesList[] = $pdetails;
			
			$slug = $pdetails[pageslug];
			if( !file_exists(sitedir($slug)) and put2file( sitedir($slug), stripslashes( $_POST[content] ) ) )
			{
				savepages();
				echo "The page was created successfully<br>";
				echo "File Created : ".sitedir($slug)."<br>";
				echo "Content : ".substr( strip_tags($content), 0, 100 ).( strlen($content)>100 ? '...' : '' )."<br>";
				return;
			}
			else
			{
				echo "save failed";
				if( file_exists(sitedir($slug)) )
					echo " - a page with similar title already exists";
			}
		}
		else
		echo "Either the title or the content is/are empty!!! Please check your input!!<br>";
	}
	
	echo "<form action='?action=addpage' method=post>";
	echo "<h2>Add a new page</h2>";
	echo "Page Title : <input name='title' type='text' value='$title' /><br>";
	echo "Type of page : 
			<select name='insidebar'>
				<option value=1 ".($insidebar==true?' selected ':'').">Sidebar Page</option>
				<option value=0 ".($insidebar==false?' selected ':'').">Normal Page</option>
			</select>
		 <br>";
	echo "Content : <br>";
	echo "<textarea name=content rows=30 cols=70>$content</textarea>";
	echo "<br><input type=submit value=save name=save>";
	echo "</form>";
}

function showadd() {
	if( isset($_GET[action]) and $_GET[action]=='addpage' )
		addpage();
}

function domove() {
	$moved = false;
	if( isset($_GET[action]) and $_GET[action]=='moveup' ) {
		moveup();
		$moved = true;
	}
	if( isset($_GET[action]) and $_GET[action]=='movedown' ) {
		movedown();
		$moved = true;
	}

	if( $moved == true ) {
		header( "Location:nanoadmin.php?action=showpages" );
	}
}

function moveup() {
	global $WebPagesList,$CurrentActivePage;
	$slug = $_GET[slug];
	$p = $WebPagesList;
	$WebPagesList = msort( $WebPagesList );
	$cnt =0;
	foreach( $WebPagesList as $k=>$pg ) {
		
		if( !inSidebar( $pg ) ) continue;
		
		if( getSlug( $pg ) == $slug ) {
				if( $k == 0 ) return; // how can we possibly move the topmost one up :)
				$CurrentActivePage = $WebPagesList[$k];
				$above = $WebPagesList[$pid];
				
				$WebPagesList[$k] = $above;
				$WebPagesList[$pid] = $CurrentActivePage;
				
				$WebPagesList[$k][order] = $CurrentActivePage[order];
				$WebPagesList[$pid][order] = $above[order];

				savepages();
				break;
		}
		
		$prev = $pg;
		$pid = $k;
	}
}

function movedown() {
	global $WebPagesList,$CurrentActivePage;
	$slug = $_GET[slug];
	$p = $WebPagesList;
	// the whole process is same as the moveup() but we are moving the target up in the reverse order
	$WebPagesList = msort( $WebPagesList );
	$WebPagesList = array_reverse( $WebPagesList );
	$cnt =0;
	foreach( $WebPagesList as $k=>$pg ) {
		
		if( !inSidebar( $pg ) ) continue;
		
		if( getSlug( $pg ) == $slug ) {
				if( $cnt == 0 ) return; // how can we possibly move the topmost one up :)
				$CurrentActivePage = $WebPagesList[$k];
				$above = $WebPagesList[$pid];
				
				$WebPagesList[$k] = $above;
				$WebPagesList[$pid] = $CurrentActivePage;
				
				$WebPagesList[$k][order] = $CurrentActivePage[order];
				$WebPagesList[$pid][order] = $above[order];

				
				$WebPagesList = array_reverse( $WebPagesList );
				savepages();
				//echo( var_dump( $WebPagesList ) );
				break;
		}
		$cnt++;
		$prev = $pg;
		$pid = $k;
	}
}

function savepages() {
	global $WebPagesList;
	$pagesdata = serialize( $WebPagesList );
	if( !put2file( PAGES_DETAILS_FILE, $pagesdata ) ) die("file writing error");
}

function doDelete() {
	global $WebPagesList;
	if( isset( $_GET[action] )  and $_GET[action]=='delete' )
	{
		$slug = $_GET[slug];
		foreach( $WebPagesList as $k=>$pg ) {
			//if( !inSidebar( $pg ) ) continue;
			if( getSlug( $pg ) == $slug ) {
				if( $k == 0 ) { echo "cannot delete the Top page"; return; } // how can we possibly move the topmost one up :)
				unset( $WebPagesList[$k] );
				unlink( sitedir(getSlug($pg)) );
				$WebPagesList = msort( $WebPagesList );	
				savepages();
				echo 'success fully deleted';
				break;
			}
		}
	}
}

function showpageslist() {
?>
<h2>Sidebar Pages</h2>
<?php 
	global $WebPagesList,$settings;
	$p = $WebPagesList;
	$p = msort( $p );
	echo "<table border=1 cellpadding=5>";
	echo "<tr><th>Page</th><th>Options</th><th colspan=2>Move</th><th>URL you can use</th></tr>";
	foreach( $p as $pg ) {
		if( inSidebar( $pg ) ) {
			$s = "<tr>
					<td><b>".getTitle($pg)."</b></td>
					<td>
						<a href='?action=edit&slug=".getSlug($pg)."'>Edit</a> | 
						<a href='?action=delete&slug=".getSlug($pg)."' onclick='return confirm(\"Are you sure you want to delete this page!! Remember Once you delete you cannot retreive again!! Proceed???\");'>Delete</a>
					</td>
					<td><a href='?action=moveup&slug=".getSlug($pg)."'>UP</a></td>
					<td><a href='?action=movedown&slug=".getSlug($pg)."'>Down</a></td>
					<td><a href='".NANO_CMS_PAGE."?slug=".getSlug($pg)."'>".NANO_CMS_PAGE."?slug=".getSlug($pg)."</a></td>
				  </tr>";
			echo $s;
		}
	}
	echo "</table>";
	
?>
</td><td>
<h2>Saperate Pages</h2>
<?php 
	global $WebPagesList,$settings;
	$p = $WebPagesList;
	$p = msort( $p );
	echo "<table border=1 cellpadding=5>";
	echo "<tr class=th><th>Page</th><th>Edit</th><th>URL you can use</th></tr>";
	foreach( $p as $pg ) {
		if( !inSidebar( $pg ) ) {
			$s = "<tr>
					<td><b>".getTitle($pg)."</b></td>
					<td>
						<a href='?action=edit&slug=".getSlug($pg)."'>Edit</a> | 
						<a href='?action=delete&slug=".getSlug($pg)."' onclick='return confirm(\"Are you sure you want to delete this page!! Remember Once you delete you cannot retreive again!! Proceed???\");'>Delete</a>
					</td>
					<td><a href='".NANO_CMS_PAGE."?slug=".getSlug($pg)."'>".NANO_CMS_PAGE."?slug=".getSlug($pg)."</a></td>
				  </tr>";
			echo $s;
		}
	}
	echo "</table>";

}


	set_curr_page(); 
	domove(); 

// include the template of the admin area :)
	require_once "data/admindesign.php"; 
?>
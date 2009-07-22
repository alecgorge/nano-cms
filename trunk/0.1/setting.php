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

////////////////////////////////////////////////////////////////////////////////////////
//		your personal settings
////////////////////////////////////////////////////////////////////////////////////////
define( NANO_CMS_PAGE , "index.php" );		// the name of the main index page whereyour site will be displayed
define( NANO_CMS_ADMIN_PASS , "demo" );			//Admin Password
define( NANO_CMS_ADMIN_USER , "admin" );		//Admin username
define( NANO_CMS_FILE_EXTENSION, "txt" );		// choose your default file extension

////////////////////////////////////////////////////////////////////////////////////////
//		do not edit anything below this
////////////////////////////////////////////////////////////////////////////////////////
define( NANO_CMS_ADMIN_LOGGED, 'logged');
define( PAGES_DETAILS_FILE, 'data/pagesdata.txt' );

function msort($array, $id="order") {
    $temp_array = array();
    while(count($array)>0) {
    $lowest_id = 0;
    $index=0;
    foreach ($array as $item) {
    if (isset($item[$id]) && $array[$lowest_id][$id]) {
    if ($item[$id]<$array[$lowest_id][$id]) {
    $lowest_id = $index;
    }
    }
    $index++;
    }
    $temp_array[] = $array[$lowest_id];
    $array = array_merge(array_slice($array, 0,$lowest_id), array_slice($array, $lowest_id+1));
    }
    return $temp_array;
   }

function put2file($n,$d) {
 $f=@fopen($n,"w");
 if (!$f) {
   return false;
 } else {
   fwrite($f,$d);
   fclose($f);
   return true;
 }
}

////////////////////////////////////////////////////////////////////////////////////////
// 	SOME KIND OF API FUNCTIONS WHICH ARE USED THROUGHOUT THE NANO CMS
////////////////////////////////////////////////////////////////////////////////////////
function newPage($title,$pageslug='',$sbar=true) {
	global $WebPagesList;
	if( $pageslug=='' ) {
		$pageslug = $title;
		$pageslug = strtolower( $pageslug );
		$pageslug = str_replace( array(',',"'",'?','/','*','(',')','@','!','&','='),'',$pageslug );
		$pageslug = str_replace( array(' '),'-', $pageslug );
	}
	return array( 'title'=>$title, 'inSidebar'=>$sbar, 'order'=>(count($WebPagesList)+1), 'pageslug'=>$pageslug );
}

function getTitle ( $arr ) { return $arr['title']; }
function inSidebar ( $arr ) { return $arr['inSidebar']; }
function getOrder ( $arr ) { return $arr['order']; }
function getSlug ( $arr ) { return $arr['pageslug']; }
function sitedir( $s ) { return "data/pages/$s.".NANO_CMS_FILE_EXTENSION; }


////////////////////////////////////////////////////////////////////////////////////////
// some functions which are used on front page to display the content
////////////////////////////////////////////////////////////////////////////////////////
function show_title()
{
	global $WebPagesList,$CurrentActivePage;
	echo getTitle( $CurrentActivePage );
}

function show_content()
{
	global $WebPagesList,$CurrentActivePage;
	$slug = getSlug( $CurrentActivePage );
	if( file_exists( sitedir( $slug ) ) ) {
		require_once( sitedir( $slug ) );
	}
	else
	echo "no content defined for this page";
}

function show_sidebar()
{
	global $WebPagesList,$settings;
	$p = $WebPagesList;
	$p = msort( $p );
	echo "<ul>";
	foreach( $p as $pg ) {
		if( inSidebar( $pg ) ) {
			$s = "<li><a href='".NANO_CMS_PAGE."?slug=".getSlug($pg)."'>".getTitle($pg)."</a></li>";
			echo $s;
		}
	}
	echo "</ul>";
}

$WebPagesList = array();
$CurrentActivePage;
/*
$WebPagesList[] = newPage( 'Home','index',true );
$WebPagesList[] = newPage( 'My Biography' );
$WebPagesList[] = newPage( 'About Us' );
$WebPagesList[] = newPage( 'Contact' );
$WebPagesList[] = newPage( 'Disclaimer' );
put2file( PAGES_DETAILS_FILE, serialize( $WebPagesList ) );
//*/

////////////////////////////////////////////////////////////////////////////////////////
// read the settings file
$WebPagesList = unserialize( file_get_contents( PAGES_DETAILS_FILE ) );
////////////////////////////////////////////////////////////////////////////////////////

function set_curr_page()
{
	global $WebPagesList,$CurrentActivePage;
	if( isset( $_GET[slug] ) )
	{
		$slug = $_GET[slug];
		foreach( $WebPagesList as $pg ) {
			if( getSlug( $pg ) == $slug ) {		
				$CurrentActivePage = $pg;
				break;
			}
		}
	}
	else
	{
		$slug = getSlug($WebPagesList[0]);
		$CurrentActivePage = $WebPagesList[0];	
	}
}

set_curr_page();
?>
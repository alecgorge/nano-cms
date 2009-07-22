<?php 
////////////////////////////////////////////////////////////////////////////////////////
//		your personal settings
////////////////////////////////////////////////////////////////////////////////////////
define( NANO_CMS_PAGE , "index.php" );		// the name of the main index page whereyour site will be displayed
define( NANO_CMS_ADMIN_PASS , "demo" );			//Admin Password
define( NANO_CMS_ADMIN_USER , "admin" );		//Admin username
define( NANO_CMS_FILE_EXTENSION, "php" );		// choose your default file extension
define( NANO_CMS_EXTENSIONS_ORDER, "php,html,txt" );
define( NANO_WYSIWYG_EDITOR, 'openwysiwyg' ); 		//wymeditor, openwysiwyg use blank for none;
							//if the addon does not exist it will be ignored..:)
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
// 	SOME FUNCTIONS WHICH ARE USED THROUGHOUT THE NANO CMS
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
function pageDataDir( $s ) {
	$ext = explode( ',', NANO_CMS_EXTENSIONS_ORDER );
	foreach( $ext as $e ) { if( file_exists( "data/pages/$s.$e" ) ) return "data/pages/$s.$e"; }
	return "data/pages/$s.".NANO_CMS_FILE_EXTENSION;
}
function areaDataDir( $s ) {
	$ext = explode( ',', NANO_CMS_EXTENSIONS_ORDER );
	foreach( $ext as $e ) { if( file_exists( "data/areas/$s.$e" ) ) return "data/areas/$s.$e"; }
	return "data/areas/$s.".NANO_CMS_FILE_EXTENSION;
}


////////////////////////////////////////////////////////////////////////////////////////
// some functions which are used on front page to display the content
////////////////////////////////////////////////////////////////////////////////////////
function show_title()
{
	global $WebPagesList,$CurrentActivePage;
	echo getTitle( $CurrentActivePage );
}

function show_content_slug()
{
	global $WebPagesList,$CurrentActivePage;
	$slug = getSlug( $CurrentActivePage );
	$contentFile = pageDataDir( $slug );
	if( file_exists( $contentFile ) )
		require_once( $contentFile );
	else
	echo "no content defined for this page";
}

function show_content_area($areaName)
{
	$areaFile = areaDataDir( $areaName );
	if( file_exists( $areaFile ) )
		require( $areaFile ); // make it compulsory
	//else ignore
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

function set_curr_page()
{
	global $WebPagesList,$CurrentActivePage;
	if( isset( $_GET[slug] ) )
	{
		$slug = $_GET[slug];
		foreach( $WebPagesList as $pg )
		{
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

// wysiwyg loading functions  //////////////////////////////////////////////////////////
function loadWysiwyg() {
if( file_exists( "data/addons/".NANO_WYSIWYG_EDITOR.".php" ) )
	{
		include ( "data/addons/".NANO_WYSIWYG_EDITOR.".php" );
		define( WYSIWYG_LOADED, true );
	}
}
function isloadedWysiwyg() {
	return defined( "WYSIWYG_LOADED" );
}

function showWysiwyg() {
		if( !isloadedWysiwyg() ) return;
		$editor = NANO_WYSIWYG_EDITOR;
		$editor();
}

////////////////////////////////////////////////////////////////////////////////////////
$WebPagesList = $editErrMsg = array();
$CurrentActivePage;
// read the settings file
$WebPagesList = unserialize( file_get_contents( PAGES_DETAILS_FILE ) );
////////////////////////////////////////////////////////////////////////////////////////
set_curr_page();
?>
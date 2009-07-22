<?php 
session_start();
require_once "setting.php";

//login
if( !isset( $_SESSION[ NANO_CMS_ADMIN_LOGGED ] ) ) {
	if( isset($_POST[user]) ) {
		if( $_POST[user] == NANO_CMS_ADMIN_USER and $_POST[pass] == NANO_CMS_ADMIN_PASS ) {
			$_SESSION[ NANO_CMS_ADMIN_LOGGED ] = NANO_CMS_ADMIN_PASS; //die('done');
		}
	}
}
//logout
if( isset( $_GET[ logout ] ) )	{
	echo('logged out<br />');
	unset( $_SESSION[NANO_CMS_ADMIN_LOGGED] );
}
//the login form
if( $_SESSION[NANO_CMS_ADMIN_LOGGED]!=NANO_CMS_ADMIN_PASS or !isset( $_SESSION[NANO_CMS_ADMIN_LOGGED] ) )
{
	session_destroy();
	echo "<html><head><title>NanoCMS Admin login</title></head><body style='font-family:courier'>";
	echo "<form action='?' method='post'>username <input type=text name='user'><br />password <input type=password name='pass'><br />
		  <input type=submit value='Login'>";
	exit();
}


function editForm()
{
	global $CurrentActivePage,$WebPagesList,$editErrMsg;
	$slug = $_GET[slug];
	foreach( $editErrMsg as $err ) echo " - $err<br />";
	echo "<h2>Edit Content : ".getTitle( $CurrentActivePage )."</h2>";
	EditCreateForm( 'edit', $CurrentActivePage );
}

function EditCreateForm($do,$defaultPage) {
	if( $_GET[editor]!='text' )
		loadWysiwyg();
	
	$fileName = pageDataDir( getSlug($defaultPage) );
	$slug = $_GET[slug];
	if( $do=='edit') {
		$insidebar = inSidebar($defaultPage);
		$formAction = "?action=edit&slug=$slug";
		$fileContent = file_exists( $fileName )? htmlentities( file_get_contents( $fileName ) ) : "no content yet";
		$submitButton = 'Save Page';
		$title = getTitle( $defaultPage );
	}
	else {
		$insidebar= true; //default is sidebar
		$formAction = "?action=addpage";
		$fileContent = '';
		$submitButton = 'Add Page';
		$title = '';
	}
		echo "<form action='$formAction' method=post>";
		echo "Page Title : <input type='text' value='$title' name='title'>";
		echo "Type of page : 
				<select name='insidebar'>
					<option value=1 ".($insidebar==true?' selected ':'').">Sidebar Page</option>
					<option value=0 ".($insidebar==false?' selected ':'').">Normal Page</option>
				</select>
			 <br />Content:<br />";
		echo "<textarea name=content rows=20 cols=70 id='editbox' class='editbox'>$fileContent</textarea>";
		echo "<br /><input type=submit value='$submitButton' name=save>";
		echo "</form>";
		if( !isloadedWysiwyg() )
		{
			echo "<input type='button' onclick='makebig(\"editbox\")' value='Bigger Input'>";
			echo "<input type='button' onclick='makesmall(\"editbox\")' value='Smaller Input'>";
			echo "<script langua='javascript'>
					function makebig(id) {
					obj = document.getElementById(id);
					if( obj.rows < 40 ) obj.rows+= 5;
					}
					function makesmall(id) {
					obj = document.getElementById(id);
					if( obj.rows > 15 ) obj.rows-= 5;
					}
				  </script>";
		}
		else
		{
			$s = $_SERVER['QUERY_STRING'].'&editor=text';
			echo "<input type='button' onclick='window.location=\"?$s\"' value='Text Edit'>";
		}
		
		showWysiwyg();
}

function showedit() {
	if( isset($_GET[action]) and $_GET[action]=='edit' )
		editForm();
}

function performEdit() {
	if( $_GET[action] != 'edit' ) return;
	global $CurrentActivePage,$WebPagesList,$editErrMsg;
	$slug = $_GET[slug];
	$editErrMsg = array();
	if( isset( $_POST[save] ) )
	{
		$insidebar = $_POST[insidebar];
		$newData = newPage( stripslashes($_POST[title]) );
		$fileName = pageDataDir($slug);
		$newName =  pageDataDir( getSlug($newData) );
		$oldSlug = $slug;
		$newSlug = $newData[pageslug];

		if( getSlug($newData)=='' ) { $editErrMsg[]="Title cannot be empty"; return; }
		if( !put2file( $fileName, stripslashes( $_POST[content] ) ) ) { $editErrMsg[]="error writing to file"; return; }
		if( $newData[pageslug]!=$slug or $insidebar!=inSidebar( $CurrentActivePage ) )
		{
			if( $newData[pageslug]!=$slug ) // if it is only rename operation
			{
				if( file_exists( $newName ) ) { $editErrMsg[] = "cannot rename : a similar file exists"; return; }
				if( !rename($fileName,$newName) ) { $editErrMsg[]="error renaming"; return; }
			}
			
			$WebPagesList = msort( $WebPagesList );
			foreach( $WebPagesList as $k=>$pg ) {
				if( getSlug( $pg ) == $oldSlug ) {
						$WebPagesList[$k][title] = $newData[title];
						$WebPagesList[$k][pageslug] = $newData[pageslug];
						$WebPagesList[$k][inSidebar] = $insidebar;
						$CurrentActivePage = $WebPagesList[$k]; // the current page details changed so reload changes
						savepages();
						header("location:nanoadmin.php?action=edit&slug=$newSlug".(isset($_GET[editor])?"&editor=$_GET[editor]":"") );
						break;
				}
			}
			
		}//change only if title is changed
	}
}
function addpage()
{
	global $CurrentActivePage,$WebPagesList;
	$slug = $_GET[slug];
	$insidebar = true;
	
	if( isset( $_POST[save] ) )
	{
		$content = stripslashes($_POST[content]);
		$insidebar = $_POST[insidebar]=='1'?true:false;
		$title = stripslashes($_POST[title]);
		
		if( $title != '' and $content !='' )
		{
			$pdetails = newPage($title,'',$insidebar );
			$WebPagesList[] = $pdetails;
			$slug = $pdetails[pageslug];
			$newPageFile = pageDataDir($slug);
			if( !file_exists($newPageFile)  )
			{
				put2file( $newPageFile, $content );
				savepages();
				echo "The page was created successfully<br />";
				echo "File Created : ".$newPageFile."<br />";
				echo "Content : ".substr( strip_tags($content), 0, 100 ).( strlen($content)>100 ? '...' : '' )."<br />";
				return;
			}
			else
			{
				echo "save failed";
				if( file_exists($content) )
					echo " - a page with similar title already exists";
			}
		}
		else
		echo "Either the title or the content is/are empty!!! Please check your input!!<br />";
	}
	
	echo "<h2>Add a new page</h2>";
	EditCreateForm( 'new', newPage('') );
}

function showadd() {
	if( isset($_GET[action]) and $_GET[action]=='addpage' )
		addpage();
}

function performMove() {
	if( in_array($_GET[action],array('moveup','movedown') ) ) {
		if( $_GET[action]=='moveup' ) moveup();
		if( $_GET[action]=='movedown' ) movedown();
		header( "Location:nanoadmin.php?action=showpages" );
	}
}

function moveup() {
	global $WebPagesList,$CurrentActivePage;
	$slug = $_GET[slug];
	$WebPagesList = msort( $WebPagesList );
	foreach( $WebPagesList as $k=>$pg ) {
		if( !inSidebar( $pg ) ) continue; // no need to consider non sidebar pages
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

// the whole process is same as the moveup() but we are moving the target up in the reverse order
function movedown() {
	global $WebPagesList,$CurrentActivePage;
	$slug = $_GET[slug];
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
			if( getSlug( $pg ) == $slug ) {
				if( $k == 0 ) { echo "cannot delete the Top page"; return; } // we cannot delete the topmost one as it is the homepage
				unset( $WebPagesList[$k] );
				unlink( pageDataDir(getSlug($pg)) );
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
	$p = msort( $WebPagesList );
	echo "<table border=1 cellpadding=5>";
	echo "<tr class='th'><th>Page</th><th>Options</th><th colspan=2>Move</th><th>URL you can use</th></tr>";
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
	echo "<tr  class='th'><th>Page</th><th>Edit</th><th>URL you can use</th></tr>";
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


function doAreaEdit() {
	if( isset($_GET[action]) and $_GET[action]=='editarea' ) {
		$areaName = stripslashes($_POST[areaname]);
		$content = stripslashes($_POST[content]);
		$areaFile = areaDataDir( "$areaName" );
		//die( $areaFile );
		if(!put2file( $areaFile, $content ) ) echo("error saving area");
		header("location:?action=showareas");
	}
}

function showareas() {
	global $indexTemplateAreas;
	//execute the nano site in demo to read the content areas
	demoExecuteNanoSite();
	$contents = $indexTemplateAreas;
	$contents = array_unique( $contents );
	foreach( $contents as $areaName )
	{
		$areaFile = areaDataDir( "$areaName" );
		$fileContent = file_exists($areaFile) ? file_get_contents( $areaFile ) : '';
		$boxId = md5($areaFile);
		echo "<h2>$areaName</h2>
			  <form action='?action=editarea' method='post'>
				<textarea name='content' rows='2' cols='60' id='$boxId' class='areabox'>".htmlentities( $fileContent )."</textarea>
				<input type='hidden' name='areaname' value='$areaName'><br />
				<input type='submit' value='Save'>
				<input type='button' onclick='makebig(\"$boxId\")' value='  +  ' title='Bigger Input'>
				<input type='button' onclick='makesmall(\"$boxId\")' value='  -  ' title='Smaller Input'>
				<input type='button' onclick='makenarrow(\"$boxId\")' value='  <  ' title='Narrower Input'>
				<input type='button' onclick='makewide(\"$boxId\")' value='  >  ' title='Wider Input'>
			  </form>";
	}
	echo "<script langua='javascript'>
			function makebig(id) {
			obj = document.getElementById(id);
			if( obj.rows < 30 ) obj.rows+= 3;
			}
			function makesmall(id) {
			obj = document.getElementById(id);
			if( obj.rows > 3 ) obj.rows-= 3;
			}
			function makewide(id) {
			obj = document.getElementById(id);
			if( obj.cols < 90 ) obj.cols+= 5;
			}
			function makenarrow(id) {
			obj = document.getElementById(id);
			if( obj.cols > 60 ) obj.cols-= 5;
			}
		  </script>";
}


// automatically gets the content_areas present in the template ////////////////////////////////
$indexTemplateAreas = array();
function readIntoAreaList($l,$a='',$b='') {
	global $indexTemplateAreas;
	$indexTemplateAreas[] = $l;
}
function dummyFunction($a='',$b='',$c='',$d='',$e='',$f=''){}
function demoExecuteNanoSite() {
	$removeFunctionList = array('show_sidebar','show_content_slug','show_title','require_once');
	$replaceFunction = 'dummyFunction';
	$demoContentToRun = file_get_contents( NANO_CMS_PAGE );
	$demoContentToRun = str_replace( 'show_content_area', 'readIntoAreaList', $demoContentToRun );
	$demoContentToRun = str_replace( $removeFunctionList, $replaceFunction, $demoContentToRun );

	ob_start();
	eval(" ?> ".$demoContentToRun." <?php ");
	$cont = ob_get_contents();
	ob_end_clean();
}

performMove();
doAreaEdit();
performEdit();
// include the template of the admin area :)  ///////////////////////////////////////////////////
	require_once "data/admindesign.php"; 
?>
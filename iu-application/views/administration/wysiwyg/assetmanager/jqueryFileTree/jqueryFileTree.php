<?php
//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//

error_reporting(0);

require "../../../../../external/index.php";
$_IU = &get_instance();

if (!$_IU->loginmanager->is_logged_in()) {
	die('You need to be logged in to continue');
}

$root=$_SERVER["DOCUMENT_ROOT"];

$_POST['dir'] = urldecode($_POST['dir']);

if( file_exists($root . $_POST['dir']) ) {
	$files = scandir($root . $_POST['dir']);
	natcasesort($files);
	if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
		// All dirs
		foreach( $files as $file ) {
			//skip CMS' folders
			if (in_array($file, array('iu-application', 'iu-system', 'iu-resources', 'install')))
				continue;
				
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
				echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
			}
		}
		// All files
		foreach( $files as $file ) {
			//skip files starting with dot
			if (strpos($file, '.') === 0)
				continue;
			
			//skip html/php files (not used atm)
			/*$ext = end(explode('.', $file));
			
			if (in_array($ext, array('htm', 'html', 'php', 'phtml', 'php3', 'php4', 'php5', 'php6')))
				continue;//*/
			
			if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
				$ext = preg_replace('/^.*\./', '', $file);
				echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
			}
		}
		echo "</ul>";
	}
}

?>
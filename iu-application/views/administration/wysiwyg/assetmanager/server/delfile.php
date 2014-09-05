<?php
require "../../../../../external/index.php";
$_IU = &get_instance();

if (!$_IU->loginmanager->is_logged_in()) {
	die('You need to be logged in to continue');
}

$root=$_SERVER["DOCUMENT_ROOT"];
$file = $root . $_POST["file"];

if(file_exists ($file)) {
	unlink($file);
} else {

}

echo $_POST["file"];

?>
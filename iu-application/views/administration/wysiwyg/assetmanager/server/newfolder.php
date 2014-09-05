<?php
require "../../../../../external/index.php";
$_IU = &get_instance();

if (!$_IU->loginmanager->is_logged_in()) {
	die('You need to be logged in to continue');
}

$root=$_SERVER["DOCUMENT_ROOT"];
$newfolder = $root . $_POST["folder"];

$parent = dirname($newfolder);

if(!is_writable($parent)) {
	echo "Write permission required";
	exit();
}

if(!file_exists ($newfolder)) {
	//create the folder
	mkdir($newfolder);
} else {
	echo "Folder already exists.";
}
?>
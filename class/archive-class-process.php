<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
OpenSession();

if(isset($_POST['class_id']) && !empty($_POST['class_id'])){
	$archive_process_message = InputArchiveClass($_POST['class_id'], $_SESSION['user_id']);
}

if(isset($_POST['archive_class_id']) && !empty($_POST['archive_class_id'])){
	$archive_process_message = DeleteArchiveClass($_POST['archive_class_id']);
}

echo $archive_process_message;
?>
<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open connection
$conn = OpenCon();

if(isset($_POST['note_id'])){
	$archive_note_message = ArchiveUserNote($_POST['note_id'], $_POST['user_id'], $conn);
	echo $archive_note_message;
}

if(isset($_POST['archive_user_note_id'])){
	$archive_note_message = RestoreArchiveUserNote($_POST['archive_user_note_id'], $conn);
	echo $archive_note_message;
}

?>
<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open connection
$conn = OpenCon();

if(isset($_POST['note_id'])){
	$archive_note_message = ArchiveNote($_POST['note_id'], $_POST['member_id'], $conn);
	echo $archive_note_message;
}

if(isset($_POST['archive_note_id'])){
	$archive_note_message = RestoreArchiveNote($_POST['archive_note_id'], $conn);
	echo $archive_note_message;
}

?>
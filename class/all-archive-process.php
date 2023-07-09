<?php
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open connection
$conn = OpenCon();

if (isset($_POST['note_id'])) {
	$archive_note_message = ArchiveNoteFromAll($_POST['note_id'], $_POST['class_id'], $_POST['user_id'], $conn);
	echo $archive_note_message;
}

if (isset($_POST['archive_note_id'])) {
	$archive_note_message = RestoreArchiveNoteFromAll($_POST['archive_note_id'], $conn);
	echo $archive_note_message;
}

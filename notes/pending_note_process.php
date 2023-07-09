<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';

$note_id = NULL;
if(isset($_POST['note_id']) && !empty($_POST['note_id'])){
		$note_id = $_POST['note_id'];
}

if (isset($_POST['pending_note_id'])) {
	$pending_note_message = ProcessPendingNote($_POST['pending_note_id'], $note_id, $_POST['status']);
}
echo $pending_note_message;
?>
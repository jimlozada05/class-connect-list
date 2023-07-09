<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';

if(isset($_POST['note_id'])){
	$delete_note_message = DeleteNote($_POST['note_id']);
}

echo $delete_note_message;
?>
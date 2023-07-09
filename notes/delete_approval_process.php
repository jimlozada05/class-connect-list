<?php 
include '../dbconnect.php';

if(isset($_POST['pending_note_id'])){
	$pending_message = DeletePendingNote($_POST['pending_note_id']);
	echo $pending_message;
}
?>
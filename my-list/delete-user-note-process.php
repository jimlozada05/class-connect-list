<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open database connection for the nl2br
$conn = OpenCon();

// If user_id is set then it means it is a user note
// user_id is from the ajax pass
if(isset($_POST['note_id']) && isset($_POST['user_id']) && !empty($_POST['user_id'])){
	$delete_note_message = DeleteUserNote($_POST['note_id'], $conn);
}
// If user_id is not set then it was added to mylist
else{
	$delete_note_message = RemoveUserNote($_POST['note_id'], $conn);
}
echo $delete_note_message;
?>
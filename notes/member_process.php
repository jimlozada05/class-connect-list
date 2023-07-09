<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';

// Change member type since they have a member type of 1 or 0
if($_POST['member_type'] == 1 || $_POST['member_type'] == 0){
	$process_message = UpdateMember($_POST['member_id'], $_POST['member_type']);
}
// Remove member as there is no member type passed as POST
else if($_POST['member_type'] == null){
	$process_message = DeleteMember($_POST['member_id']);
}

echo $process_message;
?>
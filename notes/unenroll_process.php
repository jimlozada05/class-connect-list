<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';

// Enroll process == 1
if($_POST['process_type'] == 1){
	$process_message = DeleteUnenroll($_POST['unenroll_id']);
}
// Unenroll process == 0
else if($_POST['process_type'] == 0){
	
	$process_message = InsertUnenroll($_POST['member_id'], $_POST['subject_id']);
}

echo $process_message;

?>
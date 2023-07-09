<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open database connection for the nl2br
$conn = OpenCon();
OpenSession();

// Process the details to include spaces and symbols
$event_details = null;
if(isset($_POST['event_details']) && !empty($_POST['event_details'])){
    $event_details = mysqli_real_escape_string($conn, nl2br($_POST['event_details']));
}

// Check if event ID has a value or not and the title the process is to UPDATE
if((isset($_POST['event_id']) && !empty($_POST['event_id'])) && (isset($_POST['event_title']) && !empty($_POST['event_title']))){
    $event_process_message = UpdateUserCalendar($_POST['event_id'], $_POST['event_title'], $_POST['event_details'], $_POST['event_from_date'], $_POST['event_to_date'], $_SESSION['user_id'], $conn);
}
// If event ID doesn't have a value to process is to INSERT and the event title has a value
else if((isset($_POST['event_title']) && !empty($_POST['event_title'])) && !(isset($_POST['event_id']) && !empty($_POST['event_id']))){
    $event_process_message = InsertUserCalendar($_POST['event_title'], $_POST['event_details'], $_POST['event_from_date'], $_POST['event_to_date'], $_SESSION['user_id'], $conn);
}
// If event ID exist but there is no title the process is to DELTE
else if(!(isset($_POST['event_title']) && !empty($_POST['event_title'])) && (isset($_POST['event_id']) && !empty($_POST['event_id']))){
    $event_process_message = DeleteUserCalendar($_POST['event_id'], $conn);
}

echo $event_process_message;
?>
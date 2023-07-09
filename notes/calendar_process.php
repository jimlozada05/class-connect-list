<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open database connection for the nl2br
$conn = OpenCon();

$description = null;
if(isset($_POST['description']) && !empty($_POST['description'])){
    $description = mysqli_real_escape_string($conn, nl2br($_POST['description']));
}


$subject_id = null;
if(isset($_POST['subject_id']) && !empty($_POST['subject_id'])){
    // Check if the subject_id is not zero, if not assign proper value instead of null
    if ($_POST['subject_id'] != "0") {
        $subject_id = $_POST['subject_id'];
    }
}

// Check if event ID has a value or not and the title the process is to UPDATE
if((isset($_POST['event_id']) && !empty($_POST['event_id'])) && (isset($_POST['event_title']) && !empty($_POST['event_title']))){
    $event_process_message = UpdateClassCalendarEvent($_POST['event_id'], $_POST['event_title'], $_POST['description'], $_POST['start_datetime'], $_POST['end_datetime'], $_POST['class_id'], $subject_id, $conn);
}
// If event ID doesn't have a value to process is to INSERT
else if((isset($_POST['event_title']) && !empty($_POST['event_title'])) && !(isset($_POST['event_id']) && !empty($_POST['event_id']))){
    $event_process_message = InsertClassCalendarEvent($_POST['event_title'], $_POST['description'], $_POST['start_datetime'], $_POST['end_datetime'], $_POST['class_id'], $subject_id, $conn);
}
// If event ID exist but there is no title the process is to DELTE
else if(!(isset($_POST['event_title']) && !empty($_POST['event_title'])) && (isset($_POST['event_id']) && !empty($_POST['event_id']))){
    $event_process_message = DeleteClassCalendarEvent($_POST['event_id'], $conn);
}

echo $event_process_message;
?>
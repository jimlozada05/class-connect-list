<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Open database connection for the nl2br
$conn = OpenCon();

if(isset($_POST['edit_note'])){
    $edit_note_message = json_encode(SelectUserNoteRecord($_POST['edit_note']));
}

$description = '';
// Check if description is empty if not properly put the strings for SQL format
if(isset($_POST['description'])){
	//nl2br converts the line breaks
	//mysqli_real_escape_string converts the special characters
	$description = mysqli_real_escape_string($conn, nl2br($_POST['description']));
}

$due_date = null;
// Check if due date is null
if(isset($_POST['due_date']) && !empty($_POST['due_date'])){
	$due_date = $_POST['due_date'];
}

$due_time = null;
// Check if due date is null
if(isset($_POST['due_time']) && !empty($_POST['due_time']) && isset($_POST['due_date']) && !empty($_POST['due_date'])){
	$due_time = $_POST['due_time'];
}

if(isset($_POST['note_title'])){
	$edit_note_message = UpdateUserNote($_POST['note_id'], $_POST['user_id'], $due_date, $due_time, $_POST['note_title'], $description, $conn);
}

echo $edit_note_message;
?>
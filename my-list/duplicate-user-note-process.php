<?php
include '../dbconnect.php';
$conn = OpenCon();
$note_id = $_POST['note_id'];
$user_id = $_POST['user_id'];
$sql = "SELECT * FROM class_to_user_note WHERE note_id='$note_id' AND user_id='$user_id'";
$result = $conn->query($sql);
echo $result->num_rows;
$conn->close();
<?php
include '../dbconnect.php';
OpenSession();

$join_message = InsertMemberJoin($_POST['class_code'], $_SESSION['user_id']);
// The join message is the class_id returned
echo $join_message;
?>
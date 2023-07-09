<?php
require_once '../dbconnect.php';
OpenSession();
$newPass = $_POST['new_pass'] && "";
$email = $_SESSION['user_id'];

updatePassword($email, $newPass);
session_destroy();
echo "Success";
?>
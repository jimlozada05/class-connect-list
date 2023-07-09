<?php
include '../dbconnect.php';
if (session_status() === PHP_SESSION_NONE) {
    // Start session
    session_start();
}

// Check if the email and password filled out has a value and is not empty
if(isset($_POST['email']) && isset($_POST['password']) && !empty($_POST['email']) && !empty($_POST['password'])){
	// Check if the email exist in the database
	$email_result = checkEmail($_POST['email']);

	// if the email doesn't exist display warning
	if($email_result == NULL){
		echo "Incorrect Email!";
	}
	else{
		// Check if the password and hash password is the same
		if(password_verify($_POST['password'], $email_result['password'])){
			// Set the user_id session
			$_SESSION['user_id'] = $email_result['user_id'];
			echo "Login Successful";
		}
		// If the password doesn't match warn the user
		else{
			echo "Incorrect password!";
		}
	}
}
?>
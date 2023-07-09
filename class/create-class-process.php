<?php 
include '../dbconnect.php';
OpenSession();

$confirm_class_code = 1;
while ($confirm_class_code = 1) {
	$class_code = bin2hex(random_bytes(5));;
	// Check on the database if this randomly generated class code already exist in the database
	$confirm_class_code = checkClassCode($class_code);
	if ($confirm_class_code == 0){
		break;
	}
}


$school_year = $_POST['school_year']."-01-01";

// Check if the class_name have values and it is not an empty value
if(isset($_POST['class_name']) && !empty($_POST['class_name'])){
	// Check if the class name and school year already exist 
	$class_exist = CheckClassExist($_POST['class_name'], $school_year);
	if($class_exist == TRUE){
		// Since it already exist, notify the user then go back to the create-class.php
		echo "<script type=\"text/javascript\">
		alert('Class with the same name year already exist!');
		history.back();
		</script>";
	}
	else if($class_exist == FALSE){
		$class_id = InputClass(
			$_POST['class_name'], 
			$class_code, 
			$_SESSION['user_id'], 
			$school_year);
	}
}

// Process to check if the class_name and the school year exist at the same time on a single record, if yes say that the class already exist

// Check if the class_id returned has a value
if($class_id > 0){
	// Redirect to the add subject page
	// header("location: add_subject.php?class_id=$class_id");
	header("location: manage-class.php?class_id=$class_id");
}
?>
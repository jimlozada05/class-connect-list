<?php
include "../dbconnect.php";
// Check if session exist
OpenSession();

// Check if the user is in a class
if(checkClassJoin($_SESSION['user_id']) == TRUE){
    header("location: with-class.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/sections.css">
</head>

<body>
    <?php DisplayNavHeader();?>

    <div class="para">
        <p class="p1">Resources cannot be accessed.</p>
        <br>
        <p class="p1">You have not joined any classes, yet.</p>
        <p class="p2">Are you a class officer? Click the button below to create a classroom!</p>
        <div class="btns">
            <button><a class="create" href="../class/create-class.php">CREATE CLASS</a></button>
            <button><a class="join" href="../class/join-class.php">JOIN CLASS</a></button>
        </div>
    </div>

</body>

</html>
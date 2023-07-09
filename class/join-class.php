<?php
include "../dbconnect.php";
// Check if session exist
OpenSession();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Class - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/create.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>
    <!-- CONTENT -->
    <div class="create-container">
        <p>JOIN CLASS</p>
        <div class="create-form">
            <form id="formJoinClass">
                <div class="cp">
                    <p class="details">Enter the classroom code of the desired class.</p>
                    <p class="details">Doing so will send a join request towards the class officers.<br></p>
                </div>
                <div>
                    <input type="text" placeholder="Enter Class Code" name="class_code" class="form-control" required>
                </div>
                <button type="submit" class="btn">JOIN CLASS</button>
        </div>
        </form>
    </div>

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Get the form value and send to the with-class-process for joining class -->
<script type="text/javascript">
$("#formJoinClass").submit(function(e){
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: 'POST',
        url: 'with-class-process.php',
        data: form.serialize(),
        success: function(data){
            if(data > 0){
                alert("Class joined successfully!");
                // window.location.replace("../notes/note.php?class_id=" + data);
                window.location.replace("with-class.php");
            }
            else if(data == 0){
                alert("Class doesn't exist!");
            }
            else{
                alert(data);
            }
        }
    });
});
</script>
</body>
</html>
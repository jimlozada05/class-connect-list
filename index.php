<?php 
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }

if (isset($_SESSION["user_id"])) {
    header("Location: class/no-class.php");
    die();
  }
  
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">

</head>
<body>
    <div class="container">
        <div class="content">
            <p class="title">CLASS CONNECT: LIST</p><br>
            <p class="para">A centralized checklist for students,<br>
            made by students.</p><br>
            <!--<label class="loginMessage"></label>-->
        </div>

        <div class="form">
            <form id="loginUser">
                <label>EMAIL</label>
                <input type="email" name="email" placeholder="Example@gmail.com" required>
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Password" required>
                <div class="loginMessage"></div>
                <button class="loginbtn" type="submit" name="submit" >LOGIN</button>
                <button class="forgot_pass" onclick="location.href='user/forgot-password.php'">Forgot password?</button>
                <hr>
                <button class="createbtn" onclick="location.href='user/signup.php'">CREATE NEW ACCOUNT</button>
            </form>
        </div>
    </div>

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">
$("#loginUser").submit(function(e){
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: 'POST',
        url: 'user/login.process.php',
        data: form.serialize(),
        success: function(data){
            if(data == "Login Successful"){
                window.location.replace("class/no-class.php");
            }
            else{
                $(".loginMessage").html(data);
            }
        }
    });
});
</script>

</body>
</html>
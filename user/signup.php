<?php 
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
  }

if (isset($_SESSION["user_id"])) {
    header("Location: ../class/no-class.php");
    die();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>
<body>
    <div class="details">
        <div class="content">
            <p class="title">SIGN-UP</p><br>
            <p class="para">Create an account to manage your<br>
            to-dos.</p><br>
        </div>

        <div class="content2">
            <form id="signupUser">
                <div class="form1">
                    <div style="flex-grow: 8">
                        <label>LAST NAME</label>
                        <input type="text" name="lname" placeholder="ex. Dela Cruz" required>
                    </div>
                    <div style="flex-grow: 8">
                        <label>FIRST NAME</label>
                        <input type="text" name="fname" placeholder="ex. Juan" required>
                    </div>
                    <div style="flex-grow: 1">
                        <label>M.I</label>
                        <input id="mi" type="text" name="mi" placeholder="ex. A">
                    </div>
                </div>
                <div class="form2">
                    <div class="part1">
                        <div>
                            <label>CONTACT NUMBER</label>
                            <input type="tel" name="contact" placeholder="ex. 09123456780" pattern="[0-9]{11}" required>
                        </div>
                        <div>
                            <label>EMAIL</label>
                            <input type="email" name="email" placeholder="example@gmail.com" required>
                        </div>
                    </div>
                    <div class="part2">
                        <div>
                            <label>PASSWORD</label>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>                    
                        <div>
                            <label>CONFIRM PASSWORD</label>
                            <input type="password" name="repassword" placeholder="Confirm Password" required>
                        </div>
                    </div>
                    <div class="part3">                        
                        <div class="loginMessage"></div>
                        <button class="regbtn" type="submit" name="submit" >REGISTER</button>
                        <hr>
                        <button class="lrdbtn" onclick="location.href='../index.php'">ALREADY HAVE AN ACCOUNT?</button>
                    </div>
                </div>
            </form>
        </div>  
    </div>

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">
$("#signupUser").submit(function(e){
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: 'POST',
        url: '../user/signup.process.php',
        data: form.serialize(),
        success: function(data){
            if((data == "Email Already Exist") || (data == "Contact Number Already Taken") || (data == "Password Does Not Match")){
                $(".loginMessage").html(data);
            }
            else if (data == "Success"){
                alert("Account Successfully Created");
                window.location.replace("../index.php");
            }
            // else{
            //     $(".loginMessage").html(data);
            // }
        }
    });
});
</script>
<script>
        $(function() {
            $('#mi').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });
        });
    </script> 

</body>
</html>
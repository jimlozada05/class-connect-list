<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password - Class Connect List</title>
    <link rel="stylesheet" href="../css/login.css">
</head>

<body>
    <div class="pass-case">
        <p class="title fp">CLASS CONNECT: LIST</p><br>

        <div class="pass-form">
            <p class="title2">FORGOT PASSWORD</p>
            <form id="forgotPassword" method="GET" action="./new-password.php">
                <label>EMAIL</label>
                <input type="email" class="email" type="email" id="email" name="email" placeholder="Example@gmail.com" required>
                <button class="createbtn otp mid" type="button" name="submit" onclick="sendOTP()">SEND OTP</button>
            </form>
            <hr>
            <form id="loginUser">
                <label>ENTER OTP (DO NOT SHARE THE OTP)</label>
                <input type="password" class="email" id="otp" name="otp" placeholder="" required>
                <button class="createbtn otp" type="button" onclick="verifyOTP()" name="submit">VERIFY OTP</button>
            </form>
            <div class="center">
                <button class="fp2" onclick="location.href='../index.php'">Back to Login</button>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript">
        function maskInput(input) {
            const maskedValue = input.value.replace(/./g, '*');
            input.value = maskedValue;
        }

        function sendOTP() {
            const email = document.getElementById('email').value;
            $.ajax({
                url: './forgot-password-process.php',
                type: 'POST',
                data: {
                    email_value: email
                }
            }).done(function(result) {
                alert(result);
            });
        }

        function verifyOTP() {
            const otp = document.getElementById('otp').value;
            const email = document.getElementById('email').value;
            $.ajax({
                url: './forgot-password-process.php',
                type: 'POST',
                data: {
                    verify_otp: otp, email: email
                }
            }).done(function(result) {
                if (result === "Fail") {
                    alert('Invalid/Incorrect OTP.');
                } else {
                    // document.getElementById('forgotPassword').submit();
                    window.location.replace('/user/new-password.php');
                }
            });
        }
    </script>
</body>

</html>
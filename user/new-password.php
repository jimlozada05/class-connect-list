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
        <div class="new pass-form">
        <p class="title2">CHANGE PASSWORD</p>
            <form id="change-pass">
                <label>NEW PASSWORD</label>
                <input class="email" type="password" id="new_pass" name="email" placeholder="New Password" required>
                <label>CONFIRM NEW PASSWORD</label>
                <input class="email" type="password" id="confirm_pass" name="email" placeholder="Confirm New Password" required>
                <button class="createbtn otp mid" type="button" name="submit" onclick="setNewPassword()">CONFIRM</button>
            </form>
        </div>
    </div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function setNewPassword() {
        const newPass = document.getElementById('new_pass').value;
        const confirmPass = document.getElementById('confirm_pass').value;
        if (newPass === confirmPass) {
            $.ajax({
                url: './new-password-process.php',
                type: 'POST',
                data: {
                    new_pass: newPass
                }
            }).done(function(result) {
                if (result === "Success") {
                    alert("Password has been updated.")
                    location.href = "../index.php";
                } else {
                    alert(result)
                }
            });
        } else {
            alert("Fields must match.");
        }
    }
</script>

</html>
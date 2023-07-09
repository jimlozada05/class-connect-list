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
    <title>Create Class - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/create.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>
    <!-- CONTENT -->
    <div class="create-container">
        <p>CREATE CLASSROOM</p>
        <div class="create-form">
        <button type="button" id="c" onClick="location.href='with-class.php'">X</button>
            <form method="POST" action="create-class-process.php">
                <div class="cp">
                    <label><span>CLASS PRESIDENT</span></label>
                    <input type="text" id="creator" value="<?php echo SelectUserName($_SESSION['user_id']); ?>" class="class-pres" readonly />
                </div>
                <div>
                    <label>NAME OF SECTION</label>
                    <input type="text" id="class_name" name="class_name" placeholder="e.g. BSIT 4-2" class="form-control" required>
                </div>
                <div>
                    <label>SCHOOL YEAR</label>
                    <input type="number" id="school_year" name="school_year" min="1900" max="2099" placeholder="e.g. 2022" required>
                </div>
                <button type="submit" class="btn">CREATE CLASS</button>
            </form>
        </div>
    </div>
</body>

</html>
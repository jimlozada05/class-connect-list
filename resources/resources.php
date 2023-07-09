<?php
include "../dbconnect.php";
OpenSession();

// Check if the user is in a class
if (checkClassJoin($_SESSION['user_id']) == FALSE) {
    header("location: resources-no-class.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/resources.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>

    <p class="title-resources">RESOURCES</p>
    <div class="resource-case">
        <?php
        $class_info = GetClass($_SESSION['user_id']);
        while ($rows = $class_info->fetch_assoc()):?>
                <!-- Enrolled Class -->
                <div class="resource-card">
                    <p class="class-title"><?php echo $rows['class_name']; ?></p>
                    <hr width="100%" color="white">
                    
                    <button class="class-view-btn" onclick="location.href='resources-subjects.php?class_code=<?php echo $rows['class_code']; ?>'">VIEW</button>
                </div>
        <?php endwhile ?>
    </div>

</body>

</html>
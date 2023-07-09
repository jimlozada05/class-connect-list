<?php
include "../dbconnect.php";
OpenSession();

$class_info = GetClassRecord($_GET['class_code']);
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
    <p class="subject-name"><?php echo $class_info['class_name']; ?></p>

    <div class="resource-case">
        <div class="resource-card">
            <div class="class-title-box subject">
                <p class="class-title">General</p>
            </div>
            <p class="class-code"></p>
            <hr width="100%" color="white">
            <button class="class-view-btn" onclick="location.href='resources-subjects-archives.php?class_id=<?php echo $class_info['class_id'] ?>'">VIEW</button>
        </div>

        <?php
        $subj = SelectClassSubjectList($_GET['class_code']);
        while ($subj_row = $subj->fetch_assoc()):?>
            <!-- Subjects of the class -->
            <div class="resource-card">
                <div class="class-title-box subject">
                    <p class="class-title"><?php echo $subj_row['subject_name']; ?></p>
                </div>
                <p class="class-code"><?php echo $subj_row['subject_details']; ?></p>
                <hr width="100%" color="white">
                <button class="class-view-btn" onclick="location.href='resources-subjects-archives.php?subject_id=<?php echo $subj_row['subject_id'] ?>'">VIEW</button>
            </div>
        <?php endwhile ?>
    </div>
</body>

</html>
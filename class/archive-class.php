<?php
include "../dbconnect.php";
OpenSession();

// Check if the user is in a class
if (checkClassJoin($_SESSION['user_id']) == FALSE) {
    header("location: no-class.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Classes - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/sections.css">
    <link rel="stylesheet" href="../css/with-class.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>
    <div class="archive-container">
        <div class="website-content">
            <p class="archive-title">ARCHIVED CLASSES
            <p>
        </div>
    </div>

    <div class="case">
            <?php
            $class_info = GetArchivedClass($_SESSION['user_id']);
            while ($rows = $class_info->fetch_assoc()) {    ?>
                <div class="card">
                    <div class="cname">
                        <p><?php echo $rows['class_name']; ?></p>
                    </div>
                    <p>Class Code: <span><?php echo $rows['class_code']; ?></span></p></br>
                    <p>SY: <?php
                            echo date("Y", strtotime($rows['school_year']));
                            echo "-";
                            echo date("Y", strtotime($rows['school_year'])) + 1; ?></p>
                    <form method="GET" action="note.php">
                        <input type="hidden" name="class_id" value="<?php echo $rows['class_id']; ?>">
                    </form>
                    </br>
                    <div class="unarchive-btn">
                        <button type="button" class="view" data-id="<?php echo $rows['archive_class_id']; ?>" onClick="RestoreClass(this)">RESTORE CLASS</button>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script type="text/javascript">
function RestoreClass(e){
    var archive_class_id = $(e).attr("data-id");

    $.ajax({
        type: 'POST',
        url: 'archive-class-process.php',
        data: {
            "archive_class_id": archive_class_id
        },
        success: function(data){
            alert(data);
            location.reload();
        }
    });
}
</script>

</body>
</html>
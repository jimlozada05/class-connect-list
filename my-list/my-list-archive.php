<?php
include "../dbconnect.php";
OpenSession();

// Check if the user is in a class
if (checkClassJoin($_SESSION['user_id']) == FALSE) {
    header("location: ../class/no-class.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My List - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/manage-class.css">
    <link rel="stylesheet" href="../css/my-list.css">
    <script src="https://kit.fontawesome.com/c11ce9a287.js" crossorigin="anonymous"></script>
</head>

<body id="scrll">
    <?php DisplayNavHeader(); ?>

    <p class="subj">MY LIST ARCHIVE</p>

    <div class="buttons">
        <button class="button" onclick="location.href='my-list.php'">GO BACK</button>
    </div>

    <!-- DISPLAY TO-DOs -->
    <div class="listcase">
        <?php $user_note_info = GetUserNoteArchive($_SESSION['user_id']);

        while ($rows = $user_note_info->fetch_assoc()) {    ?>
            <div class="listcard">
                <div class="notetitle">
                    <p><?php echo $rows['note_title']; ?></p>
                </div>
                <p>Post Date: <span><?php echo $rows['post_date']; ?></span></p>

                <?php if (isset($rows['due_date'])) { ?>
                    <p style="color:#D53F3A">Due Date: <span><?php echo $rows['due_date']; ?></span></p></br>
                <?php } ?>

                <div class="notedetails">
                    <p>
                        <?php
                        $description = $rows['description'];
                        if (!empty($description)) {
                            // Split the text into an array of words
                            $words = explode(' ', $description);
                            // Limit the array to the first 20 words
                            $limitedWords = array_slice($words, 0, 20);
                            // Join the words back into a string and echo the result
                            echo implode(' ', $limitedWords) . "...";
                        }
                        ?>
                    </p>
                </div>
                </br>
                <div class="subjbuttons">
                    <button type="button" class="view" data-id="<?php echo $rows['archive_user_note_id']; ?>" onClick="RestoreUserNote(this)">RESTORE NOTE</button>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script type="text/javascript">
        function RestoreUserNote(e) {
            var archive_user_note_id = $(e).attr("data-id");

            $.ajax({
                type: 'POST',
                url: 'my-list-archive-process.php',
                data: {
                    "archive_user_note_id": archive_user_note_id
                },
                success: function(data) {
                    location.reload();
                }
            });
        }
    </script>

    <script>
        function openEditUserNoteForm(note_id) {
            document.getElementById("editpopup").style.display = "block";
            document.getElementById('blur').style.filter = "blur(5px)";
            document.getElementById('blur').style.display = "block";
            document.getElementById('scrll').style.overflow = "hidden";
            getUserNoteInfo(note_id);
        }
    </script>
</body>

</html>
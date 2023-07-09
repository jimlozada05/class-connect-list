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
    <title>Homepage - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/sections.css">
    <link rel="stylesheet" href="../css/with-class.css">
    <script src="https://kit.fontawesome.com/c11ce9a287.js" crossorigin="anonymous"></script>
</head>

<body id="scrll">
    <?php DisplayNavHeader(); ?>

    <div class="container">
        <div class="website-content">
            <?php
            // GETs the parameter from the URL php?parameter=[value]&anohterParamaterifthereis=[value]
            // echo "<h1>" . SelectClassName($_GET['class_id']) . "</h1>";
            echo "<p>Welcome " . SelectUserName($_SESSION['user_id']) . "!<p>";
            ?>
        </div>
        <div class="classbtns">
            <div class="buttons">
                <button type="button" class="view" onclick="location.href='create-class.php'">CREATE NEW CLASS</button>
                <button type="button" class="view" onclick="openAddClassForm()">JOIN CLASS</button>
            </div>
            <div class="buttons">
                <button type="button" class="view" onclick="location.href='archive-class.php'">ARCHIVED CLASSES</button>
                <button type="button" class="view" onclick="location.href='../class/see-all-notes.php'">SEE ALL ANNOUNCEMENTS</button>
            </div>
        </div>
    </div>

    <!-- SHOW CLASSES OF THE USER -->
    <div class="case">
        <?php
        $class_info = GetClass($_SESSION['user_id']);
        while ($rows = $class_info->fetch_assoc()) {    ?>
            <div class="card">
                <i class="fa-solid fa-box-archive" data-id="<?php echo $rows['class_id']; ?>" onclick="openArchivePopup(this)"></i>
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
                <div class="buttonswclass">
                    <button type="button" class="view" onclick="location.href='../notes/note.php?class_code=<?php echo $rows['class_code']; ?>&tab=due'">VIEW CLASS</button>

                    <?php
                    // Check if the creator id of the class is the user logged in
                    $manage_class_id = checkManageClass($rows['class_id'], $_SESSION['user_id']);
                    if ($manage_class_id == TRUE) {
                        echo "<button type=\"button\" class=\"view\" onclick=\"location.href='manage-class.php?class_code=" . $rows['class_code'] . "'\">MANAGE CLASS</button></br></br>";
                    } ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <div id="blur" onclick="closeAddClassForm()"></div>
    <!-- POPUP (join)-->
    <div class="center" id="center">
        <div class="form-popup" id="add-class-form">
            <form id="formJoinClass" class="form-container">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                <button type="button" id="close" onclick="closeAddClassForm()">X</button>

                <h1>JOIN CLASSROOM</h1>
                <p class="details">Enter the classroom code of the desired class.<br></p>
                <p class="details">Doing so will send a join request towards the class officers.<br></p>
                <strong><em><br>Proceeding means that you are currently studying on multiple college degrees or taking subjects from different classes.<br></em></strong>

                <input type="text" placeholder="Enter Class Code" name="class_code" class="form-control" required>

                <button type="submit" class="btn">JOIN CLASS</button>
            </form>
        </div>
    </div>
    <!-- END POPUP (join)-->

    <!-- POPUP (archive)-->

    <div id="archive-popup">
        <p class="prompt">Are you sure to archive this class?</p>
        <div class="archive-popup-btns">
            <button type="submit" onclick="ArchiveClass(this)" id="popup_yes">YES</button>
            <button onclick="closeAddClassForm()">NO</button>
        </div>
    </div>

    <!-- END POPUP (archive)-->

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Get the form value and send to the with-class-process for joining class -->
    <script type="text/javascript">
        $("#formJoinClass").submit(function(e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: 'with-class-process.php',
                data: form.serialize(),
                success: function(data) {
                    if (data > 0) {
                        alert("Class joined successfully!");
                        // window.location.replace("../notes/note.php?class_id=" + data);
                        window.location.replace("with-class.php");
                    } else if (data == 0) {
                        alert("Class doesn't exist!");
                    } else {
                        alert(data);
                    }
                }
            });
        });
    </script>
    <script type="text/javascript">
        function openAddClassForm() {
            document.getElementById("add-class-form").style.display = "block";
            document.getElementById('blur').style.filter = "blur(5px)";
            document.getElementById('blur').style.display = "block";
            document.getElementById('scrll').style.overflow = "hidden";
        }

        function closeAddClassForm() {
            document.getElementById("add-class-form").style.display = "none";
            document.getElementById("archive-popup").style.display = "none";
            document.getElementById('blur').style.filter = "blur(0)";
            document.getElementById('blur').style.display = "none";
            document.getElementById('scrll').style.overflow = "auto";
            $("#popup_yes").removeAttr("data-id");
        }

        function openArchivePopup(e) {
            var class_id = $(e).attr("data-id");
            document.getElementById("archive-popup").style.display = "block";
            document.getElementById('blur').style.filter = "blur(5px)";
            document.getElementById('blur').style.display = "block";
            document.getElementById('scrll').style.overflow = "hidden";
            $("#popup_yes").attr("data-id", class_id);
        }
    </script>

    <script type="text/javascript">
        function ArchiveClass(e){
            var class_id = $(e).attr("data-id");

            $.ajax({
                type: 'POST',
                url: 'archive-class-process.php',
                data: {
                    "class_id": class_id
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
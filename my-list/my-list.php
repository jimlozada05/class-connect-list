<?php
include "../dbconnect.php";
OpenSession();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My List - Class Connect List</title>
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/with-class.css">
    <link rel="stylesheet" href="../css/manage-class.css">
    <link rel="stylesheet" href="../css/my-list.css">
    <script src="https://kit.fontawesome.com/c11ce9a287.js" crossorigin="anonymous"></script>
</head>

<body id="scrll">
    <?php DisplayNavHeader(); ?>

    <p class="subj">MY LIST</p>

    <div class="buttons">
        <button class="button" onclick="openAddUserNoteForm()">ADD TO-DO</button>
        <button class="button" onclick="location.href='my-list-archive.php'">ARCHIVE</button>
    </div>

    <!-- BLUR -->
    <div id="blur" onclick="closeUserNoteForm()"></div>

    <!-- DISPLAY TO-DOs -->
    <div class="listcase">
        <?php $user_note_info = GetUserNote($_SESSION['user_id']);

        while ($rows = $user_note_info->fetch_assoc()) {    ?>
            <div class="listcard">
                <i class="fa-solid fa-box-archive" data-id="<?php echo $rows['note_id']; ?>" onclick="openArchivePopup(this)"></i>
                <div class="notetitle">
                    <p><?php echo $rows['note_title']; ?></p>
                </div>
                <div class="dates">
                    <p>Post Date: <span><?php echo $rows['post_date']; ?></span></p>

                    <?php if (isset($rows['due_date'])) { ?>
                        <p>Due Date: <span><?php echo $rows['due_date']; ?></span></p>
                    <?php }
                        if (isset($rows['due_time'])){
                            echo "<p>Due Time: <span>".date('h:i A',strtotime($rows['due_time']))."</span></p>";
                        }
                    ?>
                 </div>

                </br>
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
                    <?php if($rows['user_id'] != NULL){ ?>
                    <button type="button" class="view" onclick="openEditUserNoteForm(<?php echo $rows['note_id']; ?>)">VIEW/EDIT</button>
                    <button type="button" class="view" onclick="DeleteUserNote(<?php echo $rows['note_id']; ?>)">DELETE</button>
                    <?php }else{ ?>
                    <button type="button" class="view" onclick="RemoveUserNote(<?php echo $rows['note_id']; ?>)">REMOVE</button>
                    <?php } ?>
                </div>
            </div>
        <?php
        } ?>
    </div>

    <!-- POPUP (ADD USER NOTE FORM) -->
    <div id="addpopup" class="popup">
        <form id="formAddUserNote" class="form-container">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

            <p class="addsubj-title">ADD TO-DO</p>
            <label>Note/Task Title</label>
            <input type="text" class="form-control" name="note_title" placeholder="Title" required>

            <label>Description</label>
            <textarea class="form-control" name="description" style="height: 100px" placeholder="Note Description (optional)"></textarea>

            <div><label>Due date</label></div>
            <div><input type="date" class="form-control" name="due_date"></div>

            <div><label>Due Time</label></div>
            <div><input type="time" class="form-control" name="due_time"></div>

            <div><br><span class="form-text">Due date and/or time are optional!</span></div>

            <br><button type="submit" class="btn">ADD</button>
        </form>
    </div>

    <!-- POPUP (VIEW/EDIT USER NOTE FORM)-->
    <div id="editpopup" class="popup">
        <form id="formEditUserNote" class="form-container">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" name="note_id" id="note-id">
            <p class="addsubj-title">EDIT TO-DO</p>

            <label>Note/Task Title</label>
            <input type="text" class="form-control" name="note_title" id="note-title" required>
            <label>Description</label>
            <textarea class="form-control" name="description" style="height: 100px" id="description"></textarea>

            <div><label>Due date</label></div>
            <div><input type="date" class="form-control" name="due_date" id="due-date"></div>

            <div><label>Due Time</label></div>
            <div><input type="time" class="form-control" name="due_time" id="due-time"></div>
            <div><span class="form-text">Due date and/or time are optional!</span></div>

            <br>
            <button type="submit" class="btn">SAVE</button>
            <button type="button" class="btn" onclick="closeEditUserNoteForm()">CLOSE</button>
        </form>
    </div>

    <div id="blur" onclick="closeUserNoteForm()"></div>

    <!-- POPUP (archive) -->
    <div id="archive-popup">
        <p class="prompt">Are you sure to archive this note?</p>
        <div class="archive-popup-btns">
            <button type="submit" onclick="ArchiveUserNote(this)" id="popup_yes">YES</button>
            <button onclick="closeUserNoteForm()">NO</button>
        </div>
    </div>

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Get the form value and send to the with-class-process for joining class -->
    <script type="text/javascript">
        $("#formAddUserNote").submit(function(e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: 'add-user-note-process.php',
                data: form.serialize(),
                success: function(data) {
                    if (data > 0) {
                        window.location.replace("my-list.php");
                    }
                    location.reload();
                }
            });
        });
        $("#formEditUserNote").submit(function(e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: 'edit-user-note-process.php',
                data: form.serialize(),
                success: function(data) {
                    if (data > 0) {
                        window.location.replace("my-list.php");
                    }
                    location.reload();
                }
            });
        });

        function ArchiveUserNote(e) {
            var note_id = $(e).attr("data-id");

            $.ajax({
                type: 'POST',
                url: 'my-list-archive-process.php',
                data: {
                    "note_id": note_id,
                    "user_id": <?php echo $_SESSION['user_id']; ?>
                },
                success: function(data) {
                    location.reload();
                }
            });
        }

        function getUserNoteInfo(note_id) {
            $.ajax({
                type: 'POST',
                url: 'edit-user-note-process.php',
                dataType: 'json',
                data: {
                    edit_note: note_id
                },
                success: function(data) {
                    note_record = data;
                    document.getElementById('note-id').value = note_id;
                    var title = document.getElementById('note-title');
                    title.value = note_record.note_title;
                    title.placeholder = note_record.note_title;

                    var description = document.getElementById('description');
                    description.value = note_record.description;
                    description.placeholder = note_record.description;

                    var due_date = document.getElementById('due-date');
                    due_date.value = note_record.due_date;

                    var due_time = document.getElementById('due-time');
                    due_time.value = note_record.due_time;
                },
            });
        }
    </script>
    <script type="text/javascript">
        function openAddUserNoteForm() {
            document.getElementById("addpopup").style.display = "block";
            document.getElementById('blur').style.filter = "blur(5px)";
            document.getElementById('blur').style.display = "block";
            document.getElementById('scrll').style.overflow = "hidden";
        }

        function closeUserNoteForm() {
            document.getElementById("addpopup").style.display = "none";
            document.getElementById("archive-popup").style.display = "none";
            document.getElementById("editpopup").style.display = "none";
            document.getElementById('blur').style.filter = "blur(0)";
            document.getElementById('blur').style.display = "none";
            document.getElementById('scrll').style.overflow = "auto";
            $("#popup_yes").removeAttr("data-id");
        }

        function openEditUserNoteForm(note_id) {
            document.getElementById("editpopup").style.display = "block";
            document.getElementById('blur').style.filter = "blur(5px)";
            document.getElementById('blur').style.display = "block";
            document.getElementById('scrll').style.overflow = "hidden";
            getUserNoteInfo(note_id);
        }

        function closeEditUserNoteForm() {
            document.getElementById("editpopup").style.display = "none";
            document.getElementById('blur').style.filter = "blur(0)";
            document.getElementById('blur').style.display = "none";
            document.getElementById('scrll').style.overflow = "auto";
        }

        function openArchivePopup(e) {
            var note_id = $(e).attr("data-id");
            document.getElementById("archive-popup").style.display = "block";
            document.getElementById('blur').style.filter = "blur(5px)";
            document.getElementById('blur').style.display = "block";
            document.getElementById('scrll').style.overflow = "hidden";
            $("#popup_yes").attr("data-id", note_id);
        }
    </script>


<script type="text/javascript">
    // Delete a user note
    function DeleteUserNote(note_id){
        $.ajax({
            type: 'POST',
            url: 'delete-user-note-process.php',
            data: {
                "note_id": note_id,
                "user_id": <?php echo $_SESSION['user_id']; ?>
            },
            success: function(data) {
                alert(data)
                location.reload();
            }
        });
    }
</script>

<script type="text/javascript">
    // remove a user note that was added from addtomylist
    function RemoveUserNote(note_id){
        $.ajax({
            type: 'POST',
            url: 'delete-user-note-process.php',
            data: {
                "note_id": note_id
            },
            success: function(data) {
                alert(data)
                location.reload();
            }
        });
    }
</script>


</body>
</html>
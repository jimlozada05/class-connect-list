<?php
// Include the dbconnect.php file that has the function for the database queries
include '../dbconnect.php';
// Check if session exist
OpenSession();
$class_info = GetClassRecord($_GET['class_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/manage-class.css">

    <title>Manage Class</title>

</head>

<body class="scrll">
    <?php
    // HEADER
    DisplayNavHeader();
    ?>
    <p class="subj">SUBJECTS</p>
    <div class="buttons">
        <button class="button" onclick="location.href='with-class.php'">GO BACK</button>
        <button class="button" onclick="openAddClassForm()">ADD SUBJECT</button>
    </div>

    <!-- BLUR -->
    <div id="blur" onclick="closeClassForm()"></div>

    <br>
    <div class="classcase">
        <?php
        $subj = SelectClassSubjectList($_GET['class_id']);
        while ($subj_row = $subj->fetch_assoc()) {       ?>
            <div class="classcard">
                <div class="subjname">
                    <div>
                        <?php echo $subj_row['subject_name']; ?>
                    </div>
                    <div class="details">
                        <?php echo $subj_row['subject_details']; ?>
                    </div>
                </div>
                <div class="subjprof">
                    <p><?php echo $subj_row['professor']; ?></p>
                </div>
                <div class="scheds">
                    <?php
                    $sched = SelectClassSubjectSched($_GET['class_id'], $subj_row['subject_id']);
                    while ($sched_row = $sched->fetch_assoc()) {       ?>
                        <div class="subjsched1">
                            <!-- echo date("h:i A", strtotime($time)); -->
                            <p class="subjday"><?php echo $sched_row['day']; ?></p>
                            <p><?php echo date("h:i A", strtotime($sched_row['from_time'])) . " - " . date("h:i A", strtotime($sched_row['to_time'])); ?></p>
                        </div>
                    <?php } ?>
                </div>
                <div class="subjbuttons">
                    <button onclick="openEditClassForm(this)" data-subject-id="<?php echo $subj_row['subject_id']; ?>">Edit</button>
                    <button onclick="RemoveSubject(this)" data-subject-id="<?php echo $subj_row['subject_id']; ?>">Delete</button>
                </div>
            </div>

            <?php
            $row_subject_info = SelectSubjectRecord($subj_row['subject_id']); ?>
            <!-- Edit subject POPUP -->
            <div class="popup" id="subject-<?php echo $subj_row['subject_id']; ?>">
                <form onsubmit="EditSubjectForm(this);event.preventDefault();">
                    <p class="addsubj-title">
                        Edit SUBJECT
                    </p>
                    <div id="add-subj-form">
                        <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">

                        <label for="subject"><b>SUBJECT</b></label>
                        <input type="text" placeholder="e.g. English" name="subject_name" value="<?php echo $row_subject_info['subject_name']; ?>" required><br><br>

                        <label for="subject"><b>Subject Details</b></label>
                        <input type="text" placeholder="e.g. EN 1001" name="subject_details" value="<?php echo $row_subject_info['subject_details']; ?>"><br><br>

                        <label for="subject"><b>PROFESSOR</b></label>
                        <input type="text" placeholder="e.g. Juan Dela Cruz" name="professor" value="<?php echo $row_subject_info['professor']; ?>"><br><br>
                    </div>
                    <label class="MT"><b>MEETING TIME</b></label>
                    <button type="button" onclick="addSubjectDate(this)" data-subject-id="<?php echo $subj_row['subject_id']; ?>">Add Meeting Time</button>

                    <?php $result_subject_schedule = SelectSubjectScheduleRecord($subj_row['subject_id']);
                    while ($sub_sched_row = $result_subject_schedule->fetch_assoc()) {
                    ?>
                        <div id="scheduled_time">
                            <input type="hidden" name="subject_schedule_id[]" value="<?php echo $sub_sched_row['subject_schedule_id']; ?>">
                            <input type="hidden" name="subject_id" value="<?php echo $sub_sched_row['subject_id']; ?>">

                            <label for="start_date"><b>Start Date</b></label>
                            <input type="date" id="start_date" name="start_date[]" value="<?php echo $sub_sched_row['start_date']; ?>" required>

                            <label for="from"><b>FROM</b></label>
                            <input type="time" id="time1" name="from_time[]" value="<?php echo $sub_sched_row['from_time']; ?>" required>

                            <label for="to"><b>TO</b></label>
                            <input type="time" id="time2" name="to_time[]" value="<?php echo $sub_sched_row['to_time']; ?>" required>
                            <br>

                            <label for="occurrence"><b>Weekly Occurrence</b></label>
                            <input type="number" id="occurrence" name="occurrence[]" min="1" value="<?php echo $sub_sched_row['occurrence']; ?>" required>

                            <button type="button" id="remove_btn" onclick="removeSubjectSchedule(this)" data-subject-schedule-id="<?php echo $sub_sched_row['subject_schedule_id']; ?>">Remove</button>
                            <br>
                        </div>
                    <?php } // Close for while while ($result_subject_schedule->fetch_assoc()) { 
                    ?>

                    <!-- Prepend new meeting form generated here -->
                    <div id="output"></div>

                    <p>The day of week will come from the start date and the weekly occurence is how many times it will reoccur.</p>

                    <div class="submit">
                        <button type="submit">Save Subject</button>
                        <button type="button" onclick="closeEditClassForm(this)" data-subject-id="<?php echo $row_subject_info['subject_id']; ?>">Close</button>
                    </div>
                </form>
            </div>
            <!-- Edit subject POPUP -->
        <?php } // Close for while ($subj_row = $subj->fetch_assoc()) { 
        ?>
    </div>

    <!-- Add subject POPUP -->
    <div class="popup" id="subject-add">
        <form id="AddSubjectForm">
            <p class="addsubj-title">
                ADD SUBJECT for
                <?php
                $class_name = $class_info['class_name'];
                echo "<span class='class_name'>$class_name</span>";
                ?>
            </p>
            <div id="add-subj-form">
                <input type="hidden" name="class_id" value="<?php echo $_GET['class_id']; ?>">

                <label for="subject"><b>SUBJECT</b></label>
                <input type="text" placeholder="e.g. English" name="subject_name" required><br><br>

                <label for="subject"><b>Subject Details</b></label>
                <input type="text" placeholder="e.g. EN 1001" name="subject_details"><br><br>

                <label for="subject"><b>PROFESSOR</b></label>
                <input type="text" placeholder="e.g. Juan Dela Cruz" name="professor"><br><br>
            </div>
            <label class="MT"><b>MEETING TIME</b></label>
            <button type="button" onclick="addSubjectDate(this)">Add Meeting Time</button>

            <div id="scheduled_time">
                <label for="start_date"><b>Start Date</b></label>
                <input type="date" id="start_date" name="start_date[]" required>

                <label for="from"><b>FROM</b></label>
                <input type="time" id="time1" name="from_time[]" required>

                <label for="to"><b>TO</b></label>
                <input type="time" id="time2" name="to_time[]" required>
                <br>

                <label for="occurrence"><b>Weekly Occurrence</b></label>
                <input type="number" id="occurrence" name="occurrence[]" min="1" required>

                <button onclick="removeSubjectDate(this)">Remove</button>
                <br>
            </div>

            <!-- Prepend new meeting form generated here -->
            <div id="output"></div>

            <p>The day of week will come from the start date and the weekly occurence is how many times it will reoccur.</p>

            <div class="submit">
                <button type="submit">Add Subject</button>
            </div>
        </form>
    </div>
    <!-- Add subject POPUP -->

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Script for cloning or duplicating the input for the subject schedule -->
    <script type="text/javascript">
        function addSubjectDate(e) {
            var subject_id = $(e).attr('data-subject-id');
            // Check if the scheduled time form template exist
            if ($(e).parent().children('#scheduled_time').length > 0) {
                var add_form = $(e).parent().children('#scheduled_time').clone().appendTo($(e).parent().children('#output'));
                add_form.find('#remove_btn').removeAttr('data-subject-schedule-id');
                add_form.find('input[name="subject_schedule_id[]"], #start_date, #time1, #time2, #occurrence').removeAttr('value');
            }
            // If not create it
            else {
                $('<div id="scheduled_time"></div>').insertAfter(e);
                $(e).parent().children('#scheduled_time').append('<input type="hidden" name="subject_schedule_id[]">');
                $(e).parent().children('#scheduled_time').append('<input type="hidden" name="subject_id" value=' + subject_id + '>');
                $(e).parent().children('#scheduled_time').append('<label for="start_date"><b>Start Date</b></label> <input type="date" id="start_date" name="start_date[]" required>');
                $(e).parent().children('#scheduled_time').append('<label for="from"><b>FROM</b></label> <input type="time" id="time1" name="from_time[]" required>');
                $(e).parent().children('#scheduled_time').append('<label for="to"><b>TO</b></label> <input type="time" id="time2" name="to_time[]" required> <br>');
                $(e).parent().children('#scheduled_time').append('<label for="occurrence"><b>Weekly Occurrence</b></label> <input type="number" id="occurrence" name="occurrence[]" min="1" required>');
                $(e).parent().children('#scheduled_time').append('<button type="button" id="remove_btn" onclick="removeSubjectSchedule(this)">Remove</button> <br>');
            }
        }

        function removeSubjectDate(e) {
            $(e).parent().remove();
        }
    </script>

    <!-- Popup JS -->
    <script type="text/javascript">
        function openAddClassForm() {
            $("#subject-add").show();
            document.getElementById("blur").style.display = "block";
            document.getElementById("blur").style.filter = "blur(10px)";
            document.getElementById("scrll").style.overflow = "hidden";
        }

        function closeClassForm() {
            $(".popup").hide();
            document.getElementById('blur').style.filter = "blur(0)";
            document.getElementById('blur').style.display = "none";
            document.getElementById('scrll').style.overflow = "auto";
        }

        function openEditClassForm(e) {
            var subject_id = $(e).attr('data-subject-id');
            $("#subject-" + subject_id).show();
            document.getElementById("blur").style.display = "block";
            document.getElementById("blur").style.filter = "blur(10px)";
            document.getElementById("scrll").style.overflow = "hidden";
        }

        function closeEditClassForm(e) {
            var subject_id = $(e).attr('data-subject-id');
            $("#subject-" + subject_id).hide();
            document.getElementById('blur').style.filter = "blur(0)";
            document.getElementById('blur').style.display = "none";
            document.getElementById('scrll').style.overflow = "auto";
        }
    </script>

    <!-- Add subject -->
    <script type="text/javascript">
        $("#AddSubjectForm").submit(function(e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                type: 'POST',
                url: 'manage-class-process.php',
                data: form.serialize() + "&process_type=insert",
                success: function(data) {
                    location.reload();
                }
            });
        });
    </script>

    <!-- Delete the subject -->
    <script type="text/javascript">
        function RemoveSubject(e) {
            var subject_id = $(e).attr("data-subject-id");

            $.ajax({
                type: 'POST',
                url: 'manage-class-process.php',
                data: {
                    "subject_id": subject_id,
                    "process_type": "delete"
                },
                success: function(data) {
                    alert(data);
                    location.reload();
                }
            });
        }
    </script>

    <!-- Edit subject -->
    <script type="text/javascript">
        function EditSubjectForm(e) {
            var form = $(e);

            $.ajax({
                type: 'POST',
                url: 'manage-class-process.php',
                data: form.serialize() + "&process_type=update",
                success: function(data) {
                    location.reload();
                }
            });
        }
    </script>

    <!-- Remove the schedule from the subject schedule -->
    <script type="text/javascript">
        function removeSubjectSchedule(e) {
            var subject_schedule_id = $(e).attr("data-subject-schedule-id");
            if (typeof $(e).attr("data-subject-schedule-id") !== 'undefined' && $(e).attr("data-subject-schedule-id") !== false) {
                $.ajax({
                    type: 'POST',
                    url: 'manage-class-process.php',
                    data: {
                        "subject_schedule_id": subject_schedule_id,
                        "process_type": "delete-schedule"
                    },
                    success: function(data) {
                        alert(data);
                        location.reload();
                    }
                });
            } else {
                $(e).parent().remove();
            }
        }
    </script>

</body>

</html>
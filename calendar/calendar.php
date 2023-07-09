<?php 
require_once('../dbconnect.php');
OpenSession(); 


if (!isset($_GET['filter']) && empty($_GET['filter'])) {
    $_GET['filter'] = "sdm";
    header("Location: calendar.php?filter=" . $_GET['filter']);
}
else if($_GET['filter'] == "sdm"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "sdm");
}
else if($_GET['filter'] == "sd"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "sd");
}
else if($_GET['filter'] == "sm"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "sm");
}
else if($_GET['filter'] == "dm"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "dm");
}
else if($_GET['filter'] == "s"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "s");
}
else if($_GET['filter'] == "d"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "d");
}
else if($_GET['filter'] == "m"){
    $schedules = SelectCalendarRecord($_SESSION['user_id'], "m");
}


$sched_res = [];
foreach($schedules->fetch_all(MYSQLI_ASSOC) as $row){
    $row['sdate'] = date("F d, Y h:i A",strtotime($row['start_datetime']));
    $row['edate'] = date("F d, Y h:i A",strtotime($row['end_datetime']));
    $sched_res[$row['calendar_id']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Calendar CSS -->
    <link rel="stylesheet" href="../css/calendar.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/fullcalendar.css">
</head>

<body>
    <?php DisplayNavHeader(); ?>
    
    <div class="container" id="page-container">
        <div class="row">

            <div class="col-md-9">
                <div id="calendar"></div>
            </div>

            <div class="col-md-3">
                <!-- CATEGORIES OF CALENDAR -->
                <div class="cardt rounded-0 shadow">
                    <div class="card-header text-light">
                        <h5 class="card-title">Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="form-group mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="subject-filter" checked>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Subject Schedule
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="due-filter" checked>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        Deadlines
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="mylist-filter" checked>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        My List
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="event-details-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded">
                <div class="modal-header rounded 4">
                    <h5 class="modal-title" color="yellow">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <dl>
                            <dt class="text-muted">Event Title</dt>
                            <dd id="title" class="fw-bold fs-4"></dd>
                            <dt class="text-muted">Description</dt>
                            <dd id="description" class=""></dd>
                            <dt class="text-muted">Event Start</dt>
                            <dd id="start" class=""></dd>
                            <dt class="text-muted">Event End</dt>
                            <dd id="end" class=""></dd>
                        </dl>
                    </div>
                </div>
                <div class="modal-footer rounded-4">
                    <div class="text-end">
                        <button type="button" class="btn btn-primary btn-sm rounded-0" id="edit" data-id="">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm rounded-0" id="delete" data-id="">Delete</button>
                        <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Event Details Modal -->

    <!-- Add Event Modal -->
    <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="add-event-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content popup">
            <form id="add-event-form">

                <div class="modal-header rounded 4 part1">
                    <h5 class="modal-title" color="yellow">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body part2">
                    
                    <div class="form-group mb-2">
                        <label for="title" class="form-label">Event Title:</label>
                        <input type="text" name="event_title" id="event_title" class="form-control" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="event_details" class="form-label">Event Details:</label>
                        <textarea rows="3" name="event_details" id="event_details" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-2">
                        <label for="event_from_date" class="form-label">Event Start:</label>
                        <input type="datetime-local" name="event_from_date" id="event_from_date" class="form-control" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="event_to_date" class="form-label">Event Due:</label>
                        <input type="datetime-local" name="event_to_date" id="event_to_date" class="form-control" required>
                    </div>

                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                </div>
                <div class="modal-footer rounded-4 part3">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm rounded-0 submitbtn">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm rounded-0 closebtn" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </form>
            </div>
        </div>
    </div>
    <!-- Event Details Modal -->

    <!-- Edit Event Modal -->
    <div class="modal fade" tabindex="-1" data-bs-backdrop="static" id="edit-event-modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded">
            <form id="edit-event-form">

                <div class="modal-header rounded 4">
                    <h5 class="modal-title" color="yellow">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="event_id">
                    
                    <div class="form-group mb-2">
                        <label for="title" class="form-label">Event Title:</label>
                        <input type="text" name="event_title" id="event_title" class="form-control" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="event_details" class="form-label">Event Details:</label>
                        <textarea rows="3" name="event_details" id="event_details" class="form-control"></textarea>
                    </div>

                    <div class="form-group mb-2">
                        <label for="event_from_date" class="form-label">Event Start:</label>
                        <input type="datetime-local" name="event_from_date" id="event_from_date" class="form-control" required>
                    </div>

                    <div class="form-group mb-2">
                        <label for="event_to_date" class="form-label">Event Due:</label>
                        <input type="datetime-local" name="event_to_date" id="event_to_date" class="form-control" required>
                    </div>

                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">

                </div>
                <div class="modal-footer rounded-4">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary btn-sm rounded-0">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </form>
            </div>
        </div>
    </div>
    <!-- Event Details Modal -->

    <!-- AJAX / jQuery CDN -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Full calendar script -->
    <script src="https://unpkg.com/fullcalendar@5.10.1/main.js"></script>


<script>
    var scheds = $.parseJSON('<?= json_encode($sched_res) ?>')
    var calendar;
    var Calendar = FullCalendar.Calendar;
    var events = [];

    $(function() {
        if (!!scheds) {
            Object.keys(scheds).map(k => {
                var row = scheds[k]
                events.push({
                    id: row.calendar_id,
                    title: row.event_title,
                    start: row.start_datetime,
                    end: row.end_datetime
                });
            })
        }

        var date = new Date()
        var d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear()

        calendar = new Calendar(document.getElementById('calendar'), {
            customButtons: {
                addEvent: {
                    text: 'Add Event',
                    click: function(){
                        $('#add-event-modal').modal('show');
                    }
                }
            },
            headerToolbar: {
                right: 'addEvent today prev,next',
                left: 'dayGridMonth,timeGridWeek,listWeek',
                center: 'title',
            },
            selectable: true,
            themeSystem: 'standard',
            //Random default events
            events: events,
            eventClick: function(info) {
                var _details = $('#event-details-modal')
                var calendar_id = info.event.id
                if (!!scheds[calendar_id]) {
                    _details.find('#title').text(scheds[calendar_id].event_title)
                    _details.find('#description').text(scheds[calendar_id].description)
                    _details.find('#start').text(scheds[calendar_id].sdate)
                    _details.find('#end').text(scheds[calendar_id].edate)
                    _details.find('#edit,#delete').attr('data-id', calendar_id)
                    // Check if the event has a class id, if it does then it can't be edited or deleted
                    if(scheds[calendar_id].class_id == null){
                        _details.find('#edit,#delete').attr('disabled', false);
                    }
                    else{
                        _details.find('#edit,#delete').attr('disabled', true);
                    }
                    _details.modal('show')
                } else {
                    alert("Event is undefined");
                }
            }
        });

        calendar.render();

        // Edit Button
        $('#edit').click(function() {
            var calendar_id = $(this).attr('data-id');
            if (!!scheds[calendar_id]) {
                var _form = $('#edit-event-modal');
                _form.find('[name="event_id"]').val(scheds[calendar_id].event_id);
                _form.find('[name="event_title"]').val(scheds[calendar_id].event_title);
                _form.find('[name="event_details"]').val(scheds[calendar_id].description);
                _form.find('[name="event_from_date"]').val(String(scheds[calendar_id].start_datetime).replace(" ", "T"));
                _form.find('[name="event_to_date"]').val(String(scheds[calendar_id].end_datetime).replace(" ", "T"));
                $('#event-details-modal').modal('hide');
                $('#edit-event-modal').modal('show');
                _form.find('[name="event_title"]').focus();
            } else {
                alert("Event is undefined");
            }
        });

        // Delete Button / Deleting an Event
        $('#delete').click(function() {
            var calendar_id = $(this).attr('data-id');
            if (!!scheds[calendar_id]) {
                var _conf = confirm("Are you sure you want to delete this scheduled event?");
                if (_conf === true) {
                    $.ajax({
                        type: 'POST',
                        url: 'calendar_process.php',
                        data: {
                            "event_id": scheds[calendar_id].event_id
                        },
                        success: function(data){
                            alert(data);
                            location.reload();
                        }
                    });
                }
            } else {
                alert("Event is undefined");
            }
        });
    });
</script>

<!-- Add and Edit calendar event-->
<script type="text/javascript">
$("#add-event-form").submit(function(e){
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: 'POST',
        url: 'calendar_process.php',
        data: form.serialize(),
        success: function(data){
            alert(data);
            location.reload();
        }
    });
});

$("#edit-event-form").submit(function(e){
    e.preventDefault();
    var form = $(this);

    $.ajax({
        type: 'POST',
        url: 'calendar_process.php',
        data: form.serialize(),
        success: function(data){
            alert(data);
            location.reload();
        }
    });
});
</script>

<script type="text/javascript">
    $(document).ready(function(e){
        if("<?php echo $_GET['filter'] ?>" == "sd"){
            $("#mylist-filter").prop('checked', false);
        }
        else if("<?php echo $_GET['filter'] ?>" == "sm"){
            $("#due-filter").prop('checked', false);
        }
        else if("<?php echo $_GET['filter'] ?>" == "dm"){
            $("#subject-filter").prop('checked', false);
        }
        else if("<?php echo $_GET['filter'] ?>" == "s"){
            $("#due-filter").prop('checked', false);
            $("#mylist-filter").prop('checked', false);
        }
        else if("<?php echo $_GET['filter'] ?>" == "d"){
            $("#subject-filter").prop('checked', false);
            $("#mylist-filter").prop('checked', false);
        }
        else if("<?php echo $_GET['filter'] ?>" == "m"){
            $("#subject-filter").prop('checked', false);
            $("#due-filter").prop('checked', false);
        }

    });

    $(".form-check-input").click(function(e){
        if($("#subject-filter").is(":checked") && $("#due-filter").is(":checked") && $("#mylist-filter").is(":checked")){
            window.location.replace("calendar.php?filter=sdm");
        }
        // Only subject and due are checked
        else if($("#subject-filter").is(":checked") && $("#due-filter").is(":checked") && !($("#mylist-filter").is(":checked"))){
            window.location.replace("calendar.php?filter=sd");
        }
        // Only subject and mylist are checked
        else if($("#subject-filter").is(":checked") && !($("#due-filter").is(":checked")) && $("#mylist-filter").is(":checked")){
            window.location.replace("calendar.php?filter=sm");
        }
        // Only due and mylist are checked
        else if(!($("#subject-filter").is(":checked")) && $("#due-filter").is(":checked") && $("#mylist-filter").is(":checked")){
            window.location.replace("calendar.php?filter=dm");
        }
        else if($("#subject-filter").is(":checked") && !($("#due-filter").is(":checked")) && !($("#mylist-filter").is(":checked"))){
            window.location.replace("calendar.php?filter=s");
        }
        else if(!($("#subject-filter").is(":checked")) && $("#due-filter").is(":checked") && !($("#mylist-filter").is(":checked"))){
            window.location.replace("calendar.php?filter=d");
        }
        else if(!($("#subject-filter").is(":checked")) && !($("#due-filter").is(":checked")) && $("#mylist-filter").is(":checked")){
            window.location.replace("calendar.php?filter=m");
        }
        else if(!($("#subject-filter").is(":checked")) && !($("#due-filter").is(":checked")) && !($("#mylist-filter").is(":checked"))){
            window.location.replace("calendar.php?filter=sdm");
        }

    });
</script>

<script type="text/javascript">
    $("#event_from_date").change(function(e){
        if($("#event_from_date").val() == ""){
            $("#event_to_date").removeAttr("min");
            $("#event_to_date").val("");
        }
        else{
            $("#event_to_date").attr("min", $("#event_from_date").val());
            $("#event_to_date").val($("#event_from_date").val());
        }
    });
</script>

</body>
</html>
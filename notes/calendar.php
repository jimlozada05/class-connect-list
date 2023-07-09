<?php 
// Include database connection with SQL queries function
include '../dbconnect.php';
// Check if session exist
OpenSession();

// Get the class information and class id
$class_info = GetClassRecord($_GET['class_code']);

//Get the access attribute from Database, if 0 regular if 1 full
$member_info = MemberInfo($_SESSION['user_id'], $class_info['class_id']);
$result = $member_info->fetch_assoc(); 
$member_id = $result['member_id'];
$access = $result['member_type'];
?>
<div class="container" id="page-container">
    <div class="row justify-content-center">
        <!-- Calendar API -->
        <div class="col-md-9">
            <div id="calendar"></div>
        </div>
        <!-- End calendar API -->
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
                    <?php 
                    if($access == 1){ // If the user doesn't have full access permission they cannot edit or delete ?>
                    <button type="button" class="btn btn-primary btn-sm rounded-0" id="edit" data-id="">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm rounded-0" id="delete" data-id="">Delete</button>
                    <?php } ?>
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
                    <label for="description" class="form-label">Event Description:</label>
                    <textarea rows="3" name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group mb-2">
                    <label for="start_datetime" class="form-label">Event Start:</label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" required>
                </div>

                <div class="form-group mb-2">
                    <label for="end_datetime" class="form-label">Event Due:</label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" required>
                </div>

                <input type="hidden" name="class_id" value="<?php echo $class_info['class_id']; ?>">

                <div class="form-group mb-2">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select aria-label=".form-select-sm" name="subject_id" id="subject_id" class="form-select">
                        <option value="0">General</option>
                        <?php 
                        $subject_specific = GetAMemberSubjectNames($member_id, $class_info['class_id']); 
                        while($row = $subject_specific->fetch_assoc()){
                            echo "<option value=\"".$row['subject_id']."\">".$row['subject_name']."</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>
            <div class="modal-footer rounded-4 part3">
                <div class="text-end">
                    <?php if($access == 1){
                        // Can't save the add event if you do not have full access
                        echo "<button type=\"submit\" class=\"btn btn-primary btn-sm rounded-0 submitbtn\">Save</button>";
                    } ?>
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
                    <label for="description" class="form-label">Event Description:</label>
                    <textarea rows="3" name="description" id="description" class="form-control"></textarea>
                </div>

                <div class="form-group mb-2">
                    <label for="start_datetime" class="form-label">Event Start:</label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" class="form-control" required>
                </div>

                <div class="form-group mb-2">
                    <label for="end_datetime" class="form-label">Event Due:</label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" class="form-control" required>
                </div>

                <input type="hidden" name="class_id" value="<?php echo $class_info['class_id']; ?>">

                <div class="form-group mb-2">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select aria-label=".form-select-sm" name="subject_id" id="subject_id" class="form-select">
                        <option value="0">General</option>
                        <?php 
                        $subject_specific = GetAMemberSubjectNames($member_id, $class_info['class_id']); 
                        while($row = $subject_specific->fetch_assoc()){
                            echo "<option value=\"".$row['subject_id']."\">".$row['subject_name']."</option>";
                        }
                        ?>
                    </select>
                </div>

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

<!-- Script for the calendar API -->
<?php 

// Show only the calendar per class and exlude the subjects the user is unenrolled
$schedules = SelectClassCalendar($class_info['class_id'], $member_id);

$sched_res = [];
foreach($schedules->fetch_all(MYSQLI_ASSOC) as $row){
    $row['sdate'] = date("F d, Y h:i A",strtotime($row['start_datetime']));
    $row['edate'] = date("F d, Y h:i A",strtotime($row['end_datetime']));
    $sched_res[$row['event_id']] = $row;
}
?>
<script type="text/javascript">
	var scheds = $.parseJSON('<?= json_encode($sched_res) ?>');
	var calendar;
    var Calendar = FullCalendar.Calendar;
    var events = [];
    
    $(function() {
        if (!!scheds) {
            Object.keys(scheds).map(k => {
                var row = scheds[k]
                events.push({ id: row.event_id, title: row.event_title, start: row.start_datetime, end: row.end_datetime });
            })
        }
        
        var date = new Date();
        var d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear();

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
                var event_id = info.event.id
                if (!!scheds[event_id]) {
                    _details.find('#title').text(scheds[event_id].event_title)
                    _details.find('#description').text(scheds[event_id].description)
                    _details.find('#start').text(scheds[event_id].sdate)
                    _details.find('#end').text(scheds[event_id].edate)
                    _details.find('#edit,#delete').attr('data-id', event_id)
                    _details.modal('show')
                } else {
                    alert("Event is undefined");
                }
            }
        });

        // Render the calendar to actually show it
        calendar.render();

        // Edit Button
        $('#edit').click(function() {
            var event_id = $(this).attr('data-id');
            if (!!scheds[event_id]) {
                var _form = $('#edit-event-modal');
                _form.find('[name="event_id"]').val(event_id);
                _form.find('[name="event_title"]').val(scheds[event_id].event_title);
                _form.find('[name="description"]').val(scheds[event_id].description);
                _form.find('[name="start_datetime"]').val(String(scheds[event_id].start_datetime).replace(" ", "T"));
                _form.find('[name="end_datetime"]').val(String(scheds[event_id].end_datetime).replace(" ", "T"));
                _form.find('[name="subject_id"]').val(scheds[event_id].subject_id).change();
                $('#event-details-modal').modal('hide');
                $('#edit-event-modal').modal('show');
                _form.find('[name="event_title"]').focus();
            } else {
                alert("Event is undefined");
            }
        });

        // Delete Button / Deleting an Event
        $('#delete').click(function() {
            var event_id = $(this).attr('data-id');
            if (!!scheds[event_id]) {
                var _conf = confirm("Are you sure to delete this scheduled event?");
                if (_conf === true) {
                    $.ajax({
                        type: 'POST',
                        url: 'calendar_process.php',
                        data: {
                            "event_id": event_id
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
    $("#start_datetime").change(function(e){
        if($("#start_datetime").val() == ""){
            $("#end_datetime").removeAttr("min");
            $("#end_datetime").val("");
        }
        else{
            $("#end_datetime").attr("min", $("#start_datetime").val());
            $("#end_datetime").val($("#start_datetime").val());
        }
    });
</script>
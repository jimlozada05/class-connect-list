<?php
// Include the dbconnect.php file that has the function for the database queries
include '../dbconnect.php';
// Check if session exist
OpenSession();

// Check if the page has the tab parameter that sets the information to display
if (!isset($_GET['tab']) && empty($_SESSION['tab'])) {
	// Tab parameter is empty so the tab can't figure out which information to display
	// Redirect page
	$_GET['tab'] = "due";

	// Check if the page has the class_id parameter and tab parameter for the nav bar
	if (!isset($_GET['class_code']) && empty($_SESSION['class_code'])) {
		header("Location: ../class/no-class.php");
	} else {
		header("Location: note.php?class_code=" . $_GET['class_code'] . "&tab=" . $_GET['tab']);
	}
}

// Get the class information and class id
$class_info = GetClassRecord($_GET['class_code']);

//Get the access attribute from Database, if 0 regular if 1 full
$member_info = MemberInfo($_SESSION['user_id'], $class_info['class_id']);
$result = $member_info->fetch_assoc();
$member_id = $result['member_id'];
$access = $result['member_type'];


?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Class Connect: List</title>

	<!-- note_style CSS -->
	<link rel="stylesheet" type="text/css" href="../css/notes_style.css">
	<link href="https://fonts.googleapis.com/css?family=Work+Sans&display=swap" rel="stylesheet" />
	<!-- Bootstrap CDN -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<!-- Header CSS -->
	<link rel="stylesheet" type="text/css" href="../css/navbar.css">
	<!-- Calendar CSS -->
	<link rel="stylesheet" href="https://unpkg.com/fullcalendar@5.10.1/main.min.css">

</head>

<body>

	<?php DisplayNavHeader(); ?>

	<!-- Row for the Navbar -->
	<div class="container">
		<h1><?php echo $class_info['class_name']; ?></h1>
		<div class="row">
			<ul class="nav nav-tabs col-10">
				<li class="nav-item">
					<?php // Count how many notes are due for today
					$due_count = CountDueNoteToday($class_info['class_id'], $member_id); ?>
					<a class="nav-link text-success" id="due-navlink" href="#" onclick="window.location.href='note.php?class_code=<?php echo $_GET['class_code']; ?>&tab=due';">Dues <?php if ($due_count > 0) echo "<span class=\"badge rounded-pill bg-danger\">" . $due_count . "</span>"; ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-success" id="announcement-navlink" href="#" onclick="window.location.href='note.php?class_code=<?php echo $_GET['class_code']; ?>&tab=announcement';">Announcement</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-success" id="archive-navlink" href="#" onclick="window.location.href='note.php?class_code=<?php echo $_GET['class_code']; ?>&tab=archive';">Archive</a>
				</li>
				<li class="nav-item">
					<?php // Count how many notes are late for the due
					$late_due_count = CountLateDueNote($class_info['class_id'], $member_id); ?>
					<a class="nav-link text-success" id="late-navlink" href="#" onclick="window.location.href='note.php?class_code=<?php echo $_GET['class_code']; ?>&tab=late';">Late <?php if ($late_due_count > 0) echo "<span class=\"badge rounded-pill bg-danger\">" . $late_due_count . "</span>"; ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-success" id="calendar-navlink" href="#" onclick="window.location.href='note.php?class_code=<?php echo $_GET['class_code']; ?>&tab=calendar';">Calendar</a>
				</li>
				<?php
				if ($access == 1) {
					// Count how many pending items are open
					$pending_count = CountPendingNote($class_info['class_id']);
					echo '<li class="nav-item">';
					echo  '<a class="nav-link text-success" id="pending-navlink" href="#" onclick="window.location.href=\'note.php?class_code=' . $_GET['class_code'] . '&tab=pending\'" disabled>Pending Notes ' . ($pending_count > 0 ? '
<span class="badge rounded-pill bg-danger">' . $pending_count . '</span>' : '') . '</a>';
					echo '</li>';
				} else if ($access == 0) {
					echo '<li class="nav-item">';
					echo  '<a class="nav-link text-success" id="approval-navlink" href="#" onclick="window.location.href=\'note.php?class_code=' . $_GET['class_code'] . '&tab=approval\'" disabled>Approval Notes</a>';
					echo '</li>';
				}
				?>
				<li class="nav-item">
					<a class="nav-link text-success" id="member-navlink" href="#" onclick="window.location.href='note.php?class_code=<?php echo $_GET['class_code']; ?>&tab=member';">Member Settings</a>
				</li>
			</ul>

			<?php
			if ($access == 1) {
				echo	'<div class="nav col-2 justify-content-end">';
				echo	'<button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#AddNoteModal">Add Note</button>';
				echo	'</div>';
			} else {
				echo	'<div class="nav col-2 justify-content-end">';
				echo	'<button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#SuggestNoteModal">Suggest Note</button>';
				echo	'</div>';
			}
			?>
		</div>
	</div>
	<!-- END Row for the Navbar -->

	<!-- Add Note Modal -->
	<div class="modal fade" id="AddNoteModal" tabindex="-1" aria-labelledby="AddNoteModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<form id="formAddNote">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="AddNoteModalLabel">Add Note</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<!-- Form to add Note -->
						<div class="form-group">
							<label>Note/Task Title</label>
							<input type="text" class="form-control" name="note_title" placeholder="Title" required>
						</div>

						<br>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" style="height: 200px" placeholder="Note Description (optional)"></textarea>
						</div>

						<br>
						<div class="row">
							<div class="form-group">
								<label>Link</label>
								<input type="text" class="form-control" name="link">
							</div>
							<div>
								<div class="col-auto">
									<label class="col-form-label">Note Specific</label>
								</div>
								<div class="col-auto">
									<select name="subject_id" class="form-control">
										<option value="0" selected>General Note</option>
										<?php
										$subject_specific = GetAMemberSubjectNames($member_id, $class_info['class_id']);
										while ($row = $subject_specific->fetch_assoc()) {
											echo "<option value=\"" . $row['subject_id'] . "\">" . $row['subject_name'] . "</option>";
										}
										?>

									</select>
								</div>
							</div>
							
						</div>

						<br>
						<div class="row">
							<div class="col-auto">
								<label class="col-form-label">Due date</label>
							</div>
							<div class="col-auto">
								<input type="date" class="form-control" name="due_date">
							</div>

							<div class="col-auto">
								<label class="col-form-label">Due Time</label>
							</div>
							<div class="col-auto">
								<input type="time" class="form-control" name="due_time">
							</div>

							<div class="col-auto">
								<span class="form-text">Due date and/or time are optional</span>
							</div>
						</div>

						<div class="form-group">
							<input type="hidden" name="class_id" value="<?php echo $class_info['class_id']; ?>">
						</div>
						<!-- END Form to add Note -->
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-success">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="SuggestNoteModal" tabindex="-1" aria-labelledby="SuggestNoteModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content rounded">
				<form id="formSuggestNote">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="SuggestNoteModalLabel">Suggest Note</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<!-- Form to add Note -->
						<div class="form-group">
							<label>Note/Task Title</label>
							<input type="text" class="form-control" name="note_title" placeholder="Title" required>
						</div>
						
						<br>
						<div class="form-group">
							<label>Description</label>
							<textarea class="form-control" name="description" style="height: 200px" placeholder="Note Description (optional)"></textarea>
						</div>

						<br>
						<div class="row">
							<div class="form-group">
								<label>Link</label>
								<input type="text" class="form-control" name="link">
							</div>
							<div>
								<div class="col-auto">
									<label class="col-form-label">Note Specific</label>
								</div>
								<div class="col-auto">
									<select name="subject_id" class="form-control">
										<option value="0" selected>General Note</option>
										<?php
										$subject_specific = GetAMemberSubjectNames($member_id, $class_info['class_id']);
										while ($row = $subject_specific->fetch_assoc()) {
											echo "<option value=\"" . $row['subject_id'] . "\">" . $row['subject_name'] . "</option>";
										}
										?>

									</select>
								</div>
							</div>
							
						</div>

						<br>
						<div class="row">
							<div class="col-auto">
								<label class="col-form-label">Due date</label>
							</div>
							<div class="col-auto">
								<input type="date" class="form-control" name="due_date">
							</div>

							<div class="col-auto">
								<label class="col-form-label">Due Time</label>
							</div>
							<div class="col-auto">
								<input type="time" class="form-control" name="due_time">
							</div>

							<div class="col-auto">
								<span class="form-text">Due date and/or time are optional</span>
							</div>
						</div>

						<div class="form-group">
							<input type="hidden" name="class_id" value="<?php echo $class_info['class_id']; ?>">
							<input type="hidden" name="member_id" value="<?php echo $member_id ?>">
						</div>
						<!-- END Form to add Note -->
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-success">Submit</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- END Add Note Modal -->

	<!-- Section for loading information -->
	<section class="container" id="note-section">

	</section>
	<!-- END Section for loading information -->


	<!-- AJAX / jQuery CDN -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<!-- Bootstrap JS CDN -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
	<!-- Full calendar script -->
	<script src="https://unpkg.com/fullcalendar@5.10.1/main.js"></script>


<!-- Submit the AddNote form -->
<script type="text/javascript">
	$("#formAddNote").submit(function(e) {
		e.preventDefault();
		var form = $(this);

		$.ajax({
			type: 'POST',
			url: 'add_note_process.php',
			data: form.serialize(),
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	});
</script>

<!-- Check the URL parameter for the tab then load according to it -->
<script type="text/javascript">
	// Get the url tab parameter
	var tab_selected = "<?php echo $_GET['tab']; ?>";
	// Check the value
	if (tab_selected == "announcement") {
		// Load the php page to the note-section class
		$("#note-section").load("announcement.php?class_code=<?php echo $_GET['class_code']; ?>");
		// Set the tab navlink as active
		$("#announcement-navlink").addClass("active");
	}
	if (tab_selected == "due") {
		$("#note-section").load("due.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#due-navlink").addClass("active");
	}
	if (tab_selected == "archive") {
		$("#note-section").load("archive.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#archive-navlink").addClass("active");
	}
	if (tab_selected == "late") {
		$("#note-section").load("late.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#late-navlink").addClass("active");
	}
	if (tab_selected == "calendar") {
		$("#note-section").load("calendar.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#calendar-navlink").addClass("active");
	}
	if (tab_selected == "resources") {
		$("#note-section").load("resources.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#resources-navlink").addClass("active");
	}
	if (tab_selected == "pending") {
		$("#note-section").load("pending.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#pending-navlink").addClass("active");
	}
	if (tab_selected == "member") {
		$("#note-section").load("member.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#member-navlink").addClass("active");
	}
	if (tab_selected == "approval") {
		$("#note-section").load("approval.php?class_code=<?php echo $_GET['class_code']; ?>");
		$("#approval-navlink").addClass("active");
	}
</script>

<!-- Make the note show their full details -->
<script type="text/javascript">
	function DisplayNoteDetails(e) {
		// Get the note-box div parent element
		var note_box = e.closest(".note-box");
		// Get the note detail div child element
		var note_detail = note_box.querySelector(".note-detail");
		// Check if the class show already exist
		if (!note_detail.classList.contains('show')) {
			note_detail.classList.add('show');
		}
	}

	function CloseDisplayNote(e) {
		// Get the note detail dive parent element
		var note_detail = e.closest(".note-detail");
		// Remove the show class
		note_detail.classList.remove('show');
	}
</script>

<!-- Submit the edit note -->
<script type="text/javascript">
	function submitEditNote(e) {
		// Get the form data
		var form = new FormData(e);

		$.ajax({
			type: 'POST',
			url: 'edit_note_process.php',
			data: form,
			processData: false,
			contentType: false,
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Archive and Restore tasks -->
<script type="text/javascript">
	function CompleteTask(e) {
		// Get the data-id attribute value from the onclick
		var note_id = $(e).attr("data-id");

		$.ajax({
			type: 'POST',
			url: 'archive_note_process.php',
			data: {
				"note_id": note_id,
				"member_id": <?php echo $member_id; ?>
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}

	function RestoreTask(e) {
		// Get the data-id attribute value from the onclick
		var archive_note_id = $(e).attr("data-id");

		$.ajax({
			type: 'POST',
			url: 'archive_note_process.php',
			data: {
				"archive_note_id": archive_note_id
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Add to notes to My List -->
<script>
	function CheckDuplicateUserNote(e) {
		var jsonString = e.getAttribute('data-id');
		var data = JSON.parse(jsonString);
		var {
			note_id,
			user_id,
		} = data;
		$.ajax({
			type: "POST",
			url: "../my-list/duplicate-user-note-process.php",
			data: {
				"note_id": note_id,
				"user_id": user_id
			},
			success: function(result) {
				if (result > 0) {
					// popup box goes here
					if (confirm("Note already exists. Do you want to add it again?"))
						AddMyListNote(e);
				} else {
					AddMyListNote(e);
				}
			}
		});
	}
	
	function AddMyListNote(e) {
		var jsonString = e.getAttribute('data-id');
		var data = JSON.parse(jsonString);
		var {
			note_id,
			user_id
		} = data;

		$.ajax({
			type: 'POST',
			url: '../my-list/add-user-note-process.php',
			data: {
				"note_id": note_id,
				"user_id": user_id
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Submit the Suggest Note form -->
<script type="text/javascript">
	$("#formSuggestNote").submit(function(e) {
		e.preventDefault();
		var form = $(this);

		$.ajax({
			type: 'POST',
			url: 'suggest_note_process.php',
			data: form.serialize(),
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	});
</script>

<!-- Cancel the approval note submited -->
<script type="text/javascript">
	function CancelSuggestion(e) {
		// Get the data-id attribute value from the onclick
		var pending_note_id = $(e).attr("data-id");

		$.ajax({
			type: 'POST',
			url: 'delete_approval_process.php',
			data: {
				"pending_note_id": pending_note_id
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Submit the correcttion edit note -->
<script type="text/javascript">
	function submitCorrectionNote(e) {
		// Get the form data
		var form = new FormData(e);

		$.ajax({
			type: 'POST',
			url: 'correction_note_process.php',
			data: form,
			processData: false,
			contentType: false,
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Process the pending note if it will be rejected or approved from the button -->
<script type="text/javascript">
	function proccessPendingNote(e) {
		// Get the data-id attribute value from the onclick
		var pending_note_id = $(e).attr("data-id");
		var status = $(e).attr("data-status");
		var note_id = $(e).attr("data-note-id");

		$.ajax({
			type: 'POST',
			url: 'pending_note_process.php',
			data: {
				"pending_note_id": pending_note_id,
				"status": status,
				"note_id": note_id
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Script for unenrolling and enrolling subjects-->
<script type="text/javascript">
	function EnrollSubject(e) {
		var unenroll_id = $(e).attr("data-unenroll-id");

		$.ajax({
			type: 'POST',
			url: 'unenroll_process.php',
			data: {
				"unenroll_id": unenroll_id,
				"process_type": 1
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}

	function UnenrollSubject(e) {
		var subject_id = $(e).attr("data-subject-id");
		var member_id = $(e).attr("data-member-id");

		$.ajax({
			type: 'POST',
			url: 'unenroll_process.php',
			data: {
				"subject_id": subject_id,
				"member_id": member_id,
				"process_type": 0
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<!-- Script for changing member type and removing members-->
<script type="text/javascript">
	function ChangeMemberType(e) {
		var member_id = $(e).attr("data-member-id");
		var member_type = $(e).attr("data-member-type");

		$.ajax({
			type: 'POST',
			url: 'member_process.php',
			data: {
				"member_id": member_id,
				"member_type": member_type
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}

	function RemoveMember(e) {
		var member_id = $(e).attr("data-member-id");

		$.ajax({
			type: 'POST',
			url: 'member_process.php',
			data: {
				"member_id": member_id,
				"member_type": null
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

<script type="text/javascript">
	function DeleteNote(e){
		var note_id = $(e).attr("data-id");

		$.ajax({
			type: 'POST',
			url: 'delete_note_process.php',
			data: {
				"note_id": note_id
			},
			success: function(data) {
				alert(data);
				location.reload();
			}
		});
	}
</script>

</body>
</html>
<?php
include "../dbconnect.php";
// Check if session exist
OpenSession();

// Check if the user is in a class
if (checkClassJoin($_SESSION['user_id']) == FALSE) {
	header("location: ../class/no-class.php");
}

if (!isset($_GET['tab']) && empty($_SESSION['tab'])) {
	// Tab parameter is empty so the tab can't figure out which information to display
	// Redirect page
	$_GET['tab'] = "due";
	header("Location: see-all-notes.php?tab=" . $_GET['tab']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>All Announcements</title>
	<!-- note_style CSS -->
	<link rel="stylesheet" type="text/css" href="../css/notes_style.css">
	<link href="https://fonts.googleapis.com/css?family=Work+Sans&display=swap" rel="stylesheet" />
	<!-- Bootstrap CDN -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<!-- Header CSS -->
	<link rel="stylesheet" href="../css/navbar.css">
</head>

<body>
	<?php DisplayNavHeader(); ?>

	<div class="container">
		<h1>All Class Notes</h1>
		<div class="row">
			<ul class="nav nav-tabs col-10">
				<li class="nav-item">
					<?php // Count how many notes are due for today
					$due_count = CountAllDueNote($_SESSION['user_id']); ?>
					<a class="nav-link text-success" id="due-navlink" href="#" onclick="window.location.href='see-all-notes.php?tab=due';">Dues <?php if ($due_count > 0) echo "<span class=\"badge rounded-pill bg-danger\">" . $due_count . "</span>"; ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-success" id="announcement-navlink" href="#" onclick="window.location.href='see-all-notes.php?tab=announcement';">Announcement</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-success" id="archive-navlink" href="#" onclick="window.location.href='see-all-notes.php?tab=archive';">Archive</a>
				</li>
				<li class="nav-item">
					<?php // Count how many notes are late for the due
					$late_due_count = CountAllLateDueNote($_SESSION['user_id']); ?>
					<a class="nav-link text-success" id="late-navlink" href="#" onclick="window.location.href='see-all-notes.php?tab=late';">Late <?php if ($late_due_count > 0) echo "<span class=\"badge rounded-pill bg-danger\">" . $late_due_count . "</span>"; ?></a>
				</li>
			</ul>
		</div>
	</div>

	<section class="container" id="note-section"></section>

	<!-- AJAX / jQuery CDN -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<!-- Check the URL parameter for the tab then load according to it -->
	<script type="text/javascript">
		// Get the url tab parameter
		var tab_selected = "<?php echo $_GET['tab']; ?>";
		// Check the value
		if (tab_selected == "announcement") {
			// Load the php page to the note-section class
			$("#note-section").load("all-announcement.php");
			// Set the tab navlink as active
			$("#announcement-navlink").addClass("active");
		}
		if (tab_selected == "due") {
			$("#note-section").load("all-due.php");
			$("#due-navlink").addClass("active");
		}
		if (tab_selected == "archive") {
			$("#note-section").load("all-archive.php");
			$("#archive-navlink").addClass("active");
		}
		if (tab_selected == "late") {
			$("#note-section").load("all-late.php");
			$("#late-navlink").addClass("active");
		}
	</script>

	<script type="text/javascript">
		// Make the note show their full details
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

		// Archive and Restore tasks
		function CompleteTask(e) {
			// get the value of the data-id attribute
			var jsonString = e.getAttribute('data-id');
			// parse the JSON string
			var data = JSON.parse(jsonString);
			// destructure the "data" object and store the values in separate variables
			var {
				note_id,
				class_id,
				user_id
			} = data;

			$.ajax({
				type: 'POST',
				url: 'all-archive-process.php',
				data: {
					"note_id": note_id,
					"class_id": class_id,
					"user_id": user_id
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
				url: 'all-archive-process.php',
				data: {
					"archive_note_id": archive_note_id
				},
				success: function(data) {
					alert(data);
					location.reload();
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

</body>

</html>
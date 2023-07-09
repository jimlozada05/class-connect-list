<?php
// Include database connection with SQL queries function
include '../dbconnect.php';
// Check if session exist
OpenSession();

$select_archive_result = SelectAllArchiveNote($_SESSION['user_id']);
if(mysqli_num_rows($select_archive_result) > 0){
	while ($row = $select_archive_result->fetch_assoc()) {
?>

	<!-- Box -->
	<div class="note-box">
		<div class="row" onclick="DisplayNoteDetails(this)">
			<!-- Title -->
			<span class="note-title col-6 align-self-center"><?php echo $row['note_title']; ?></span>
			<!-- Details -->
			<span class="note-description col-4 align-self-center"><?php echo substr($row['description'], 0, 50); ?></span>
			<div class="col-2 align-self-center">
				<!-- Spent date -->
				<span class="note-due"><?php echo $row['due_date']; ?></span>
				<br>
				<!-- Date -->
				<span class="note-day"><?php echo $row['post_date']; ?></span>
			</div>
		</div>

		<div class="note-detail">
			<div class=" detail-container">
				<div class="note-detail-box">
					<span><?php echo $row['description']; ?>
				</div>
			</div>

			<div class="container d-flex justify-content-end">
				<button class="btn btn-outline-success" data-id='<?php echo $row['archive_note_id']; ?>' onclick="RestoreTask(this)">Restore</button>
				&emsp;<button class="btn btn-outline-secondary" onclick="CloseDisplayNote(this)">Close</button>
			</div>
		</div>
	</div>
	<br>

<?php 
	} // While
} // If
else{
	?>
	<h1 style="text-align: center;">NO ARCHIVED NOTE</h1>
<?php
}
?>
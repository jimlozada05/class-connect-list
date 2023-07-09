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

// Select all note table records without due date
$select_announcement_result = SelectAnnouncementRecord($class_info['class_id'], $member_id);
if(mysqli_num_rows($select_announcement_result) > 0) {
	while($row = $select_announcement_result->fetch_assoc()) {
?>

<!-- Box -->
<div class="note-box">
	<div class="row" onclick="DisplayNoteDetails(this)">
		<!-- Title -->
		<span class="note-title col-6 align-self-center"><?php echo $row['note_title'];?></span>
		<!-- Details -->
		<span class="note-description col-4 align-self-center">
			<?php 
				if(strlen($row['description']) > 50){
					echo "\"".substr($row['description'], 0, 50)."\"...";
				}
				else{
					echo $row['description'];
				}
			?>
		</span>
		<div class="col-2 align-self-center">
			<!-- Spent date -->
			<span class="note-due"></span>
			<br>
			<!-- Date -->
			<span class="note-day"><?php echo $row['post_date'];?></span>
		</div>
	</div>

	<div class="note-detail">
		<div class="detail-container">
			<div class="note-detail-box">
				<?php if(!empty($row['link'])) { ?>
				<a href="<?php echo $row['link'];?>" target="_blank" id="custom-link"> 
					<?php 
						if(strlen($row['link'])>50) {
							echo substr($row['link'], 0, 50)."...";
						} else {
							echo $row['link'];
						}
					?>
				</a> 
				<?php if(!empty($row['description'])) { ?>
					<br><br>
				<?php } ?>
				<?php } else { echo ""; } ?>
				<span><?php echo $row['description'];?></span>
			</div>
		</div>

		<div class="container d-flex justify-content-end">
			<?php 
			if($access == 0){
				echo "<button class=\"btn btn-outline-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#SuggestEditModal".$row['note_id']."\">Suggest Correction</button>";
			}
			else if($access == 1){
				echo "<button class=\"btn btn-outline-primary\" data-bs-toggle=\"modal\" data-bs-target=\"#EditModal".$row['note_id']."\">Edit</button>";
				echo "&emsp;<button class=\"btn btn-outline-danger\" data-id='".$row['note_id']."' onclick=\"DeleteNote(this)\">Delete</button>";
			}
			 ?>
			&emsp;<button class="btn btn-outline-success" data-id="<?php echo $row['note_id']; ?>" onclick="CompleteTask(this)">Complete</button>
			&emsp;<button class="btn btn-outline-info" data-id='<?php echo json_encode(
                    array(
                        "note_id" => $row['note_id'],
                        "user_id" => $_SESSION['user_id']
                ))?>' onclick="AddMyListNote(this)">Add to My List</button>
			&emsp;<button class="btn btn-outline-secondary" onclick="CloseDisplayNote(this)">Close</button>
		</div>
		

	</div>
</div>
<br>



<!-- Edit Note Modal -->
<div class="modal fade" id="EditModal<?php echo $row['note_id']; ?>" tabindex="-1" aria-labelledby="EditNoteModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
   		<div class="modal-content">
   		<form id="formEditNote" onsubmit="submitEditNote(this);event.preventDefault()">
      		<div class="modal-header">
        		<h1 class="modal-title fs-5" id="EditNoteModalLabel">Edit Note</h1>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
      		<div class="modal-body">
      			<?php echo NULL; ?>
      		<!-- Form to edit Note -->
        		<div class="form-group">
				    <label>Note/Task Title</label>
				    <input type="text" class="form-control" name="note_title" value="<?php echo $row['note_title'];?>" required>
				</div>
				<br>
				<div class="form-group">
				    <label>Description</label>
				    <textarea class="form-control" name="description" style="height: 200px"><?php echo str_replace("<br />","",$row['description']);?></textarea>
				</div>

				<br>
				<div class="row">
					<div class="form-group">
						<label>Link</label>
						<input type="text" class="form-control" name="link" value="<?php echo $row['link'];?>">
					</div>
					<div>
						<div class="col-auto">
							<label class="col-form-label">Note Specific</label>
						</div>
						<div class="col-auto">
							<select name="subject_id" class="form-control">
								<option value="0">General Note</option>
								<?php 
								$subject_specific = GetAMemberSubjectNames($member_id, $class_info['class_id']); 
								// Get all the subject id and title from database and show it in a dropdown list
								while($subject_row = $subject_specific->fetch_assoc()){
									// Check if the subject id list match with the subject id of the note
									if ($subject_row['subject_id'] == $row['subject_id']) {
										echo "<option value=\"".$subject_row['subject_id']."\" selected>".$subject_row['subject_name']."</option>";
									}
									else{
									echo "<option value=\"".$subject_row['subject_id']."\">".$subject_row['subject_name']."</option>";
									}
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
					<input type="hidden" name="note_id" value="<?php echo $row['note_id'];?>">
					<input type="hidden" name="class_id" value="<?php echo $class_info['class_id'];?>">
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


<div class="modal fade" id="SuggestEditModal<?php echo $row['note_id']; ?>" tabindex="-1" aria-labelledby="SuggestEditModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
   		<div class="modal-content">
   		<form id="formCorrectionNote" onsubmit="submitCorrectionNote(this);event.preventDefault()">
      		<div class="modal-header">
        		<h1 class="modal-title fs-5" id="SuggestEditModalLabel">Suggest Note</h1>
        		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      		</div>
      		<div class="modal-body">
        	<!-- Form to edit Note -->
        		<div class="form-group">
				    <label>Note/Task Title</label>
				    <input type="text" class="form-control" name="note_title" value="<?php echo $row['note_title'];?>" required>
				</div>

				<div class="form-group">
				    <label>Description</label>
				    <textarea class="form-control" name="description" style="height: 200px"><?php echo str_replace("<br />","",$row['description']);?></textarea>
				</div>

				<br>
				<div class="row">
					<div class="form-group">
						<label>Link</label>
						<input type="text" class="form-control" name="link" value="<?php echo $row['link'];?>">
					</div>
					<div>
						<div class="col-auto">
							<label class="col-form-label">Note Specific</label>
						</div>
						<div class="col-auto">
							<select name="subject_id" class="form-control">
								<option value="0">General Note</option>
								<?php 
								$subject_specific = GetAMemberSubjectNames($member_id, $class_info['class_id']); 
								// Get all the subject id and title from database and show it in a dropdown list
								while($subject_row = $subject_specific->fetch_assoc()){
									// Check if the subject id list match with the subject id of the note
									if ($subject_row['subject_id'] == $row['subject_id']) {
										echo "<option value=\"".$subject_row['subject_id']."\" selected>".$subject_row['subject_name']."</option>";
									}
									else{
									echo "<option value=\"".$subject_row['subject_id']."\">".$subject_row['subject_name']."</option>";
									}
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
					<input type="hidden" name="note_id" value="<?php echo $row['note_id'];?>">
					<input type="hidden" name="post_date" value="<?php echo $row['post_date'];?>">
					<input type="hidden" name="class_id" value="<?php echo $row['class_id'];?>">
					<input type="hidden" name="member_id" value="<?php echo $member_id;?>">
				</div>
			<!-- END Form to edit Note -->
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        		<button type="submit" class="btn btn-success">Submit</button>
      		</div>
      	</form>
    	</div>
  	</div>
</div>
<!-- END Edit Note Modal -->

<?php 
	} // While
} // If
else{
	?>
	<h1 style="text-align: center;">NO ANNOUNCEMENT YET</h1>
<?php
}
?>
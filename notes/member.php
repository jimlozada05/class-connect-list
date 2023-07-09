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

<h3>Class Subjects</h3>
<table class="table table-hover">
	<thead>
		<tr>
			<th scope="col">Subject ID</th>
			<th scope="col">Subject Name</th>
			<th scope="col">Professor</th>
			<th scope="col">Details</th>
			<th scope="col">Action</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		$result = SelectClassSubjectList($class_info['class_id']);
		while($row = $result->fetch_assoc()){
	?>
		<tr>
			<th scope="row"><?php echo $row['subject_id'];?></th>
			<td><?php echo $row['subject_name'];?></td>
			<td><?php echo $row['professor'];?></td>
			<td><?php echo $row['subject_details'];?></td>
			<td><?php 
				// Check if the subject id and member id is listed as unenrolled
				$unenroll_row = SelectUnenrollbySubjectID($row['subject_id'], $member_id);
				if($unenroll_row == FALSE){
					// Display unenroll
					echo "<button class=\"btn btn-outline-danger\" data-subject-id=\"".$row['subject_id']."\" data-member-id=\"".$member_id."\" onclick=\"UnenrollSubject(this)\">Unenroll</button>";
				}
				else if($unenroll_row['subject_id'] == $row['subject_id']){
					// Display enroll and delete from unenroll table
					echo "<button class=\"btn btn-outline-success\" data-unenroll-id=\"".$unenroll_row['unenroll_id']."\" onclick=\"EnrollSubject(this)\">Enroll</button>";
				}
			 ?>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<br>
<h3>Class Members</h3>
<table class="table table-hover">
	<thead>
		<tr>
			<th>Member ID</th>
			<th scope="col">Member Name</th>
			<th scope="col">Member Type</th>
			<?php 
			// Check if the user logged in is a full user of the class to give access to change type of other users
			if($access == 1){
				echo "<th scope=\"col\">Action</th>";
			}?>
		</tr>
	</thead>
	<tbody>
	<?php 
		$result = SelectClassMemberList($class_info['class_id']);
		while($row = $result->fetch_assoc()){
	?>
		<tr>
			<th scope="row"><?php echo $row['member_id'];?></th>
			<td><?php echo $row['f_name']." ".$row['m_name']." ".$row['l_name']; ?></td>
			<td><?php 
			// Check if the member type of the member listed is full access or member
			if ($row['member_type'] == 1) {
				echo "Full Access";
			}
			else{
				echo "Member";
			}
			?></td>
			<td><?php
			// Check if the user logged in is a full user of the class to give access to change type of other users
			if($access == 1 && ($_SESSION['user_id'] != $row['user_id'])){
				echo "<button class=\"btn btn-outline-info\" data-member-id=\"".$row['member_id']."\" data-member-type=\"".$row['member_type']."\" onclick=\"ChangeMemberType(this)\">Change Type</button> ";
				echo "<button class=\"btn btn-outline-danger\" data-member-id=\"".$row['member_id']."\" onclick=\"RemoveMember(this)\">Remove</button>";
			}?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
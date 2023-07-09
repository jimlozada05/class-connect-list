<?php
// Insert to note table
function InsertNote($class_id, $subject_id, $due_date, $due_time, $note_title, $description, $link, $conn){
	$offset = SynchTimeZone();
	// Change the timezone first then proceed with the query, this is a multi_query instead of a query
	$sql = "SET time_zone='$offset'";
	$conn->query($sql);
	$sql = "INSERT INTO note (class_id, subject_id, post_date, due_date, due_time, note_title, description, link)
			VALUES ('$class_id', ".
			($subject_id == null ? "NULL" : "'$subject_id'")
			.", CURDATE(), ".
			($due_date == null ? "NULL" : "'$due_date'")
			.", ".
			($due_time == null ? "NULL" : "'$due_time'")
			.", '$note_title', '$description', '$link')";
	if ($conn->query($sql) === TRUE) {
		// Check if there is a due date then add the note to calendar
		if($due_date != null){
			// If due time is null then set it as 08 AM
			if($due_time == null){
				$due_time = "23:29:00";
			}
			$start_datetime = date('Y-m-d H:i:s', strtotime("$due_date $due_time"));
			$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . "+30 minutes"));
			// Insert into calendar table and pass the note_id as the last insert id
			InsertClassCalendarNote($note_title, $description, $start_datetime, $end_datetime, $class_id, $subject_id, $conn->insert_id);
		}
		echo "Note added successfully";
	}
	else{
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn -> close();
}

// Update note table
function UpdateNote($note_id, $subject_id, $due_date, $due_time, $note_title, $description, $link, $class_id, $conn){
	$offset = SynchTimeZone();
	// Change the timezone first then proceed with the query, this is a multi_query instead of a query
	$sql = "SET time_zone='$offset';

			UPDATE note
			SET subject_id = ".($subject_id == null ? "NULL" : "'$subject_id'").", due_date = ".($due_date == null ? "NULL" : "'$due_date'").", due_time = ".($due_time == null ? "NULL" : "'$due_time'").", note_title = '$note_title', description = '$description', link = '$link'
			WHERE note_id = '$note_id'";
	if ($conn->multi_query($sql) === TRUE) {
		// Check if there is a due date then add the note to calendar
		if($due_date != null){
			// If due time is null then set it as 08 AM
			if($due_time == null){
				$due_time = "23:29:00";
			}
			$start_datetime = date('Y-m-d H:i:s', strtotime("$due_date $due_time"));
			$end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . "+30 minutes"));
			// Update note table then update calendar table using the note id
			UpdateClassCalendarNote($note_title, $description, $start_datetime, $end_datetime, $class_id, $subject_id, $note_id);
		}
		if($due_date == null){
			// if due date is null then the calendar event must be deleted
			$conn->next_result();
			// The next result command is needed since it is not possible to run two simultatnues query like DELETE, INSERT, or UPDATE
			$sql = "DELETE FROM calendar
					WHERE event_id = (SELECT event_id
						FROM note_calendar
						WHERE note_id = '$note_id');
					DELETE FROM note_calendar
					WHERE note_id = '$note_id';";
			$conn->multi_query($sql);

		}
		echo "Note updated successfully";
	}
	else{
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn -> close();
}

// Select records from the note table where due date have values
function SelectDueRecord($class_id, $member_id){
	$conn = OpenCon();
	// Set Time Zone query
	$conn->query("SET time_zone='+08:00'");
	$sql = "SELECT *
			FROM note
			WHERE class_id = '$class_id' AND due_date IS NOT NULL AND (subject_id NOT IN (
				SELECT subject_id
			    FROM unenroll
			    WHERE member_id = '$member_id') OR subject_id IS NULL) AND note_id NOT IN (
				SELECT note.note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE class_id = '$class_id' AND member_id = '$member_id') AND DATEDIFF(CURDATE(), due_date) < '1'
			ORDER BY due_date ASC";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Select records from the note table where due date is null
function SelectAnnouncementRecord($class_id, $member_id){
	$conn = OpenCon();
	// Set Time Zone query
	$conn->query("SET time_zone='+08:00'");
	$sql = "SELECT *
			FROM note
			WHERE class_id = '$class_id' AND due_date IS NULL AND (subject_id NOT IN (
				SELECT subject_id
			    FROM unenroll
			    WHERE member_id = '$member_id') OR subject_id IS NULL) AND note_id NOT IN (
				SELECT note.note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE class_id = '$class_id' AND member_id = '$member_id')
			ORDER BY post_date DESC";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Select all the note that are late
function SelectLateDueRecord($class_id, $member_id){
	$conn = OpenCon();
	// Set Time Zone query
	$conn->query("SET time_zone='+08:00'");
	$sql = "SELECT *
			FROM note
			WHERE class_id = '$class_id' AND due_date IS NOT NULL AND (subject_id NOT IN (
				SELECT subject_id
			    FROM unenroll
			    WHERE member_id = '$member_id') OR subject_id IS NULL) AND note_id NOT IN (
				SELECT note.note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE class_id = '$class_id' AND member_id = '$member_id') AND DATEDIFF(CURDATE(), due_date) >= '1'
			ORDER BY due_date ASC";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Check what member type is a user 0 for regular and 1 for officers
function MemberInfo($user_id, $class_id){
	$conn = OpenCon();
	$sql = "SELECT member_id, member_type
			FROM member
			WHERE user_id = $user_id AND class_id = $class_id";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Get all the subject names from a given class
function GetAMemberSubjectNames($member_id, $class_id){
	$conn = OpenCon();
	$sql = "SELECT subject_id, subject_name
			FROM subject
			WHERE class_id = '$class_id' AND subject_id NOT IN (
				SELECT subject_id
				FROM unenroll
				WHERE member_id = '$member_id')";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Get the information about a specific class
function GetClassRecord($class_code){
	$conn = OpenCon();
	$sql = "SELECT * 
			FROM class 
			WHERE class_code = '$class_code'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc(); 
	$conn -> close();
	return $row;
}

// Get the subject name using the subject ID
function SelectSubjectName($subject_id){
	$conn = OpenCon();
	$sql = "SELECT subject_name
			FROM subject
			WHERE subject_id = '$subject_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn -> close();
	return $row['subject_name'];
}

// Select a single note record using note id
function SelectANoteRecord($note_id){
	$conn = OpenCon();
	$sql = "SELECT *
			FROM note
			WHERE note_id = '$note_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn -> close();
	return $row;
}

// Update note_status from the note_table
function ArchiveNote($note_id, $member_id, $conn){
	$sql = "INSERT INTO archive_note (note_id, member_id)
			VALUES ('$note_id', '$member_id')";
	if($conn->query($sql) === TRUE){
		echo "Note archived successfully";
	}
	else{
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn -> close();
}

// Delete from archive_note to restore the note
function RestoreArchiveNote($archive_note_id, $conn){
	$sql = "DELETE FROM archive_note
			WHERE archive_note_id = '$archive_note_id'";
	if ($conn->query($sql) === TRUE) {
	  echo "Note restored successfully";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn -> close();
}

// Select from note where note_status = 1
function SelectArchiveNote($class_id, $member_id){
	$conn = OpenCon();
	$sql = "SELECT archive_note.archive_note_id, note.*
			FROM note
			JOIN archive_note 
			ON note.note_id = archive_note.note_id
			WHERE class_id = '$class_id' AND member_id = '$member_id' AND note.note_id IN (
				SELECT note_id 
				FROM archive_note 
				WHERE member_id = '$member_id'
			    )
			ORDER BY post_date DESC";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Add values to Pending_Note which is from the suggest note
function AddNotePending($subject_id, $due_date, $due_time, $note_title, $description, $link, $member_id, $class_id, $conn){
	$offset = SynchTimeZone();
	$sql = "SET time_zone='$offset';

			INSERT INTO pending_note (subject_id, due_date, due_time, note_title, description, link, pending_date, status, member_id, class_id)
			VALUES(".($subject_id == null ? "NULL" : "'$subject_id'").", ".($due_date == null ? "NULL" : "'$due_date'").", ".($due_time == null ? "NULL" : "'$due_time'").", '$note_title', '$description', '$link', CURDATE(), '0', '$member_id', '$class_id')";
	
	if ($conn->multi_query($sql) === TRUE) {
	echo "Approval note added successfully";
	}
	else{
	echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Delete a pending note
function DeletePendingNote($pending_note_id){
	$conn = OpenCon();
	$sql = "DELETE FROM pending_note
			WHERE pending_note_id = '$pending_note_id'";
	if ($conn->query($sql) === TRUE) {
	  echo "Pending note cancelled";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn -> close();
}

// Add values to Pending_Note which is from the correction note
function AddCorrectionPending($note_id, $subject_id, $post_date, $due_date, $due_time, $note_title, $description, $link, $member_id, $class_id, $conn){
	$offset = SynchTimeZone();
	$sql = "SET time_zone='$offset';

			INSERT INTO pending_note (note_id, subject_id, post_date, due_date, due_time, note_title, description, link, pending_date, status, member_id, class_id)
			VALUES('$note_id', ".($subject_id == null ? "NULL" : "'$subject_id'").", '$post_date', ".($due_date == null ? "NULL" : "'$due_date'").", ".($due_time == null ? "NULL" : "'$due_time'").", '$note_title', '$description', '$link', CURDATE(), '0', '$member_id', '$class_id')";
	
	if ($conn->multi_query($sql) === TRUE) {
	echo "Correction note added successfully";
	}
	else{
	echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Select all from the pending_note table filtered by member_id or who is logged in
function SelectApprovalNote($class_id, $member_id){
	$conn = OpenCon();
	$sql = "SELECT *
			FROM pending_note
			WHERE class_id = '$class_id' AND member_id = '$member_id'
			ORDER BY pending_note_id DESC";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

// Select pending note of the class
function SelectPendingNote($class_id){
	$conn = OpenCon();
	$sql = "SELECT *
			FROM pending_note
			WHERE class_id = '$class_id' AND status = '0'
			ORDER BY pending_note_id DESC";
	$result = $conn->query($sql);
	$conn -> close();
	return $result;
}

//Process the pendinte_note if accepted change status to 2 if rejected change status to 1
// If accepted copy the old data from note to note_history first before changing status
function ProcessPendingNote($pending_note_id, $note_id, $status){
	$conn = OpenCon();
	// If Rejected
	if($status == '1'){
		$sql = "UPDATE pending_note
				SET status = '1'
				WHERE pending_note_id = '$pending_note_id'";
		if ($conn->query($sql) === TRUE) {
			echo "Suggested Note Rejected";
		} else {
		  echo "Error: " . $sql . "<br>" . $conn->error;
		}
	}
	// If Accepted
	else if($status == '2'){
		$offset = SynchTimeZone();
		// Check if note_id is not Null, if it is the note has been suggested
		if($note_id != NULL){
			$sql = "SET time_zone='$offset';

				INSERT INTO note_history (note_id, pending_note_id, prev_subject_id, prev_due_date, prev_due_time, prev_note_title, prev_description, prev_link, change_date)
				SELECT note_id, '$pending_note_id', subject_id, due_date, due_time, note_title, description, link, CURDATE()
				FROM note
				WHERE note_id = (SELECT note_id FROM pending_note WHERE pending_note_id = '$pending_note_id');

				UPDATE note, pending_note
				SET note.subject_id = pending_note.subject_id, note.due_date = pending_note.due_date, note.due_time = pending_note.due_time, note.note_title = pending_note.note_title, note.description = pending_note.description, note.link = pending_note.link, pending_note.status = 2 
				WHERE note.note_id = pending_note.note_id AND pending_note.pending_note_id = '$pending_note_id';

				UPDATE calendar
				SET event_title = (SELECT note_title FROM pending_note WHERE pending_note.pending_note_id = '$note_id'),
					description = (SELECT description FROM pending_note WHERE pending_note.pending_note_id = '$note_id'),
					start_datetime = 
						CASE 
							WHEN (SELECT due_date FROM pending_note WHERE pending_note.pending_note_id = '$note_id') IS NOT NULL THEN 
								(SELECT DATE_ADD(CONCAT((SELECT due_date FROM pending_note WHERE pending_note.pending_note_id = '$note_id'), ' 08:00:00'), INTERVAL 30 MINUTE))
							ELSE NULL
						END,
					end_datetime = 
						CASE 
							WHEN (SELECT due_date FROM pending_note WHERE pending_note.pending_note_id = '$note_id') IS NOT NULL THEN 
								(SELECT DATE_ADD(CONCAT((SELECT due_date FROM pending_note WHERE pending_note.pending_note_id = '$note_id'), ' 08:00:00'), INTERVAL 60 MINUTE))
							ELSE NULL
						END,
					class_id = (SELECT class_id FROM pending_note WHERE pending_note.pending_note_id = '$note_id'),
					subject_id = (SELECT subject_id FROM pending_note WHERE pending_note.pending_note_id = '$note_id')
				WHERE event_id = (SELECT event_id FROM note_calendar WHERE note_id = (SELECT note_id FROM pending_note WHERE pending_note.pending_note_id = '$note_id'));";

				$due_date_query = "SELECT due_date FROM pending_note WHERE pending_note_id = '$pending_note_id'";
				$result = $conn->query($due_date_query);
				
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					$due_date = $row["due_date"];
					
					if ($due_date == NULL) {
						$due_date_sql = "DELETE c.*, nc.*
								FROM calendar c
								JOIN note_calendar nc ON c.event_id = nc.event_id
								WHERE nc.note_id = (SELECT note_id FROM pending_note WHERE pending_note_id = '$pending_note_id');";
					}
				}
		}
		// Check if note_id is Null, if it is the note has been corrected
		else if($note_id == NULL){
			$sql = "SET time_zone='$offset';

				INSERT INTO note (class_id, subject_id, post_date, due_date, due_time, note_title, description, link)
				SELECT class_id, subject_id, CURDATE(), due_date, due_time, note_title, description, link
				FROM pending_note
				WHERE pending_note_id = '$pending_note_id';

				UPDATE pending_note
				SET note_id = LAST_INSERT_ID(), status = '2'
				WHERE pending_note_id = '$pending_note_id';";

		}

		// Suggest note with changed due date
        if (isset($due_date_sql) && $conn->multi_query($sql . $due_date_sql) === TRUE) {
            echo "Suggested note accepted!";
        } 
        // Suggest note with removed due date
        elseif ($conn->multi_query($sql) === TRUE) {
            echo "Suggested note accepted!";
        } else {
            echo "Error: " . $due_date_sql . "<br>" . $conn->error;
        }
	}
	$conn->close();
	return;
}

// Check if the note_id exist in Histroy_note table
function SelectNoteIdInHistory($note_id){
	$conn = OpenCon();
	$sql = "SELECT *
			FROM note_history
			WHERE note_id = '$note_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn -> close();
	return $row;
}

// Check if a subject is unenrolled using subject ID and member ID
function SelectUnenrollbySubjectID($subject_id, $member_id){
	$conn = OpenCon();
	$sql = "SELECT *
			FROM unenroll
			WHERE subject_id = '$subject_id' AND member_id = '$member_id'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$conn -> close();
		return $row;
	} else {
		$conn->close();
		return FALSE;
	}	
}

// Insert into unenroll table
function InsertUnenroll($member_id, $subject_id){
	$conn = OpenCon();
	$sql = "INSERT INTO unenroll (member_id, subject_id)
			VALUES ('$member_id', '$subject_id')";
	if ($conn->query($sql) === TRUE) {
	  echo "Subject unenrolled!";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Delete from unenroll table to enroll
function DeleteUnenroll($unenroll_id){
	$conn = OpenCon();
	$sql = "DELETE FROM unenroll
			WHERE unenroll_id = '$unenroll_id'";
	if ($conn->query($sql) === TRUE) {
	  echo "Subject enrolled!";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Change the member type
function UpdateMember($member_id, $member_type){
	$conn = OpenCon();
	$sql = "UPDATE member
			SET member_type = '".($member_type == "0" ? "1" : "0")."'
			WHERE member_id = '$member_id'";
	if ($conn->query($sql) === TRUE) {
	  echo "Member type changed!";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Deletes a member from the member table
function DeleteMember($member_id){
	$conn = OpenCon();
	$sql = "DELETE FROM member
			WHERE member_id = '$member_id'";
	if ($conn->query($sql) === TRUE) {
	  echo "Member removed!";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Count how many row are status pending by class
function CountPendingNote($class_id){
	$conn = OpenCon();
	$sql = "SELECT COUNT(pending_note_id) AS 'pending_count'
			FROM pending_note
			WHERE class_id = '$class_id' AND status = '0'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn -> close();
	return $row['pending_count'];
}

// Count all the note due today by class
function CountDueNoteToday($class_id, $member_id){
	$conn = OpenCon();
	$sql = "SET time_zone='+08:00';";
	if($conn->query($sql) === TRUE){
		$sql = "SELECT COUNT(note_id) AS 'due_count'
				FROM note
				WHERE class_id = '$class_id' AND due_date IS NOT NULL AND (subject_id NOT IN (
				SELECT subject_id
			    FROM unenroll
			    WHERE member_id = '$member_id') OR subject_id IS NULL) AND note_id NOT IN (
				SELECT note.note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE class_id = '$class_id' AND member_id = '$member_id') AND due_date = CURDATE()";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$conn -> close();
		return $row['due_count'];
	}
	$conn -> close();
}

// Count all the late note due today by class
function CountLateDueNote($class_id, $member_id){
	$conn = OpenCon();
	$sql = "SET time_zone='+08:00';";
	if($conn->query($sql) === TRUE){
		$sql = "SELECT COUNT(note_id) AS 'late_due_count'
				FROM note
				WHERE class_id = '$class_id' AND due_date IS NOT NULL AND (subject_id NOT IN (
				SELECT subject_id
			    FROM unenroll
			    WHERE member_id = '$member_id') OR subject_id IS NULL) AND note_id NOT IN (
				SELECT note.note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE class_id = '$class_id' AND member_id = '$member_id') AND DATEDIFF(CURDATE(), due_date) >= '1'";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$conn -> close();
		return $row['late_due_count'];
	}
	$conn -> close();
}

// Insert into note table from the calendar InsertClassCalendarEvent() function
function InsertNoteClassCalendar($event_title, $description, $start_datetime, $end_datetime, $class_id, $subject_id, $event_id){
	$conn = OpenCon();
	// Convert the date to the proper format
	$date = date('Y-m-d H:i:s', strtotime($start_datetime));
	$sql = "INSERT INTO note (class_id, subject_id, post_date, due_date, due_time, note_title, description)
			VALUES ('$class_id', ".
			($subject_id == null ? "NULL" : "'$subject_id'")
			.", CURDATE(), DATE('$date'), TIME('$date'), '$event_title', '$description')";
	if($conn->query($sql) === TRUE){
		// Insert into the note_calendar table so the calendar and note can be referenced
		$sql = "INSERT INTO note_calendar (note_id, event_id)
				VALUES ('".$conn->insert_id."', '$event_id')";
		if ($conn->query($sql) === TRUE) {
			echo "Note created! \n";
		} else {
	 	 	echo "Error: " . $sql . "<br>" . $conn->error;
	 	}

	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Update note table after updating the calendar table. From UpdateClassCalendarEvent() function
function UpdateNoteClassCalendar($event_title, $description, $start_datetime, $end_datetime, $class_id, $subject_id, $event_id){
	$conn = OpenCon();
	// Convert the date to the proper format
	$date = date('Y-m-d H:i:s', strtotime($start_datetime));
	$sql = "UPDATE note
			SET subject_id = ".($subject_id == null ? "NULL" : "'$subject_id'").", due_date = DATE('$date'), due_time = TIME('$date'), note_title = '$event_title', description = '$description'
			WHERE note_id = (SELECT note_id
				FROM note_calendar
				WHERE event_id = '$event_id')";
	if($conn->query($sql) === TRUE){
		echo "Note updated! \n";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Delete note table after deleting from the calendar table
function DeleteNoteClassCalendar($event_id){
	$conn = OpenCon();
	// Cleart the query
	$conn->next_result();
	$sql = "DELETE FROM note
			WHERE note_id = (SELECT note_id
				FROM note_calendar
				WHERE event_id = '$event_id');
			DELETE FROM note_calendar
			WHERE event_id = '$event_id'";
	if($conn->multi_query($sql) === TRUE){
		echo "Note removed! \n";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

function DeleteNote($note_id){
	$conn = OpenCon();
	$sql = "DELETE FROM note
			WHERE note_id = '$note_id'";
	if($conn->multi_query($sql) === TRUE){
		DeleteClassCalendarNote($note_id);
		echo "Note removed! \n";
	} else {
	  echo "Error: " . $sql . "<br>" . $conn->error;
	}
}

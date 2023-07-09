<?php

// Display the full name of the user for the class president text field
function SelectUserName($user_id)
{
	$conn = OpenCon();
	$sql = "SELECT f_name, l_name
			FROM user
			WHERE user_id = '$user_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn->close();
	return $row['f_name'] . " " . $row['l_name'];
}

// Create a class
function InputClass($class_name, $class_code, $user_id, $school_year)
{
	$conn = OpenCon();
	// Insert into class table
	$sql = "INSERT INTO class (class_name, class_code, creator_id, school_year)
			VALUES ('$class_name', '$class_code', '$user_id', '$school_year')";

	if ($conn->query($sql) === TRUE) {
		// Select the last insert id or the class id created
		$sql = "SELECT LAST_INSERT_ID();";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		$class_id = $row['LAST_INSERT_ID()'];

		// Insert into member table
		$sql = "INSERT INTO member (member_type, class_id, user_id)
			VALUES ('1', '$class_id', '$user_id')";
		if ($conn->query($sql) === TRUE) {
			// If the insert member worked return the class id
			$conn->close();
			return $class_id;
		}
		// Error out if the member table insert did not work
		else {
			echo "Error: " . $sql . "<br>" . $conn->error;
			$conn->close();
			return 0;
		}
	}
	// Error out if the class table insert did not work
	else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
		return 0;
	}
}

// Select the class name using a class id
function SelectClassName($class_id)
{
	$conn = OpenCon();
	$sql = "SELECT class_name
			FROM class
			WHERE class_id = '$class_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn->close();
	return $row['class_name'];
}

function GetClass($user_id)
{
	$conn = OpenCon();
	// INNER JOIN = join the same attributes from different tables 
	// class table = class_id, class_name, class_code, school_year
	// member table = member_type
	$sql = "SELECT class.class_id, class.class_name, class.class_code, class.school_year, member.member_type
			FROM class
			JOIN member
			ON class.class_id = member.class_id
			WHERE member.user_id = '$user_id' AND class.class_id NOT IN (
				SELECT class_id
				FROM archive_class
				WHERE user_id = '$user_id')";
	// why query() = simple queries (e.g., select, update, delete, insert)
	// multiquery() = ex. select sa loob ng WHERE/SELECT; so far si jim lang gumagamit 
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

function GetClassId($class_code)
{
	// opens connection to database
	$conn = OpenCon();
	$sql = "SELECT class_id
			FROM class
			WHERE class_code = '$class_code'";
	// conn can query, then stores the result to $result
	$result = $conn->query($sql);
	// convert to array/dictionary for PHP to make use of the info (since only sql can understand $result)
	$class_id = $result->fetch_assoc();
	$conn->close();
	// return value
	return $class_id['class_id'];
}

function GetSubject($class_id)
{
	$conn = OpenCon();
	$sql = "SELECT * 
			FROM subject
			WHERE class_id = $class_id";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn->close();
	return $row;
}

// Insert subject then return the subject id
function InsertSubject($subject_name, $subject_details, $professor, $class_id, $conn)
{
	$sql = "INSERT INTO subject (subject_name, subject_details, professor, class_id)
			VALUES ('$subject_name', " .
		# TERNARY: NOT REQUIRED
		# IF variable is null, put "NULL"; else put the inputted variable
		($subject_details == null ? "NULL" : "'$subject_details'")
		. ", " .
		($professor == null ? "NULL" : "'$professor'")
		. ", '$class_id')";
	if ($conn->query($sql) === TRUE) {
		// The newly added id
		return $conn->insert_id;
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Insert the schedule of the subject to the subjectschedule table
function InsertSubjectSchedule($subject_id, $from_time, $to_time, $start_date, $occurrence, $class_id)
{
	$conn = OpenCon();
	$sql = "INSERT INTO subject_schedule (subject_id, from_time, to_time, start_date, occurrence, class_id)
			VALUES ('$subject_id', '$from_time', '$to_time', '$start_date', '$occurrence', '$class_id')";
	if ($conn->query($sql) === TRUE) {
		return $conn->insert_id;
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Insert the dates of the subject schedule
function InsertSubjectCalendar($event_title, $event_details, $event_from_date, $event_to_date, $subject_id, $class_id, $subject_schedule_id){
	$conn = OpenCon();
	$sql = "INSERT INTO subject_calendar (event_title, event_details, event_from_date, event_to_date, subject_id, class_id, subject_schedule_id)
			VALUES('$event_title', ".($event_details == null ? "NULL" : "'$event_details'").", '$event_from_date', '$event_to_date', '$subject_id', '$class_id', '$subject_schedule_id')";
	if ($conn->query($sql) === TRUE) {
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

function DeleteSubjectCalendar($subject_schedule_id){
	$conn = OpenCon();
	$sql = "DELETE FROM subject_calendar
			WHERE subject_schedule_id = '$subject_schedule_id'";
	if ($conn->query($sql) === TRUE) {
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

function DeleteSubjectCalendarSubjectID($subject_id){
	$conn = OpenCon();
	$sql = "DELETE FROM subject_calendar
			WHERE subject_id = '$subject_id'";
	if ($conn->query($sql) === TRUE) {
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Check if the class code generated is unique
function checkClassCode($class_code)
{
	$conn = OpenCon();
	$sql = "SELECT class_id
			FROM class
			WHERE class_code = '$class_code'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$conn->close();
		return 1;
	} else {
		$conn->close();
		return 0;
	}
}

// Check if the user has a full access on the class
function checkManageClass($class_id, $user_id)
{
	$conn = OpenCon();
	$sql = "SELECT class_id
			FROM member
			WHERE class_id = '$class_id' AND user_id = '$user_id' AND member_type = '1'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$conn->close();
		return TRUE;
	} else {
		$conn->close();
		return FALSE;
	}
}

// Check if the user is in a class or not for the no-class and with-class page
function checkClassJoin($user_id)
{
	$conn = OpenCon();
	$sql = "SELECT member_id
			FROM member
			WHERE user_id = '$user_id'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		$conn->close();
		return TRUE;
	} else {
		$conn->close();
		return FALSE;
	}
}

// User joining using a class code
function InsertMemberJoin($class_code, $user_id)
{
	$conn = OpenCon();
	// Check if the class code exist
	$sql = "SELECT class_id
			FROM class
			WHERE class_code = '$class_code'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	if ($result->num_rows > 0) {
		// Check first if the user is already a member of this class
		$sql = "SELECT member_id
				FROM member
				WHERE user_id = '$user_id' AND class_id = '".$row['class_id']."'";
		$result = $conn->query($sql);
		if($result->num_rows > 0){
			echo "You are already a member of this class";
		}
		else {
			// If the class exist insert the member
			$sql = "INSERT INTO member (member_type, class_id, user_id) 
					VALUES ('0', '" . $row['class_id'] . "', '$user_id')";
			if ($conn->query($sql) === TRUE) {
				echo $row['class_id'];
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}
		}
	}
	else {
		echo 0;
	}
	$conn->close();
}

// Select all the subject for a specific class
function SelectClassSubjectList($class_id)
{
	$conn = OpenCon();
	$sql = "SELECT *
			FROM subject
			WHERE class_id = '$class_id'
			ORDER BY subject_name";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

function SelectClassSubjectSched($class_id, $subject_id)
{
	$conn = OpenCon();
	$sql = "SELECT DISTINCT 
			from_time, 
			to_time, 
			(CASE WHEN DAYOFWEEK(start_date) = '2' THEN 'MON'
             WHEN DAYOFWEEK(start_date) = '3' THEN 'TUE'
			 WHEN DAYOFWEEK(start_date) = '4' THEN 'WED'
			 WHEN DAYOFWEEK(start_date) = '5' THEN 'THU'
			 WHEN DAYOFWEEK(start_date) = '6' THEN 'FRI'
			 WHEN DAYOFWEEK(start_date) = '7' THEN 'SAT'
			 WHEN DAYOFWEEK(start_date) = '1' THEN 'SUN'
             ELSE DAYOFWEEK(start_date) END) AS day
			FROM subject
			JOIN subject_schedule AS sched
			WHERE sched.subject_id = '$subject_id'
				AND sched.class_id = '$class_id'";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

// Select all the member of a specific class
function SelectClassMemberList($class_id)
{
	$conn = OpenCon();
	$sql = "SELECT member.*, user.f_name, user.m_name, user.l_name
			FROM member
			JOIN user
			ON member.user_id = user.user_id
			WHERE class_id = '$class_id'";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

function GetArchivedClass($user_id)
{
	$conn = OpenCon();

	$sql = "SELECT class.class_id, class.class_name, class.class_code, class.school_year, archive_class.archive_class_id
			FROM class
			JOIN archive_class
			ON class.class_id = archive_class.class_id
			WHERE archive_class.user_id = '$user_id'";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

// Check if the class name and school year already exist
function CheckClassExist($class_name, $school_year)
{
	$conn = OpenCon();
	$sql = "SELECT *
			FROM class
			WHERE class_name = '$class_name' AND school_year = '$school_year'";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		// The class already exist
		$conn->close();
		return TRUE;
	} else {
		// Class have not been created yet
		$conn->close();
		return FALSE;
	}
}

// Delete subject and the subject schedule with it
function DeleteSubject($subject_id)
{
	$conn = OpenCon();
	$sql = "DELETE FROM subject
			WHERE subject_id = '$subject_id'";
	if ($conn->query($sql) === TRUE) {
		$sql = "DELETE FROM subject_schedule
				WHERE subject_id = '$subject_id'";
		if ($conn->query($sql) === TRUE) {
			echo "Subject removed!";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Select the information of a subject for edit purpose
function SelectSubjectRecord($subject_id)
{
	$conn = OpenCon();
	$sql = "SELECT *
			FROM subject
			WHERE subject_id = '$subject_id'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn->close();
	return $row;
}
// Select the information of a subject schedule for edit purpose
function SelectSubjectScheduleRecord($subject_id)
{
	$conn = OpenCon();
	$sql = "SELECT *
			FROM subject_schedule
			WHERE subject_id = '$subject_id'";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

// Delete from the subhect schedule table
function DeleteSubjectSchedule($subject_schedule_id)
{
	$conn = OpenCon();
	$sql = "DELETE FROM subject_schedule
			WHERE subject_schedule_id = '$subject_schedule_id'";
	if ($conn->query($sql) === TRUE) {
		$conn->close();
		// return true to indicate that the delete was successful
		return TRUE;
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
	}
}

// Update the Subject
function UpdateSubject($subject_id, $subject_name, $subject_details, $professor, $class_id, $conn)
{
	$sql = "UPDATE subject
			SET subject_name = '$subject_name', subject_details = " . ($subject_details == null ? "NULL" : "'$subject_details'") . ", professor = " . ($professor == null ? "NULL" : "'$professor'") . ", class_id = '$class_id' 
			WHERE subject_id = '$subject_id'";
	if ($conn->query($sql) === TRUE) {
		$conn->close();
		// return true to indicate that the update was successful
		return TRUE;
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
	}
}

// Update the subject schedule
function UpdateSubjectSchedule($subject_schedule_id, $subject_id, $from_time, $to_time, $start_date, $occurrence, $class_id)
{
	$conn = OpenCon();
	$sql = "UPDATE subject_schedule
			SET subject_id = '$subject_id', from_time = '$from_time', to_time = '$to_time', start_date = '$start_date', occurrence = '$occurrence', class_id = '$class_id'
			WHERE subject_schedule_id = '$subject_schedule_id'";
	if ($conn->query($sql) === TRUE) {
		$conn->close();
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
		$conn->close();
	}
}

// Archive a class
function InputArchiveClass($class_id, $user_id)
{
	$conn = OpenCon();
	$sql = "INSERT INTO archive_class (class_id, user_id)
			VALUES ('$class_id', '$user_id')";
	if ($conn->query($sql) === TRUE) {
		echo "Class archived";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Restore an archived class
function DeleteArchiveClass($archive_class_id)
{
	$conn = OpenCon();
	$sql = "DELETE FROM archive_class
			WHERE archive_class_id = '$archive_class_id'";
	if ($conn->query($sql) === TRUE) {
		echo "Class restored";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Select records from the note table where due date is null
// and are published from all enrolled classes of the user
function SelectAllAnnouncementRecord($user_id, $selected_process)
{
	$conn = OpenCon();
	$sql = "WITH matched_member_id AS (
				SELECT member_id
				FROM member
				WHERE user_id = '$user_id'
			), enrolled_classes AS (
				SELECT class_id
				FROM member
				WHERE user_id = '$user_id'
			), archived_classes AS (
				SELECT class_id
				FROM archive_class
				WHERE user_id = '$user_id'
			), archived_notes AS (
				SELECT note.note_id AS note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE 
					class_id IN (SELECT class_id FROM enrolled_classes) 
					AND member_id IN (SELECT member_id FROM matched_member_id)
			), unenrolled_subjects AS (
				SELECT subject_id
				FROM unenroll
				WHERE member_id IN (SELECT member_id FROM matched_member_id)
			)
			
			SELECT *
			FROM note
			WHERE
				-- notes from enrolled classes should be displayed
				class_id IN (SELECT class_id FROM enrolled_classes)
				-- notes from archived classes should NOT be displayed
				AND class_id NOT IN (SELECT class_id FROM archived_classes)
				-- archived/completed notes should NOT be displayed
				AND note_id NOT IN (SELECT archived_notes.note_id FROM archived_notes)
				-- notes from unenrolled subjects should NOT be displayed
				-- notes that don't fall under any subjects will be displayed (i.e. General Note)
				AND (subject_id NOT IN (SELECT subject_id FROM unenrolled_subjects) OR subject_id IS NULL)";

	if ($selected_process == "due") {
		$process = "AND due_date IS NOT NULL AND DATEDIFF(CURDATE(), due_date) < '1'
							ORDER BY due_date ASC";
	} elseif ($selected_process == "announcement") {
		$process = "AND due_date IS NULL
							ORDER BY post_date ASC";
	} elseif ($selected_process == "late") {
		$process = "AND due_date IS NOT NULL AND DATEDIFF(CURDATE(), due_date) >= '1'
							ORDER BY due_date ASC";
	}

	$result = $conn->query($sql . $process);
	$conn->close();
	return $result;
}

function SelectAllArchiveNote($user_id)
{
	$conn = OpenCon();
	$sql = "WITH matched_member_id AS (
				SELECT member_id
				FROM member
				WHERE user_id = '$user_id'
			), enrolled_classes AS (
				SELECT class_id
				FROM member
				WHERE user_id = '$user_id'
			), archived_notes AS (
				SELECT note_id 
				FROM archive_note 
				WHERE member_id IN (SELECT member_id FROM matched_member_id)
			)

			SELECT archive_note.archive_note_id, note.*
			FROM note
			JOIN archive_note 
			ON note.note_id = archive_note.note_id
			WHERE class_id IN (SELECT class_id FROM enrolled_classes)
				AND member_id IN (SELECT member_id FROM matched_member_id)
				AND note.note_id IN (SELECT note_id FROM archived_notes)
			ORDER BY post_date ASC";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

function ArchiveNoteFromAll($note_id, $class_id, $user_id, $conn)
{
	$sql = "INSERT INTO archive_note (note_id, member_id)
			VALUES ('$note_id', 
					(	SELECT member_id
						FROM member
						WHERE class_id = '$class_id'
							AND user_id = '$user_id'))";
	if ($conn->query($sql) === TRUE) {
		echo "Note archived successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

function RestoreArchiveNoteFromAll($archive_note_id, $conn)
{
	$sql = "DELETE FROM archive_note
	WHERE archive_note_id = '$archive_note_id'";

	if ($conn->query($sql) === TRUE) {
		echo "Note restored successfully";
	} else {
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$conn->close();
}

// Count all the note due today by the user
function CountAllDueNote($user_id)
{
	$conn = OpenCon();
	// Set the time zone
	$conn->query("SET time_zone='+08:00'");
	$sql = "WITH matched_member_id AS (
				SELECT member_id
				FROM member
				WHERE user_id = '$user_id'
			), enrolled_classes AS (
				SELECT class_id
				FROM member
				WHERE user_id = '$user_id'
			), archived_classes AS (
				SELECT class_id
				FROM archive_class
				WHERE user_id = '$user_id'
			), archived_notes AS (
				SELECT note.note_id AS note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE 
					class_id IN (SELECT class_id FROM enrolled_classes) 
					AND member_id IN (SELECT member_id FROM matched_member_id)
			), unenrolled_subjects AS (
				SELECT subject_id
				FROM unenroll
				WHERE member_id IN (SELECT member_id FROM matched_member_id)
			)
			
			SELECT COUNT(note_id) AS 'due_count'
			FROM note
			WHERE
				-- notes from enrolled classes should be displayed
				class_id IN (SELECT class_id FROM enrolled_classes)
				-- notes from archived classes should NOT be displayed
				AND class_id NOT IN (SELECT class_id FROM archived_classes)
				-- archived/completed notes should NOT be displayed
				AND note_id NOT IN (SELECT archived_notes.note_id FROM archived_notes)
				-- notes from unenrolled subjects should NOT be displayed
				-- notes that don't fall under any subjects will be displayed (i.e. General Note)
				AND (subject_id NOT IN (SELECT subject_id FROM unenrolled_subjects) OR subject_id IS NULL)
				-- due date must be the current date to get the today
				AND due_date = CURDATE()";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn -> close();
	return $row['due_count'];
}

// Count all LATE the note due today by the user
function CountAllLateDueNote($user_id)
{
	$conn = OpenCon();
	// Set the time zone
	$conn->query("SET time_zone='+08:00'");
	$sql = "WITH matched_member_id AS (
				SELECT member_id
				FROM member
				WHERE user_id = '$user_id'
			), enrolled_classes AS (
				SELECT class_id
				FROM member
				WHERE user_id = '$user_id'
			), archived_classes AS (
				SELECT class_id
				FROM archive_class
				WHERE user_id = '$user_id'
			), archived_notes AS (
				SELECT note.note_id AS note_id
				FROM note
				JOIN archive_note
				ON note.note_id = archive_note.note_id
				WHERE 
					class_id IN (SELECT class_id FROM enrolled_classes) 
					AND member_id IN (SELECT member_id FROM matched_member_id)
			), unenrolled_subjects AS (
				SELECT subject_id
				FROM unenroll
				WHERE member_id IN (SELECT member_id FROM matched_member_id)
			)
			
			SELECT COUNT(note_id) AS 'late_due_count'
			FROM note
			WHERE
				-- notes from enrolled classes should be displayed
				class_id IN (SELECT class_id FROM enrolled_classes)
				-- notes from archived classes should NOT be displayed
				AND class_id NOT IN (SELECT class_id FROM archived_classes)
				-- archived/completed notes should NOT be displayed
				AND note_id NOT IN (SELECT archived_notes.note_id FROM archived_notes)
				-- notes from unenrolled subjects should NOT be displayed
				-- notes that don't fall under any subjects will be displayed (i.e. General Note)
				AND (subject_id NOT IN (SELECT subject_id FROM unenrolled_subjects) OR subject_id IS NULL)
				-- due date must be the current date to get the today
				AND DATEDIFF(CURDATE(), due_date) >= '1'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$conn -> close();
	return $row['late_due_count'];
}
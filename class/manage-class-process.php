<?php
include '../dbconnect.php';
OpenSession();

// Delete the subject from the table
if($_POST['process_type'] == "delete"){
	$delete_subject_message = DeleteSubject($_POST['subject_id']);
	DeleteSubjectCalendarSubjectID($_POST['subject_id']);
	echo $delete_subject_message;
}

// Insert into subject table then to subject schedule table
if($_POST['process_type'] == "insert"){
	// Open database connection for the nl2br
	$conn = OpenCon();

	$subject_details = NULL;
	// Check if subject_code is empty if not properly put the strings for SQL format
	if (isset($_POST['subject_details']) && !empty($_POST['subject_details'])) {
		//nl2br converts the line breaks
		//mysqli_real_escape_string converts the special characters
		$subject_details = mysqli_real_escape_string($conn, nl2br($_POST['subject_details']));
	}

	$professor = NULL;
	if (isset($_POST['professor']) && !empty($_POST['professor'])) {
		$professor = mysqli_real_escape_string($conn, nl2br($_POST['professor']));
	}

	// INSERT to subject table
	$subject_id = 0;
	if (isset($_POST['subject_name']) && !empty($_POST['subject_name'])) {
		// Send the parameters to InsertSubject() function to insert to the subject table then return subject id
		$subject_id = InsertSubject(
			$_POST['subject_name'],
			$subject_details,
			$professor,
			$_POST['class_id'],
			$conn
		);
	}

	// Insert the schedule for the subject using the returned subject id
	if($subject_id > 0){
		for ($i=0; $i < sizeof($_POST['start_date']); $i++) { 
			$subject_schedule_id = InsertSubjectSchedule($subject_id, $_POST['from_time'][$i], $_POST['to_time'][$i], $_POST['start_date'][$i], $_POST['occurrence'][$i], $_POST['class_id']);

			// Insert the schedule to subject calendar
			for ($j=0; $j < $_POST['occurrence'][$i]; $j++) { 
				$event_title = $_POST['subject_name'];
				$event_details = $subject_details;
				// Calculate for the next date or next recurrence
				$event_date = date('Y-m-d', strtotime('+'.$j.' week', strtotime($_POST['start_date'][$i])));
				$event_from_date = date('Y-m-d H:i:s', strtotime("$event_date ".$_POST['from_time'][$i]));
				$event_to_date = date('Y-m-d H:i:s', strtotime("$event_date ".$_POST['to_time'][$i]));
				InsertSubjectCalendar($event_title, $event_details, $event_from_date, $event_to_date, $subject_id, $_POST['class_id'], $subject_schedule_id);
			}
		}
	}
}

// Update subject table then subject schedule table
if($_POST['process_type'] == "update"){
	// Open database connection for the nl2br
	$conn = OpenCon();

	$subject_details = NULL;
	// Check if subject_code is empty if not properly put the strings for SQL format
	if (isset($_POST['subject_details']) && !empty($_POST['subject_details'])) {
		//nl2br converts the line breaks
		//mysqli_real_escape_string converts the special characters
		$subject_details = mysqli_real_escape_string($conn, nl2br($_POST['subject_details']));
	}

	$professor = NULL;
	if (isset($_POST['professor']) && !empty($_POST['professor'])) {
		$professor = mysqli_real_escape_string($conn, nl2br($_POST['professor']));
	}

	if (isset($_POST['subject_id']) && !empty($_POST['subject_id'])) {

		// Update subject table
		if(UpdateSubject($_POST['subject_id'], $_POST['subject_name'], $subject_details, $professor, $_POST['class_id'], $conn) == TRUE){
			// Update the subject schedule by loopin on the array
			for ($i=0; $i < sizeof($_POST['start_date']); $i++) { 
				// If the subject schedule is not set it means it is a new subject schedule. Insert the data to subject schedule
				if(!isset($_POST['subject_schedule_id'][$i]) || empty($_POST['subject_schedule_id'][$i])){
					$subject_schedule_id = InsertSubjectSchedule($_POST['subject_id'], $_POST['from_time'][$i], $_POST['to_time'][$i], $_POST['start_date'][$i], $_POST['occurrence'][$i], $_POST['class_id']);

					// Insert the schedule to subject calendar
					for ($j=0; $j < $_POST['occurrence'][$i]; $j++) { 
						$event_title = $_POST['subject_name'];
						$event_details = $subject_details;
						// Calculate for the next date or next recurrence
						$event_date = date('Y-m-d', strtotime('+'.$j.' week', strtotime($_POST['start_date'][$i])));
						$event_from_date = date('Y-m-d H:i:s', strtotime("$event_date ".$_POST['from_time'][$i]));
						$event_to_date = date('Y-m-d H:i:s', strtotime("$event_date ".$_POST['to_time'][$i]));
						InsertSubjectCalendar($event_title, $event_details, $event_from_date, $event_to_date, $_POST['subject_id'], $_POST['class_id'], $subject_schedule_id);
					}
				}
				// If the subject schedule is already set the row needs to be updated
				else{
					UpdateSubjectSchedule($_POST['subject_schedule_id'][$i], $_POST['subject_id'], $_POST['from_time'][$i], $_POST['to_time'][$i], $_POST['start_date'][$i], $_POST['occurrence'][$i], $_POST['class_id']);

					// Delete the schedule calendar 
					DeleteSubjectCalendar($_POST['subject_schedule_id'][$i]);

					// Insert the schedule to subject calendar
					for ($j=0; $j < $_POST['occurrence'][$i]; $j++) { 
						$event_title = $_POST['subject_name'];
						$event_details = $subject_details;
						// Calculate for the next date or next recurrence
						$event_date = date('Y-m-d', strtotime('+'.$j.' week', strtotime($_POST['start_date'][$i])));
						$event_from_date = date('Y-m-d H:i:s', strtotime("$event_date ".$_POST['from_time'][$i]));
						$event_to_date = date('Y-m-d H:i:s', strtotime("$event_date ".$_POST['to_time'][$i]));
						InsertSubjectCalendar($event_title, $event_details, $event_from_date, $event_to_date, $_POST['subject_id'], $_POST['class_id'], $_POST['subject_schedule_id'][$i]);
					}
				}
			}
		}
	}
}

// Delete subject schedule
if ($_POST['process_type'] == "delete-schedule") {
	// Delete subject schedule
	if(DeleteSubjectSchedule($_POST['subject_schedule_id']) == TRUE){
		DeleteSubjectCalendar($_POST['subject_schedule_id']);
		echo "Schedule Deleted";
	}
}
?>
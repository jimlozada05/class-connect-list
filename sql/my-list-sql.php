<?php
function GetUserNote($user_id)
{
    $conn = OpenCon();
    $sql = "(SELECT *
			FROM user_note
			WHERE user_id = '$user_id'
            AND note_id NOT IN (SELECT note_id FROM archive_user_note)
            )
            UNION ALL
            (SELECT note_id, NULL AS 'user_id', post_date, due_date, due_time, note_title, description
            FROM note
            WHERE note_id IN (
                SELECT note_id
                FROM user_class_note
                WHERE user_id = '$user_id')
            )
            ORDER BY post_date";
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

function GetUserNoteArchive($user_id)
{
    $conn = OpenCon();
    $sql = "SELECT *
			FROM user_note AS user 
            JOIN archive_user_note AS archive
            ON user.note_id = archive.note_id
			WHERE user.user_id = '$user_id'
            ORDER BY archive.archive_user_note_id DESC";
    $result = $conn->query($sql);
    $conn->close();
    return $result;
}

function InsertMyListNote($user_id, $due_date, $due_time, $note_title, $description, $conn)
{
    $offset = SynchTimeZone();
    // Change the timezone first then proceed with the query, this is a multi_query instead of a query
    $sql = "SET time_zone='$offset'";
    $conn->query($sql);
	$sql = "INSERT INTO user_note (user_id, post_date, due_date, due_time, note_title, description)
			VALUES ('$user_id', CURDATE(), " .
        ($due_date == null ? "NULL" : "'$due_date'")
        . ", " .
        ($due_time == null ? "NULL" : "'$due_time'")
        . ", '$note_title', '$description')";
    if ($conn->query($sql) === TRUE) {
        // Add to user calendar after adding the note
        if($due_date != null){
            // If due time is null then set it as 08 AM
            if($due_time == null){
                $due_time = "23:29:00";
            }
            $start_datetime = date('Y-m-d H:i:s', strtotime("$due_date $due_time"));
            $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . "+30 minutes"));
            // Insert into user calendar table and pass the note_id as the last insert id
            InsertUserCalendarNote($note_title, $description, $start_datetime, $end_datetime, $user_id, $conn->insert_id);
        }
        echo "Note added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function InsertClassNoteToMyList($user_id, $note_id)
{
    $conn = OpenCon();
    $sql = "SELECT user_class_note_id
            FROM user_class_note
            WHERE note_id = '$note_id' AND user_id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        echo "Note has already been added!";
    }
    else{
        $sql = "INSERT INTO user_class_note (note_id, user_id)
        VALUES ('$note_id',  '$user_id')";
        if ($conn->query($sql) === TRUE) {
            echo "Inserted to User List successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
    $conn->close();
}

function SelectUserNoteRecord($note_id)
{
    $conn = OpenCon();
    $sql = "SELECT *
			FROM user_note
            WHERE note_id = '$note_id'";
    $result = $conn->query($sql);
    $conn->close();
    return $result->fetch_assoc();
}

function UpdateUserNote($note_id, $user_id, $due_date, $due_time, $note_title, $description, $conn)
{
    $offset = SynchTimeZone();
    $sql = "SET time_zone='$offset';

			UPDATE user_note
			SET user_id = " . ($user_id == null ? "NULL" : "'$user_id'") . ", 
                due_date = " . ($due_date == null ? "NULL" : "'$due_date'") . ", 
                due_time = " . ($due_time == null ? "NULL" : "'$due_time'") . ", 
                note_title = '$note_title', description = '$description'
			WHERE note_id = '$note_id'";
    if ($conn->multi_query($sql) === TRUE) {
        // Check if there is a due date then add the user_note to user_calendar
        if($due_date != null){
            // If due time is null then set it as 08 AM
            if($due_time == null){
                $due_time = "23:29:00";
            }
            $start_datetime = date('Y-m-d H:i:s', strtotime("$due_date $due_time"));
            $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . "+30 minutes"));
            // Update note table then update calendar table using the note id
            UpdateUserCalendarNote($note_title, $description, $start_datetime, $end_datetime, $user_id, $note_id);
        }
        if($due_date == null){
            // if due date is null then the calendar event must be deleted
            $conn->next_result();
            // The next result command is needed since it is not possible to run two simultatnues query like DELETE, INSERT, or UPDATE
            $sql = "DELETE FROM user_calendar
                    WHERE event_id = (SELECT event_id
                        FROM user_note_calendar
                        WHERE note_id = '$note_id');
                    DELETE FROM user_note_calendar
                    WHERE note_id = '$note_id';";
            $conn->multi_query($sql);
        }
        echo "Note updated successfully";
    }
    else{
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function ArchiveUserNote($note_id, $user_id, $conn)
{
    $sql = "INSERT INTO archive_user_note (note_id, user_id)
    VALUES ('$note_id',  $user_id)";
    if ($conn->query($sql) === TRUE) {
        echo "Note archived successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function RestoreArchiveUserNote($archive_user_note_id, $conn)
{
    $sql = "DELETE FROM archive_user_note
    WHERE archive_user_note_id = '$archive_user_note_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Note restored successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

// Delete a user note
function DeleteUserNote($note_id, $conn)
{
    $sql = "DELETE FROM user_note
            WHERE note_id = '$note_id'";
    if ($conn->query($sql) === TRUE) {
        // Also delete the calendar note
        DeleteUserCalendarNote($note_id);
        echo "Note deleted";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

function RemoveUserNote($note_id, $conn){
    $sql = "DELETE FROM user_class_note
            WHERE note_id = '$note_id'";
    if ($conn->query($sql) === TRUE) {
        echo "Note removed";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Insert user note after inserting to user calendar
function InsertNoteUserCalendar($event_title, $description, $start_datetime, $end_datetime, $user_id, $event_id){
    $conn = OpenCon();
    // Convert the date to the proper format
    $date = date('Y-m-d H:i:s', strtotime($start_datetime));
    $sql = "INSERT INTO user_note (user_id, post_date, due_date, due_time, note_title, description)
            VALUES ('$user_id', CURDATE(), DATE('$date'), TIME('$date'), '$event_title', '$description')";
    if($conn->query($sql) === TRUE){
        // Insert into the user_note_calendar table so the calendar and note can be referenced
        $sql = "INSERT INTO user_note_calendar (note_id, event_id)
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

// Update note when the user calendar has been updated
function UpdateNoteUserCalendar($event_title, $description, $start_datetime, $end_datetime, $user_id, $event_id){
    $conn = OpenCon();
    // Convert the date to the proper format
    $date = date('Y-m-d H:i:s', strtotime($start_datetime));
    $sql = "UPDATE user_note
            SET due_date = DATE('$date'), due_time = TIME('$date'), note_title = '$event_title', description = '$description'
            WHERE note_id = (SELECT note_id
                FROM user_note_calendar
                WHERE event_id = '$event_id')";
    if($conn->query($sql) === TRUE){
        echo "Note updated! \n";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}

// Delete note when the user calendar event has been deleted
function DeleteNoteUserCalendar($event_id){
    $conn = OpenCon();
    // Clear the query
    $conn->next_result();
    $sql = "DELETE FROM user_note
            WHERE note_id = (SELECT note_id
                FROM user_note_calendar
                WHERE event_id = '$event_id');
            DELETE FROM user_note_calendar
            WHERE event_id = '$event_id'";
    if($conn->multi_query($sql) === TRUE){
        echo "Note removed! \n";
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $conn->close();
}
?>
<?php

function GetResourcesFromGeneral($class_id){
    $conn = OpenCon();
	$sql = "SELECT due_date, note_title, link, description
            FROM note
            WHERE class_id = '$class_id'
                AND subject_id IS NULL AND link IS NOT NULL";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}

function GetResourcesFromSubject($subject_id){
    $conn = OpenCon();
	$sql = "SELECT due_date, note_title, link, description
            FROM note AS n
            JOIN subject AS s
            ON n.subject_id = s.subject_id
            WHERE s.subject_id = '$subject_id' 
                AND n.link IS NOT NULL";
	$result = $conn->query($sql);
	$conn->close();
	return $result;
}
?>
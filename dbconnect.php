<?php
// Check if session already exist
function OpenSession(){
	if (session_status() === PHP_SESSION_NONE) {
	    // Start session
	    session_start();
	    // Synch the time zone for PH time
	    SynchTimeZone();
	}

	// Check if user_id session exist, if not the user is not logged in
	if(!isset($_SESSION['user_id']) && empty($_SESSION['user_id'])){
	    header("Location: ../index.php");
	}
}

// Open the Database Connection
function OpenCon(){
	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$db = "list";
	// Create the SQL connection
	$conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

	return $conn;
}

// Change the PHP timezone
function SynchTimeZone(){
	// Set PHP time zone
	define('TIMEZONE', 'Asia/Singapore');
	date_default_timezone_set(TIMEZONE);

	// Calculate PHP time zone
	$now = new DateTime();
	$mins = $now->getOffset() / 60;
	$sgn = ($mins < 0 ? -1 : 1);
	$mins = abs($mins);
	$hrs = floor($mins / 60);
	$mins -= $hrs * 60;
	$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
	
	// Return PHP timezone to use for SQL
	return $offset;
}

// Prints the div of the user based nav bar
function DisplayNavHeader()
{
    $output = "
    <div class=\"container-top\">
        <ul class=\"topnav\">
			<li class=\"title\" style=\"flex-grow: 1\"><a href=\"../class/no-class.php\">CLASS CONNECT: LIST</a></li>
            <li><a href=\"../class/with-class.php\">CLASSES</a></li>
            <li><a href=\"../calendar/calendar.php\">CALENDAR</a></li>
            <li><a href=\"../resources/resources.php\">RESOURCES</a></li>
            <li><a href=\"../my-list/my-list.php\">MY LIST</a></li>
            <li><a class=\"profile\" href=../logout.php>LOGOUT</a></li>
        </ul>
    </div>
    ";
    echo $output;
}

include 'sql/notes-sql.php';
include 'sql/class-sql.php';
include 'sql/user-sql.php';
include 'sql/calendar-sql.php';
include 'sql/my-list-sql.php';
include 'sql/resources-sql.php';
?>
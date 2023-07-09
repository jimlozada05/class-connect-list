<?php
include "dbconnect.php";
// Check if session exist
OpenSession();
session_destroy(); //destroy the session
header("location:index.php"); //to redirect back to "index.php" after logging out
exit();


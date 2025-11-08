<?php
$host = "localhost";
$user = "root";
$pass = ""; // leave empty if none
$dbname = "trabawho"; // change to your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
// db.php — database connection (mysqli)
$host = 'localhost';
$user = 'root';
$pass = ''; // default XAMPP password
$dbname = 'trabawho';
$port = 3306; // your custom MySQL port

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
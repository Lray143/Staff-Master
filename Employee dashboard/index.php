<?php
include "db.php";

$sql = "SELECT * FROM jobs ORDER BY created_at DESC";
$result = $conn->query($sql);

$jobs = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
...

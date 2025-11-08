<?php
// welcome.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Get user info from session
$first_name = $_SESSION['first_name'];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Welcome - Staff Master</title>
<style>
body { font-family: Arial; padding: 20px; }
</style>
</head>
<body>
<h2>Welcome, <?= htmlspecialchars($first_name) ?>!</h2>
<p>You are now logged in to Staff Master.</p>
<p><a href="logout.php">Logout</a></p>
</body>
</html>
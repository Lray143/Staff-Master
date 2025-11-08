<?php
require_once 'db.php';

$message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id, verified FROM users WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($id, $verified);
    $stmt->fetch();
    $stmt->close();

    if (isset($id)) {
        if ($verified == 1) {
            $message = "Your account is already verified. You can <a href='login.php'>login</a>.";
        } else {
            $upd = $conn->prepare("UPDATE users SET verified = 1, token = NULL WHERE id = ?");
            $upd->bind_param("i", $id);
            if ($upd->execute()) {
                $message = "Your email has been verified! You can now <a href='login.php'>login</a>.";
            } else {
                $message = "Verification failed. Please try again.";
            }
            $upd->close();
        }
    } else {
        $message = "Invalid verification token.";
    }
} else {
    $message = "No verification token provided.";
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Email Verification - TrabaWho</title>
<style>
body{font-family:Arial; display:flex; justify-content:center; align-items:center; height:100vh; background:#f4f4f4;}
div{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
a{color:#007bff;text-decoration:none;}
</style>
</head>
<body>
<div>
<h2>Email Verification</h2>
<p><?= $message ?></p>
</div>
</body>
</html>
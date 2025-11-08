<?php
session_start();
require_once 'db.php';

$message = '';
$show_form = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    if (isset($id)) {
        $show_form = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm'] ?? '';

            if ($password === '' || $confirm === '') {
                $message = 'Please fill in all fields.';
            } elseif ($password !== $confirm) {
                $message = 'Passwords do not match.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE id = ?");
                $upd->bind_param('si', $hash, $id);
                if ($upd->execute()) {
                    $message = 'Password reset successful! You can <a href="login.php">login</a> now.';
                    $show_form = false;
                } else {
                    $message = 'Error updating password: ' . $conn->error;
                }
                $upd->close();
            }
        }
    } else {
        $message = 'Invalid or expired token.';
    }
} else {
    $message = 'No token provided.';
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Reset Password - TrabaWho</title>
<style>
body{font-family:Arial; background:#f4f4f4; display:flex; align-items:center; justify-content:center; height:100vh; margin:0;}
form{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);width:320px;}
input{width:100%;padding:8px;margin:8px 0;}
button{width:100%;padding:10px;background:#28a745;border:none;color:#fff;border-radius:5px;cursor:pointer;}
p.msg{color:#28a745;text-align:center;}
p.err{color:#c00;text-align:center;}
a{color:#007bff;text-decoration:none;}
</style>
</head>
<body>
<?php if($show_form): ?>
<form method="post" action="">
<h2>Reset Password</h2>
<input type="password" name="password" placeholder="New Password" required>
<input type="password" name="confirm" placeholder="Confirm Password" required>
<button type="submit">Reset Password</button>
</form>
<?php endif; ?>
<p class="msg"><?= $message ?></p>
<p style="text-align:center"><a href="./login.php">Back to Login</a></p>
</body>
</html>
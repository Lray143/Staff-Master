<?php
session_start();
require_once 'db.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '') {
        $message = 'Please enter your email.';
    } else {
        $stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->bind_result($id, $first_name);
        $stmt->fetch();
        $stmt->close();

        if (isset($id)) {
            $token = bin2hex(random_bytes(16));
            $upd = $conn->prepare("UPDATE users SET reset_token = ? WHERE id = ?");
            $upd->bind_param('si', $token, $id);
            $upd->execute();
            $upd->close();

            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'starloomgamingstudio@gmail.com';
                $mail->Password = 'ngyq nnls shzo lsdy';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('no-reply@trabawho.com', 'TrabaWho');
                $mail->addAddress($email, $first_name);
                $mail->Subject = 'Reset Your Password';
                $mail->Body = "Hi $first_name,\n\nClick the link below to reset your password:\n
                http://localhost/Staff-Master/login/ResetPassword.php?token=$token";

                $mail->send();
                $message = 'Password reset link sent to your email.';
            } catch (Exception $e) {
                $message = 'Error sending email: ' . $mail->ErrorInfo;
            }
        } else {
            $message = 'Email not found.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Forgot Password - TrabaWho</title>
<style>
body{font-family:Arial; background:#f4f4f4; display:flex; align-items:center; justify-content:center; height:100vh; margin:0;}
form{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);width:320px;}
input{width:100%;padding:8px;margin:8px 0;}
button{width:100%;padding:10px;background:#007bff;border:none;color:#fff;border-radius:5px;cursor:pointer;}
p.msg{color:#28a745;text-align:center;}
p.err{color:#c00;text-align:center;}
a{color:#007bff;text-decoration:none;}
</style>
</head>
<body>
<form method="post" action="">
<h2>Forgot Password</h2>
<input type="email" name="email" placeholder="Enter your email" required>
<button type="submit">Send Reset Link</button>
<?php if($message): ?><p class="msg"><?= htmlspecialchars($message) ?></p><?php endif; ?>
<p style="text-align:center"><a href="./login.php">Back to Login</a></p>
</form>
</body>
</html>
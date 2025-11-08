<?php
session_start();
require_once 'db.php';

$error = '';

if (isset($_SESSION['email'])) {
    header('Location: ..\Employee dashboard\dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, password, verified, first_name, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash, $verified, $first_name, $role);
            $stmt->fetch();

            if ($verified == 0) {
                $error = 'Your email is not verified. Please check your inbox.';
            } elseif (password_verify($password, $hash)) {
                $_SESSION['email'] = $email;
                $_SESSION['user_id'] = $id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['role'] = $role;
                $stmt->close();
                header('Location: ..\Employee dashboard\dashboard.php');
                exit();
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
        $stmt->close();
    }
}

$registered = isset($_GET['registered']);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login - TrabaWho</title>
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
<h2>TrabaWho Login</h2>
<?php if($registered): ?><p class="msg">Registration successful. Please check your email to verify your account.</p><?php endif; ?>
<?php if($error): ?><p class="err"><?=htmlspecialchars($error)?></p><?php endif; ?>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
<p style="text-align:center">Don't have an account? <a href="./register.php">Create one</a></p>
<p style="text-align:center"><a href="./ForgotPassword.php">Forgot Password?</a></p>
</form>
</body>
</html>
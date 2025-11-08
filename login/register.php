<?php

//ON CMD install phpmailer ----  C:\xampp\htdocs\Staff-Master\login  ----
//composer require phpmailer/phpmailer
// Once installed restart apache and mysql on xampp

/*CREATE DATABASE IF NOT EXISTS trabawho;
USE trabawho;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    birthday DATE NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    highest_education VARCHAR(50) NOT NULL,
    role ENUM('employee','employer','admin') NOT NULL DEFAULT 'employee',
    password VARCHAR(255) NOT NULL,
    verified TINYINT(1) NOT NULL DEFAULT 0,
    token VARCHAR(64) DEFAULT NULL,
    reset_token VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);*/

session_start();
require_once 'db.php';
require 'vendor/autoload.php'; // PHPMailer via Composer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birthday = $_POST['birthday'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $education = $_POST['education'] ?? '';
    $role = $_POST['role'] ?? '';

    if ($first_name === '' || $last_name === '' || $birthday === '' || $email === '' || $password === '' || $education === '' || $role === '') {
        $message = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = 'Email already registered.';
            $stmt->close();
        } else {
            $stmt->close();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));

            $ins = $conn->prepare("INSERT INTO users (first_name,last_name,birthday,email,password,highest_education,role,token) VALUES (?,?,?,?,?,?,?,?)");
            $ins->bind_param('ssssssss', $first_name, $last_name, $birthday, $email, $hash, $education, $role, $token);

            if ($ins->execute()) {
                $ins->close();

                // Send verification email
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
                    $mail->Subject = 'Verify Your Email';
                    $mail->isHTML(true);
                    $mail->Body = "
                        <h3>Hello $first_name,</h3>
                        <p>Click the link below to verify your email and activate your account:</p>
                        <p><a href='http://localhost/Staff-Master/login/verify.php?token=$token'>Verify Email</a></p>
                        <p>If you didn't register, ignore this email.</p>
                    ";
                    $mail->send();
                    $message = 'Registration successful! Please check your email to verify your account.';
                } catch (Exception $e) {
                    $message = "Registration successful, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $message = 'Registration error: ' . $conn->error;
                $ins->close();
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register - TrabaWho</title>
<style>
body{font-family:Arial; background:#f4f4f4; display:flex; align-items:center; justify-content:center; height:100vh; margin:0;}
form{background:#fff;padding:20px;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);width:350px;}
input, select{width:100%;padding:8px;margin:8px 0;}
button{width:100%;padding:10px;background:#28a745;border:none;color:#fff;border-radius:5px;cursor:pointer;}
p.msg{color:#c00;text-align:center;}
a{color:#007bff;text-decoration:none;}
</style>
</head>
<body>
<form method="post" action="">
  <h2>Create Account</h2>
  <input type="text" name="first_name" placeholder="First Name" required>
  <input type="text" name="last_name" placeholder="Last Name" required>
  <input type="date" name="birthday" placeholder="Birthday" required>
  <input type="email" name="email" placeholder="Email Address" required>
  <input type="password" name="password" placeholder="Password" required>
  <input type="password" name="confirm" placeholder="Confirm Password" required>
  <select name="education" required>
    <option value="">Highest Educational Attainment</option>
    <option value="High School">High School</option>
    <option value="Vocational">Vocational</option>
    <option value="College">College</option>
    <option value="Postgraduate">Postgraduate</option>
  </select>
  <select name="role" required>
    <option value="">Select Role</option>
    <option value="employee">Employee</option>
    <option value="employer">Employer</option>
    <option value="admin">Admin</option>
  </select>
  <button type="submit">Register</button>
  <?php if($message): ?><p class="msg"><?=htmlspecialchars($message)?></p><?php endif; ?>
  <p style="text-align:center">Already have an account? <a href="./login.php">Login</a></p>
</form>
</body>
</html>
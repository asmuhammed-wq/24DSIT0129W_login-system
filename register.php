<?php
session_start();
include 'config.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$role = trim($_POST['role'] ?? '');

$allowedRoles = ['admin', 'teacher', 'student'];
if ($name === '' || $email === '' || $password === '' || !in_array($role, $allowedRoles, true)) {
    $_SESSION['register_error'] = 'Please provide valid name, email, password and role.';
    $_SESSION['active_form'] = 'register';
    header('Location: index.php');
    exit();
}

$stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $_SESSION['register_error'] = 'Email already exists!';
    $_SESSION['active_form'] = 'register';
    $stmt->close();
    header('Location: index.php');
    exit();
}
$stmt->close();

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$insert = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
$insert->bind_param('ssss', $name, $email, $hashedPassword, $role);
if (!$insert->execute()) {
    $_SESSION['register_error'] = 'Registration failed: ' . $insert->error;
    $_SESSION['active_form'] = 'register';
    $insert->close();
    header('Location: index.php');
    exit();
}
$insert->close();

$_SESSION['active_form'] = 'login';
header('Location: index.php');
exit();
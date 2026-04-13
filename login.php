<?php
session_start();
include 'config.php';

function is_bcrypt_hash(string $hash): bool {
    return (bool) preg_match('/^\$2[aby]\$\d{2}\$[\.\/A-Za-z0-9]{53}$/', $hash);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $_SESSION['login_error'] = 'Please enter both email and password.';
        $_SESSION['active_form'] = 'login';
        header('Location: index.php');
        exit();
    }

    $stmt = $conn->prepare('SELECT id, name, password, role FROM users WHERE email = ? LIMIT 1');
    if (!$stmt) {
        $_SESSION['login_error'] = 'Database error: ' . $conn->error;
        header('Location: index.php');
        exit();
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $_SESSION['login_error'] = 'Database error: ' . $stmt->error;
        $_SESSION['active_form'] = 'login';
        header('Location: index.php');
        exit();
    }

    if ($result->num_rows !== 1) {
        $_SESSION['login_error'] = 'User not found!';
        $_SESSION['active_form'] = 'login';
        header('Location: index.php');
        exit();
    }

    $user = $result->fetch_assoc();
    $dbPassword = $user['password'];

    $loginOk = false;

    if (is_bcrypt_hash($dbPassword) && password_verify($password, $dbPassword)) {
        $loginOk = true;

        if (password_needs_rehash($dbPassword, PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $updt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
            if ($updt) {
                $updt->bind_param('si', $newHash, $user['id']);
                $updt->execute();
                $updt->close();
            }
        }

    } elseif ($password === $dbPassword) {
        // Legacy plaintext fallback
        $loginOk = true;
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $updt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
        if ($updt) {
            $updt->bind_param('si', $newHash, $user['id']);
            $updt->execute();
            $updt->close();
        }
    }

    if (!$loginOk) {
        $_SESSION['login_error'] = 'Wrong password!';
        $_SESSION['active_form'] = 'login';
        header('Location: index.php');
        exit();
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['name'];
    $_SESSION['role'] = $user['role'];

    header('Location: dashboard.php');
    exit();
}

// Fallback for non-POST access - redirect
header('Location: index.php');
exit();
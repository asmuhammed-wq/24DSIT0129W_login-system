<?php
session_start();

// Handle errors
$login_error = $_SESSION['login_error'] ?? '';
$register_error = $_SESSION['register_error'] ?? '';
$active = $_SESSION['active_form'] ?? 'login';

// Clear session messages
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EDU MANAGE HUB</title>

<style>
/* (UNCHANGED CSS — exactly as you gave it) */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(120deg, #1abc9c, #3498db);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.container {
    display: flex;
    width: 800px;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.left {
    flex: 1;
    background: #2c3e50;
    color: white;
    padding: 40px;
}

.left h1 { margin-bottom: 20px; }
.left p { line-height: 1.6; }

.right {
    flex: 1;
    padding: 40px;
}

.form-box { display: none; }
.form-box.active { display: block; }

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    width: 100%;
    padding: 10px;
    background: #1abc9c;
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
}

button:hover { background: #16a085; }

.switch {
    margin-top: 10px;
    text-align: center;
}

.switch span {
    color: #3498db;
    cursor: pointer;
    font-weight: bold;
}

.error {
    color: red;
    font-size: 14px;
}

@keyframes fade {
    from {opacity: 0;}
    to {opacity: 1;}
}
.form-box { animation: fade 0.5s ease; }
</style>

</head>

<body>

<div class="container">

    <!-- LEFT SIDE -->
    <div class="left">
        <h1>🎓 EDU MANAGE HUB</h1>
        <p>
            Welcome to <strong>Edu Manage Hub</strong>, a smart platform designed to manage 
            students, teachers, and courses efficiently.
        </p>
        <p>
            ✔ Easy student tracking<br>
            ✔ Teacher management<br>
            ✔ Course organization<br>
            ✔ Secure login system
        </p>
    </div>

    <!-- RIGHT SIDE -->
    <div class="right">

        <!-- LOGIN FORM -->
        <div class="form-box <?php echo ($active=='login')?'active':''; ?>" id="login">
            <h2>Login</h2>

            <?php if($login_error): ?>
                <p class="error"><?php echo $login_error; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="password" required>
                <button type="submit">Login</button>
            </form>

            <div class="switch">
                Don't have an account? 
                <span onclick="showForm('register')">Register</span>
            </div>
        </div>

        <!-- REGISTER FORM -->
        <div class="form-box <?php echo ($active=='register')?'active':''; ?>" id="register">
            <h2>Register</h2>

            <?php if($register_error): ?>
                <p class="error"><?php echo $register_error; ?></p>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>

                <select name="role" required style="width:100%; padding:10px; margin-top:10px;">
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>

                <button type="submit">Register</button>
            </form>

            <div class="switch">
                Already have an account? 
                <span onclick="showForm('login')">Login</span>
            </div>
        </div>

    </div>

</div>

<script>
function showForm(form) {
    document.getElementById('login').classList.remove('active');
    document.getElementById('register').classList.remove('active');
    document.getElementById(form).classList.add('active');
}
</script>

</body>
</html>
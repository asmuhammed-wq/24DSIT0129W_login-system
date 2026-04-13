<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 20px;
            width: 400px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        }

        h2 {
            margin-bottom: 10px;
            font-weight: 700;
        }

        p {
            margin-bottom: 30px;
            font-size: 16px;
            color: #e0e7ff;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 15px 0;
        }

        ul li a {
            display: block;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            color: white;
            background: rgba(255,255,255,0.15);
            transition: 0.3s;
            font-weight: 500;
        }

        ul li a:hover {
            background: #2563eb;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            opacity: 0.8;
            transition: 0.3s;
        }

        .back:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* Glow effect */
        .panel::before {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(59,130,246,0.4);
            border-radius: 50%;
            filter: blur(80px);
            top: -50px;
            left: -50px;
        }
    </style>
</head>

<body>

<div class="panel">
    <h2>⚙️ Admin Panel</h2>

    <p>Welcome Admin: <strong><?php echo $_SESSION['username']; ?></strong></p>

    <ul>
        <li><a href="students.php">👨‍🎓 Manage Students</a></li>
        <li><a href="teachers.php">👨‍🏫 Manage Teachers</a></li>
        <li><a href="courses.php">📚 Manage Courses</a></li>
    </ul>

    <a class="back" href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
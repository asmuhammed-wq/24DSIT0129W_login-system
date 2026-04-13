<?php
session_start();
include 'config.php';

if ($_SESSION['role'] !== 'admin') exit();

// MESSAGE SYSTEM
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "";
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id AND role='student'");

    $_SESSION['message'] = "🗑️ Student deleted successfully!";
}

// ADD (ALWAYS INSERT — no dependency on existing user)
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash("1234", PASSWORD_BCRYPT);

    // CHECK DUPLICATE EMAIL (prevents crash)
    $check = $conn->query("SELECT * FROM users WHERE email='$email' AND role='student'");

    if ($check->num_rows > 0) {
        $_SESSION['message'] = "⚠️ Student already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'student')");
        $stmt->bind_param("sss", $name, $email, $pass);
        $stmt->execute();

        $_SESSION['message'] = "✅ Student added successfully!";
    }

    header("Location: students.php");
    exit();
}

// FETCH
$result = $conn->query("SELECT * FROM users WHERE role='student'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Students</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: 'Segoe UI';
        }

        .container-box {
            max-width: 950px;
            margin: 40px auto;
        }

        .card-custom {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 10px 35px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
        }

        .btn-add {
            width: 100%;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #5a67d8);
            color: white;
            font-weight: bold;
            transition: 0.2s;
        }

        .btn-add:hover {
            transform: scale(1.05);
        }

        table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #4c51bf;
            color: white;
            text-align: center;
        }

        tr:hover {
            background: #f1f5f9;
        }

        .btn-delete {
            background: #e3342f;
            color: white;
            border-radius: 8px;
            padding: 5px 12px;
            text-decoration: none;
        }

        .btn-delete:hover {
            background: #c53030;
        }

        .alert-box {
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .success { background: #dcfce7; color: #166534; }
        .warning { background: #fef9c3; color: #854d0e; }

        .back-btn {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            border-radius: 10px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: #4b5563;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

<div class="container-box">

    <div class="card-custom">

        <h2>🎓 Manage Students</h2>

        <!-- MESSAGE -->
        <?php if (!empty($_SESSION['message'])): ?>
            <div class="alert-box success">
                <?= $_SESSION['message']; ?>
            </div>
            <?php $_SESSION['message'] = ""; ?>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="name" class="form-control mb-3" placeholder="Full Name" required>
                </div>

                <div class="col-md-5">
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
                </div>

                <div class="col-md-2">
                    <button name="add" class="btn btn-add">Add</button>
                </div>
            </div>
        </form>

        <!-- TABLE -->
        <table class="table table-bordered text-center">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>

            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" 
                           class="btn-delete"
                           onclick="return confirm('Delete this student?')">
                           Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">❌ No students found</td>
                </tr>
            <?php endif; ?>
        </table>

        <!-- BACK BUTTON -->
        <div class="text-center">
            <a href="admin.php" class="back-btn">
                ⬅ Back to Admin Panel
            </a>
        </div>

    </div>

</div>

</body>
</html>
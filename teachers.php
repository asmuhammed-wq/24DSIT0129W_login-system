<?php
session_start();
include 'config.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// MESSAGE VARIABLE
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "";
}

// DELETE teacher
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id AND role='teacher'");

    $_SESSION['message'] = "🗑️ Teacher deleted successfully!";
    header("Location: teachers.php");
    exit();
}

// ADD teacher
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if ($name !== '' && $email !== '') {

        // CHECK DUPLICATE EMAIL
        $check = $conn->query("SELECT * FROM users WHERE email='$email' AND role='teacher'");

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "⚠️ Teacher already exists!";
        } else {

            $password = password_hash("1234", PASSWORD_BCRYPT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'teacher')");
            $stmt->bind_param("sss", $name, $email, $password);
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = "✅ Teacher added successfully!";
        }

        header("Location: teachers.php");
        exit();
    }
}

// FETCH teachers
$result = $conn->query("SELECT * FROM users WHERE role='teacher'");

// NEW: FETCH TEACHERS WITH COURSES
$teacher_courses = $conn->query("
    SELECT u.id, u.name, u.email, GROUP_CONCAT(c.course_name SEPARATOR ', ') AS courses
    FROM users u
    LEFT JOIN courses c ON u.id = c.teacher_id
    WHERE u.role = 'teacher'
    GROUP BY u.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Teachers</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #eef2f7, #f8fafc);
            font-family: 'Segoe UI';
        }

        .container-box {
            max-width: 1000px;
            margin: 40px auto;
        }

        .card {
            border-radius: 18px;
            border: none;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .form-control {
            border-radius: 10px;
            padding: 10px;
        }

        .btn-custom {
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            font-weight: 500;
        }

        .btn-custom:hover {
            transform: scale(1.03);
            transition: 0.2s;
        }

        table {
            border-radius: 12px;
            overflow: hidden;
        }

        table thead {
            background: #1f2937;
            color: white;
        }

        table tr:hover {
            background: #f1f5f9;
        }

        .delete-btn {
            color: #dc2626;
            font-weight: 500;
            text-decoration: none;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        .alert-box {
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }
        .warning { background: #fef9c3; color: #854d0e; }
    </style>
</head>

<body>

<div class="container-box">

    <!-- TITLE -->
    <h2 class="mb-4 text-center">👨‍🏫 Manage Teachers</h2>

    <!-- MESSAGE -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert-box success">
            <?= $_SESSION['message']; ?>
        </div>
        <?php $_SESSION['message'] = ""; ?>
    <?php endif; ?>

    <!-- ADD TEACHER FORM -->
    <div class="card p-4 mb-4">
        <h5 class="mb-3">Add New Teacher</h5>

        <form method="POST" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="name" class="form-control" placeholder="Teacher Name" required>
            </div>

            <div class="col-md-5">
                <input type="email" name="email" class="form-control" placeholder="Teacher Email" required>
            </div>

            <div class="col-md-2">
                <button type="submit" name="add" class="btn btn-custom w-100">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
        </form>
    </div>

    <!-- ALL TEACHERS -->
    <div class="card p-4">
        <h5 class="mb-3">All Teachers</h5>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="120">Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td>
                        <a href="?delete=<?= $row['id']; ?>" 
                           class="delete-btn"
                           onclick="return confirm('Delete this teacher?')">
                           <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        ❌ No teachers found
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- TEACHERS + COURSES -->
    <div class="card p-4 mt-4">
        <h5 class="mb-3">Teachers & Assigned Courses</h5>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Email</th>
                    <th>Courses</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($teacher_courses->num_rows > 0): ?>
                <?php while ($row = $teacher_courses->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['name']; ?></td>
                    <td><?= $row['email']; ?></td>
                    <td>
                        <?= $row['courses'] ? $row['courses'] : 'No courses assigned'; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        ❌ No data found
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- BACK -->
    <div class="text-center mt-4">
        <a href="admin.php" class="btn btn-secondary">
            ⬅ Back to Admin Panel
        </a>
    </div>

</div>

</body>
</html>
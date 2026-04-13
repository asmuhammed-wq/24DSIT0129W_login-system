<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// DELETE enrollment
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM enrollments WHERE id=$id");
    header("Location: enrollments.php");
    exit();
}

// ENROLL student
if (isset($_POST['enroll'])) {
    $student = $_POST['student_id'];
    $course = $_POST['course_id'];

    $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student, $course);
    
    if ($stmt->execute()) {
        $message = "Student enrolled successfully!";
    } else {
        $message = "Enrollment failed (maybe duplicate).";
    }
}

// FETCH students & courses
$students = $conn->query("SELECT id,name FROM users WHERE role='student'");
$courses = $conn->query("SELECT * FROM courses");

// FETCH enrollments
$enrollments = $conn->query("
SELECT enrollments.id, users.name AS student, courses.course_name 
FROM enrollments
JOIN users ON enrollments.student_id = users.id
JOIN courses ON enrollments.course_id = courses.id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enrollments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, sans-serif; 
            background: linear-gradient(135deg, #e0f2fe, #f8fafc); 
            padding: 30px; 
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h2 { 
            color: #1e293b; 
            margin-bottom: 20px; 
            font-weight: 600;
        }

        form { 
            margin-bottom: 30px; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px;
            align-items: center;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        select { 
            padding: 10px; 
            font-size: 1rem; 
            border-radius: 6px;
        }

        button { 
            background-color: #3b82f6; 
            color: white; 
            border: none; 
            border-radius: 6px; 
            padding: 10px 18px;
            transition: 0.3s; 
            font-weight: 500;
        }

        button:hover { 
            background-color: #2563eb; 
        }

        table { 
            border-collapse: collapse; 
            width: 100%; 
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        th { 
            background: #3b82f6; 
            color: white; 
            text-align: left; 
            padding: 12px;
        }

        td { 
            padding: 12px; 
            border-bottom: 1px solid #e2e8f0; 
        }

        tr:hover {
            background: #f1f5f9;
        }

        tr:last-child td { 
            border-bottom: none; 
        }

        .msg { 
            color: green; 
            font-weight: bold; 
            margin-bottom: 20px; 
        }

        .delete { 
            color: #ef4444; 
            text-decoration: none; 
            font-weight: 500;
        }

        .delete:hover { 
            text-decoration: underline; 
        }
    </style>
</head>

<body>

<div class="container">

<h2>Enroll Student</h2>

<?php if (!empty($message)) echo "<p class='msg'>$message</p>"; ?>

<form method="POST">
    <select name="student_id" class="form-select" required>
        <option value="">Select Student</option>
        <?php while($s = $students->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
        <?php endwhile; ?>
    </select>

    <select name="course_id" class="form-select" required>
        <option value="">Select Course</option>
        <?php while($c = $courses->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= $c['course_name'] ?></option>
        <?php endwhile; ?>
    </select>

    <button name="enroll">Enroll</button>
</form>

<h2>Enrolled Students</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Course</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $enrollments->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['student'] ?></td>
        <td><?= $row['course_name'] ?></td>
        <td>
            <a class="delete" href="?delete=<?= $row['id'] ?>" 
               onclick="return confirm('Remove enrollment?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</div>

</body>
</html>
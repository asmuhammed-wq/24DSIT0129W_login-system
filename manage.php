<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

/* ================= DELETE ================= */
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

if (isset($_GET['delete_course'])) {
    $id = intval($_GET['delete_course']);
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

/* ================= ADD ================= */
if (isset($_POST['add_student'])) {
    $stmt = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
    $pass = password_hash("1234", PASSWORD_BCRYPT);
    $role = "student";
    $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $pass, $role);
    $stmt->execute();
}

if (isset($_POST['add_teacher'])) {
    $stmt = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,?)");
    $pass = password_hash("1234", PASSWORD_BCRYPT);
    $role = "teacher";
    $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $pass, $role);
    $stmt->execute();
}

if (isset($_POST['add_course'])) {
    $stmt = $conn->prepare("INSERT INTO courses(course_name, teacher_id) VALUES(?,?)");
    $stmt->bind_param("si", $_POST['course_name'], $_POST['teacher_id']);
    $stmt->execute();
}

/* ================= FETCH ================= */
$students = $conn->query("SELECT * FROM users WHERE role='student'");
$teachers = $conn->query("SELECT * FROM users WHERE role='teacher'");
$courses = $conn->query("
    SELECT courses.*, users.name AS teacher 
    FROM courses 
    LEFT JOIN users ON courses.teacher_id = users.id
");
?>

<!DOCTYPE html>
<html>
<head>
<title>System Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>⚙️ SYSTEM MANAGEMENT</h2>

<div class="row">

<!-- ADD STUDENT -->
<div class="col-md-4">
<div class="card p-3 shadow">
<h5>Add Student</h5>
<form method="POST">
<input name="name" class="form-control mb-2" placeholder="Name" required>
<input name="email" class="form-control mb-2" placeholder="Email" required>
<button name="add_student" class="btn btn-primary">Add</button>
</form>
</div>
</div>

<!-- ADD TEACHER -->
<div class="col-md-4">
<div class="card p-3 shadow">
<h5>Add Teacher</h5>
<form method="POST">
<input name="name" class="form-control mb-2" placeholder="Name" required>
<input name="email" class="form-control mb-2" placeholder="Email" required>
<button name="add_teacher" class="btn btn-success">Add</button>
</form>
</div>
</div>

<!-- ADD COURSE -->
<div class="col-md-4">
<div class="card p-3 shadow">
<h5>Add Course</h5>
<form method="POST">
<input name="course_name" class="form-control mb-2" placeholder="Course Name" required>

<select name="teacher_id" class="form-control mb-2">
<?php while($t = $teachers->fetch_assoc()): ?>
<option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
<?php endwhile; ?>
</select>

<button name="add_course" class="btn btn-warning">Add</button>
</form>
</div>
</div>

</div>

<hr>

<!-- STUDENTS TABLE -->
<h4>Students</h4>
<table class="table table-bordered">
<tr><th>Name</th><th>Email</th><th>Action</th></tr>
<?php while($s = $students->fetch_assoc()): ?>
<tr>
<td><?= $s['name'] ?></td>
<td><?= $s['email'] ?></td>
<td>
<a href="?delete_user=<?= $s['id'] ?>" onclick="return confirm('Delete?')" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<!-- TEACHERS TABLE -->
<h4>Teachers</h4>
<table class="table table-bordered">
<tr><th>Name</th><th>Email</th><th>Action</th></tr>
<?php 
$teachers = $conn->query("SELECT * FROM users WHERE role='teacher'");
while($t = $teachers->fetch_assoc()): ?>
<tr>
<td><?= $t['name'] ?></td>
<td><?= $t['email'] ?></td>
<td>
<a href="?delete_user=<?= $t['id'] ?>" onclick="return confirm('Delete?')" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<!-- COURSES TABLE -->
<h4>Courses</h4>
<table class="table table-bordered">
<tr><th>Course</th><th>Teacher</th><th>Action</th></tr>
<?php while($c = $courses->fetch_assoc()): ?>
<tr>
<td><?= $c['course_name'] ?></td>
<td><?= $c['teacher'] ?? 'None' ?></td>
<td>
<a href="?delete_course=<?= $c['id'] ?>" onclick="return confirm('Delete?')" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<a href="dashboard.php" class="btn btn-dark">⬅ Back</a>

</body>
</html>
<?php
session_start();
include 'config.php';

// STORE MESSAGE IN SESSION (for persistence after reload)
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = "";
}

// ADD COURSE
if (isset($_POST['add'])) {
    $name = $_POST['course_name'];
    $code = $_POST['course_code'];

    if ($conn->query("INSERT INTO courses (course_name,course_code)
                      VALUES ('$name','$code')")) {
        $_SESSION['message'] = "✅ Course added successfully!";
    } else {
        $_SESSION['message'] = "❌ Error adding course!";
    }

    header("Location: courses.php");
    exit();
}

// DELETE
if (isset($_GET['delete'])) {
    if ($conn->query("DELETE FROM courses WHERE id=".$_GET['delete'])) {
        $_SESSION['message'] = "🗑️ Course deleted successfully!";
    } else {
        $_SESSION['message'] = "❌ Error deleting course!";
    }

    header("Location: courses.php");
    exit();
}

// UPDATE COURSE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['course_name'];
    $code = $_POST['course_code'];

    if ($conn->query("UPDATE courses SET course_name='$name', course_code='$code' WHERE id=$id")) {
        $_SESSION['message'] = "✏️ Course updated successfully!";
    } else {
        $_SESSION['message'] = "❌ Error updating course!";
    }

    header("Location: courses.php");
    exit();
}

// ASSIGN TEACHER
if (isset($_POST['assign'])) {
    $course_id = $_POST['course_id'];
    $teacher_id = $_POST['teacher_id'];

    if ($conn->query("UPDATE courses SET teacher_id=$teacher_id WHERE id=$course_id")) {
        $_SESSION['message'] = "👨‍🏫 Teacher assigned successfully!";
    } else {
        $_SESSION['message'] = "❌ Error assigning teacher!";
    }

    header("Location: courses.php");
    exit();
}

// SEARCH
$search = $_GET['search'] ?? '';

if ($search) {
    $result = $conn->query("SELECT * FROM courses WHERE course_name LIKE '%$search%'");
} else {
    $result = $conn->query("SELECT * FROM courses");
}

// FETCH TEACHERS
$teachers = $conn->query("SELECT * FROM users WHERE role='teacher'");

// COURSE + TEACHER VIEW
$course_teacher = $conn->query("
    SELECT c.course_name, c.course_code, u.name AS teacher
    FROM courses c
    LEFT JOIN users u ON c.teacher_id = u.id
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Courses System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background:#f4f6f9; font-family:'Segoe UI'; }
.card { border-radius:15px; }

.back-btn {
    margin-top: 20px;
    padding: 10px 20px;
    border-radius: 10px;
    background: #6b7280;
    color: white;
    text-decoration: none;
    display: inline-block;
}
.back-btn:hover {
    background: #4b5563;
}
</style>
</head>

<body>

<div class="container mt-5">

<h2 class="mb-4">📚 Courses Management</h2>

<!-- ALERT MESSAGE -->
<?php if (!empty($_SESSION['message'])): ?>
    <div id="alertBox" class="alert alert-success">
        <?= $_SESSION['message']; ?>
    </div>
    <?php $_SESSION['message'] = ""; ?>
<?php endif; ?>

<!-- SEARCH -->
<form method="GET" class="mb-3">
    <input type="text" name="search" class="form-control" placeholder="Search course...">
</form>

<!-- ADD COURSE -->
<div class="card p-3 mb-4">
<h5>Add Course</h5>
<form method="POST" class="row">
    <div class="col-md-5">
        <input name="course_name" class="form-control" placeholder="Course Name" required>
    </div>
    <div class="col-md-5">
        <input name="course_code" class="form-control" placeholder="Course Code" required>
    </div>
    <div class="col-md-2">
        <button name="add" class="btn btn-success w-100">Add</button>
    </div>
</form>
</div>

<!-- COURSE TABLE -->
<div class="card p-3">
<table class="table table-bordered">
<tr>
<th>ID</th><th>Name</th><th>Code</th><th>Actions</th>
</tr>

<?php if ($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<form method="POST">

<td><?= $row['id'] ?></td>

<td>
<input type="text" name="course_name" value="<?= $row['course_name'] ?>" class="form-control">
</td>

<td>
<input type="text" name="course_code" value="<?= $row['course_code'] ?>" class="form-control">
</td>

<td>
<input type="hidden" name="id" value="<?= $row['id'] ?>">

<button name="update" class="btn btn-primary btn-sm">Update</button>
<a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
   onclick="return confirm('Are you sure you want to delete this course?')">
   Delete
</a>

</td>

</form>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="4" class="text-center text-danger">❌ No courses found</td></tr>
<?php endif; ?>

</table>
</div>

<!-- ASSIGN TEACHER -->
<div class="card p-3 mt-4">
<h5>Assign Teacher</h5>

<form method="POST" class="row">

<div class="col-md-5">
<select name="course_id" class="form-control">
<?php 
$c = $conn->query("SELECT * FROM courses");
while($course = $c->fetch_assoc()):
?>
<option value="<?= $course['id'] ?>"><?= $course['course_name'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-5">
<select name="teacher_id" class="form-control">
<?php while($t = $teachers->fetch_assoc()): ?>
<option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
<?php endwhile; ?>
</select>
</div>

<div class="col-md-2">
<button name="assign" class="btn btn-warning w-100">Assign</button>
</div>

</form>
</div>

<!-- COURSE + TEACHER VIEW -->
<div class="card p-3 mt-4">
<h5>Courses & Teachers</h5>

<table class="table table-striped">
<tr><th>Course</th><th>Code</th><th>Teacher</th></tr>

<?php while($ct = $course_teacher->fetch_assoc()): ?>
<tr>
<td><?= $ct['course_name'] ?></td>
<td><?= $ct['course_code'] ?></td>
<td><?= $ct['teacher'] ?? 'Not Assigned' ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

<!-- BACK BUTTON -->
<div class="text-center">
    <a href="admin.php" class="back-btn">
        ⬅ Back to Admin Panel
    </a>
</div>

</div>

<!-- AUTO HIDE ALERT -->
<script>
setTimeout(() => {
    let alert = document.getElementById('alertBox');
    if (alert) {
        alert.style.display = 'none';
    }
}, 3000);
</script>

</body>
</html>
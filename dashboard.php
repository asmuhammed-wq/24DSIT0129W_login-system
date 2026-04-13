<?php
session_start();
include 'config.php';

// Restrict access to admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// COUNT DATA (UNCHANGED)
$students = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'")->fetch_assoc()['total'];
$teachers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='teacher'")->fetch_assoc()['total'];
$courses = $conn->query("SELECT COUNT(*) as total FROM courses")->fetch_assoc()['total'];
$enrollments = $conn->query("SELECT COUNT(*) as total FROM enrollments")->fetch_assoc()['total'];

// RECENT ENROLLMENTS (UNCHANGED)
$recent = $conn->query("
    SELECT users.name, courses.course_name
    FROM enrollments
    JOIN users ON enrollments.student_id = users.id
    JOIN courses ON enrollments.course_id = courses.id
    ORDER BY enrollments.id DESC LIMIT 5
");

// TEACHERS & COURSES (UNCHANGED)
$teachers_courses = $conn->query("
    SELECT u.name AS teacher, c.course_name
    FROM users u
    LEFT JOIN courses c ON u.id = c.teacher_id
    WHERE u.role='teacher'
    ORDER BY u.name
");

// DYNAMIC CONTENT (UNCHANGED)
$content = $conn->query("SELECT * FROM tbl_content ORDER BY id DESC");

// 🔥 NEW: DYNAMIC STATS ARRAY
$stats = [
    ["title" => "Students", "value" => $students, "class" => "students"],
    ["title" => "Teachers", "value" => $teachers, "class" => "teachers"],
    ["title" => "Courses", "value" => $courses, "class" => "courses"],
    ["title" => "Enrollments", "value" => $enrollments, "class" => "enrollments"]
];

// 🔥 NEW: SIDEBAR MENU
$menu = [
    ["Dashboard", "dashboard.php", "fa-home"],
    ["Students", "students.php", "fa-users"],
    ["Teachers", "teachers.php", "fa-chalkboard-teacher"],
    ["Courses", "courses.php", "fa-book"],
    ["Enrollments", "enrollments.php", "fa-list"],
    ["Logout", "logout.php", "fa-sign-out-alt"]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background: linear-gradient(135deg, #eef2f7, #f8fafc);
            font-family: 'Segoe UI';
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #0f172a, #1e293b);
            color: white;
            padding: 25px;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            padding: 12px;
            margin: 10px 0;
            border-radius: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #3b82f6;
            color: white;
        }

        .topbar {
            margin-left: 240px;
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .content {
            margin-left: 240px;
            padding: 30px;
        }

        .card {
            border-radius: 20px;
            border: none;
            background: white;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .students { border-left: 6px solid #3b82f6; }
        .teachers { border-left: 6px solid #8b5cf6; }
        .courses { border-left: 6px solid #10b981; }
        .enrollments { border-left: 6px solid #f59e0b; }

        .notify {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            font-size: 12px;
        }

        .dark-mode {
            background: #0f172a;
            color: white;
        }

        .dark-mode .topbar,
        .dark-mode .card {
            background: #1e293b;
            color: white;
        }
    </style>
</head>

<body id="body">

<!-- SIDEBAR -->
<div class="sidebar">
    <h3><i class="fas fa-user-shield"></i> Admin</h3>

    <?php foreach($menu as $m): ?>
        <a href="<?= $m[1] ?>">
            <i class="fas <?= $m[2] ?>"></i> <?= $m[0] ?>
        </a>
    <?php endforeach; ?>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <h5>📊 Dashboard Overview</h5>
    <div>
        🔔 <span class="notify">3</span>
        <span style="margin-left:20px; cursor:pointer;" onclick="toggleMode()">🌙</span>
        <span style="margin-left:20px;">👤 Admin</span>
    </div>
</div>

<!-- CONTENT -->
<div class="content">

<!-- ✅ DYNAMIC CARDS -->
<div class="row g-4">
<?php foreach($stats as $s): ?>
    <div class="col-md-3">
        <div class="card p-4 shadow <?= $s['class'] ?>">
            <h6>Total <?= $s['title'] ?></h6>
            <h2><?= $s['value'] ?></h2>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- CHART -->
<div class="card mt-5 p-4 shadow">
    <h5>📈 System Statistics</h5>
    <canvas id="chart"></canvas>
</div>

<!-- RECENT ENROLLMENTS -->
<div class="card mt-5 p-4 shadow">
    <h5>🧾 Recent Enrollments</h5>
    <table class="table">
        <tr><th>Student</th><th>Course</th></tr>

        <?php if ($recent->num_rows > 0): ?>
            <?php while($r = $recent->fetch_assoc()): ?>
            <tr>
                <td><?= $r['name'] ?></td>
                <td><?= $r['course_name'] ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">❌ No records found</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- TEACHERS & COURSES (GROUPED) -->
<div class="card mt-5 p-4 shadow">
    <h5>👨‍🏫 Teachers & Their Courses</h5>
    <table class="table">
        <tr><th>Teacher</th><th>Course</th></tr>

        <?php if ($teachers_courses->num_rows > 0): ?>
            <?php 
            $currentTeacher = "";
            while($t = $teachers_courses->fetch_assoc()): 
            ?>

            <?php if ($currentTeacher != $t['teacher']): ?>
            <tr style="background:#e2e8f0;">
                <td colspan="2"><strong><?= $t['teacher'] ?></strong></td>
            </tr>
            <?php $currentTeacher = $t['teacher']; ?>
            <?php endif; ?>

            <tr>
                <td></td>
                <td><?= $t['course_name'] ?? 'No course assigned' ?></td>
            </tr>

            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="2">❌ No records found</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- 🔥 DYNAMIC CONTENT -->
<div class="card mt-5 p-4 shadow">
    <h5>📢 School Updates</h5>

    <div class="row">
    <?php if ($content->num_rows > 0): ?>
        <?php while($row = $content->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card p-3 shadow-sm mb-3">
                <img src="<?= $row['image_url'] ?>" 
                     style="width:100%; height:150px; object-fit:cover; border-radius:10px;">

                <h5 class="mt-2"><?= $row['title'] ?></h5>
                <p><?= $row['description'] ?></p>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color:red;">❌ No records found</p>
    <?php endif; ?>
    </div>
</div>

</div>

<!-- SCRIPTS -->
<script>
function toggleMode() {
    document.getElementById("body").classList.toggle("dark-mode");
}

// 🔥 DYNAMIC CHART
let labels = <?= json_encode(array_column($stats, 'title')) ?>;
let values = <?= json_encode(array_column($stats, 'value')) ?>;

new Chart(document.getElementById("chart"), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'System Data',
            data: values
        }]
    }
});
</script>

</body>
</html>
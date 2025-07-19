<?php
require 'auth.php';
require_login();
require 'db.php';

$user_id = $_SESSION['user_id'];

// Handle add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $attachment = null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $target = 'uploads/' . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $target);
        $attachment = $target;
    }
    if ($title) {
        $stmt = $pdo->prepare('INSERT INTO courses (user_id, title, description, attachment) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user_id, $title, $desc, $attachment]);
    }
    header('Location: courses.php');
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM courses WHERE id = ? AND user_id = ?');
    $stmt->execute([$_GET['delete'], $user_id]);
    header('Location: courses.php');
    exit;
}

// Fetch courses
$stmt = $pdo->prepare('SELECT * FROM courses WHERE user_id = ?');
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Courses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="d-flex justify-content-center align-items-center min-vh-100">
  <div class="container text-center">
    <h2>My Courses</h2>
    <a href="index.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <form method="post" enctype="multipart/form-data" class="row g-3 mb-4 mx-auto" style="max-width:400px;">
        <div class="col-12 mb-3"><input type="text" name="title" class="form-control" placeholder="Course Title" required></div>
        <div class="col-12 mb-3"><input type="text" name="description" class="form-control" placeholder="Description"></div>
        <div class="col-12 mb-3"><input type="file" name="attachment" class="form-control"></div>
        <div class="col-12 mb-3"><button type="submit" name="add" class="btn btn-primary w-100">Add Course</button></div>
    </form>
    <table class="table table-bordered bg-white mb-4">
        <thead>
            <tr>
                <th>Title</th><th>Description</th><th>Attachment</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($courses as $course): ?>
            <tr>
                <td><?= htmlspecialchars($course['title']) ?></td>
                <td><?= htmlspecialchars($course['description']) ?></td>
                <td><?php if ($course['attachment']) { echo '<a href="' . htmlspecialchars($course['attachment']) . '" target="_blank">Download</a>'; } ?></td>
                <td>
                    <a href="?delete=<?= $course['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
  </div>
</div>
</body>
</html> 
<?php
require 'auth.php';
require_login();
if ($_SESSION['role'] !== 'student') {
    header('Location: teacher_dashboard.php');
    exit;
}
require 'db.php';

// Fetch all tasks
$stmt = $pdo->prepare('SELECT t.*, u.username AS teacher_name FROM tasks t JOIN users u ON t.teacher_id = u.id ORDER BY t.due_date DESC');
$stmt->execute();
$tasks = $stmt->fetchAll();

// Fetch student's submissions
$stmt = $pdo->prepare('SELECT * FROM submissions WHERE student_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$submissions = $stmt->fetchAll();
$submission_map = [];
foreach ($submissions as $sub) {
    $submission_map[$sub['task_id']] = $sub;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <a href="index.php" class="btn btn-outline-primary mb-3">&larr; Back to Home</a>
    <h1>Student Dashboard</h1>
    <p>Welcome, student!</p>
    <hr>
    <h3>Assigned Tasks</h3>
    <table class="table table-bordered table-striped">
        <thead><tr><th>Title</th><th>Description</th><th>Due Date</th><th>Teacher</th><th>Status</th><th>Mark</th><th>Feedback</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($tasks as $task): 
            $sub = isset($submission_map[$task['id']]) ? $submission_map[$task['id']] : null;
        ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['description']) ?></td>
                <td><?= htmlspecialchars($task['due_date']) ?></td>
                <td><?= htmlspecialchars($task['teacher_name']) ?></td>
                <td><?= $sub ? htmlspecialchars($sub['status']) : 'Not submitted' ?></td>
                <td><?= $sub && !is_null($sub['mark']) ? htmlspecialchars($sub['mark']) : '-' ?></td>
                <td><?= $sub ? htmlspecialchars($sub['feedback']) : '-' ?></td>
                <td>
                    <a href="view_task.php?task_id=<?= $task['id'] ?>" class="btn btn-info btn-sm mb-1">View Task</a>
                    <?php if (!$sub): ?>
                        <a href="submit_task.php?task_id=<?= $task['id'] ?>" class="btn btn-success btn-sm mb-1">Submit</a>
                    <?php else: ?>
                        <a href="submit_task.php?task_id=<?= $task['id'] ?>" class="btn btn-secondary btn-sm mb-1">Edit</a>
                        <a href="uploads/<?= htmlspecialchars($sub['submission_file']) ?>" class="btn btn-primary btn-sm mb-1" target="_blank">Download</a>
                    <?php endif; ?>
                    <a href="report_problem.php?task_id=<?= $task['id'] ?>" class="btn btn-warning btn-sm">Report</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html> 
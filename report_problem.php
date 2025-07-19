<?php
require 'auth.php';
require_login();
if ($_SESSION['role'] !== 'student') {
    header('Location: teacher_dashboard.php');
    exit;
}
require 'db.php';

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
if (!$task_id) {
    echo '<div class="alert alert-danger">Invalid task ID.</div>';
    exit;
}

// Fetch task info
$stmt = $pdo->prepare('SELECT t.*, u.username AS teacher_name, u.id AS teacher_id FROM tasks t JOIN users u ON t.teacher_id = u.id WHERE t.id = ?');
$stmt->execute([$task_id]);
$task = $stmt->fetch();
if (!$task) {
    echo '<div class="alert alert-danger">Task not found.</div>';
    exit;
}

// Fetch student's latest submission for this task
$stmt = $pdo->prepare('SELECT * FROM submissions WHERE task_id = ? AND student_id = ? ORDER BY submitted_at DESC LIMIT 1');
$stmt->execute([$task_id, $_SESSION['user_id']]);
$submission = $stmt->fetch();
$submission_id = $submission ? $submission['id'] : null;

// Handle report submission
$report_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($message) {
        $stmt = $pdo->prepare('INSERT INTO reports (submission_id, student_id, teacher_id, message, status, created_at) VALUES (?, ?, ?, ?, "open", NOW())');
        $stmt->execute([$submission_id, $_SESSION['user_id'], $task['teacher_id'], $message]);
        header('Location: report_problem.php?task_id=' . $task_id);
        exit;
    } else {
        $report_error = 'Please enter a message.';
    }
}

// Fetch previous reports for this task by this student
$stmt = $pdo->prepare('SELECT * FROM reports WHERE student_id = ? AND teacher_id = ? AND (submission_id = ? OR (? IS NULL AND submission_id IS NULL)) ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id'], $task['teacher_id'], $submission_id, $submission_id]);
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Problem - <?= htmlspecialchars($task['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <h2>Report a Problem: <?= htmlspecialchars($task['title']) ?></h2>
    <a href="student_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <?php if ($report_error): ?><div class="alert alert-danger"><?= htmlspecialchars($report_error) ?></div><?php endif; ?>
    <form method="post" style="max-width:600px;">
        <div class="mb-3">
            <label for="message" class="form-label">Describe the problem you are facing:</label>
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-warning">Submit Report</button>
    </form>
    <hr>
    <h4>Your Previous Reports for this Task</h4>
    <?php if (empty($reports)): ?>
        <div class="alert alert-info">No previous reports for this task.</div>
    <?php else: ?>
        <ul class="list-group">
        <?php foreach ($reports as $rep): ?>
            <li class="list-group-item">
                <b>Message:</b> <?= nl2br(htmlspecialchars($rep['message'])) ?><br>
                <b>Status:</b> <?= htmlspecialchars($rep['status']) ?><br>
                <b>Teacher Response:</b> <?= $rep['response'] ? nl2br(htmlspecialchars($rep['response'])) : '<span class="text-muted">No response yet</span>' ?><br>
                <b>Reported at:</b> <?= htmlspecialchars($rep['created_at']) ?>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html> 
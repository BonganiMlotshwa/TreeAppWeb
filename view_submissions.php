<?php
require 'auth.php';
require_login();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: student_dashboard.php');
    exit;
}
require 'db.php';

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
if (!$task_id) {
    echo '<div class="alert alert-danger">Invalid task ID.</div>';
    exit;
}

// Handle grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submission_id'])) {
    $submission_id = intval($_POST['submission_id']);
    $mark = intval($_POST['mark']);
    $feedback = trim($_POST['feedback']);
    $stmt = $pdo->prepare('UPDATE submissions SET mark = ?, feedback = ?, status = "graded" WHERE id = ?');
    $stmt->execute([$mark, $feedback, $submission_id]);
}

// Fetch task info
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ? AND teacher_id = ?');
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch();
if (!$task) {
    echo '<div class="alert alert-danger">Task not found or access denied.</div>';
    exit;
}

// Fetch submissions
$stmt = $pdo->prepare('SELECT s.*, u.username FROM submissions s JOIN users u ON s.student_id = u.id WHERE s.task_id = ?');
$stmt->execute([$task_id]);
$submissions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Submissions - <?= htmlspecialchars($task['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <h2>Submissions for: <?= htmlspecialchars($task['title']) ?></h2>
    <a href="teacher_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <?php if (empty($submissions)): ?>
        <div class="alert alert-info">No submissions yet.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Student</th><th>Submission</th><th>Submitted At</th><th>Mark</th><th>Feedback</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($submissions as $sub): ?>
            <tr>
                <td><?= htmlspecialchars($sub['username']) ?></td>
                <td>
                    <?php if ($sub['submission_file']): ?>
                        <a href="uploads/<?= htmlspecialchars($sub['submission_file']) ?>" target="_blank">Download</a><br>
                    <?php endif; ?>
                    <?= nl2br(htmlspecialchars($sub['submission_text'])) ?>
                </td>
                <td><?= htmlspecialchars($sub['submitted_at']) ?></td>
                <td><?= is_null($sub['mark']) ? '-' : htmlspecialchars($sub['mark']) ?></td>
                <td><?= htmlspecialchars($sub['feedback']) ?></td>
                <td>
                    <form method="post" style="min-width:180px;">
                        <input type="hidden" name="submission_id" value="<?= $sub['id'] ?>">
                        <input type="number" name="mark" class="form-control mb-1" placeholder="Mark" value="<?= htmlspecialchars($sub['mark']) ?>" min="0" max="100" required>
                        <textarea name="feedback" class="form-control mb-1" placeholder="Feedback"><?= htmlspecialchars($sub['feedback']) ?></textarea>
                        <button type="submit" class="btn btn-success btn-sm">Save</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html> 
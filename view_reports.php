<?php
require 'auth.php';
require_login();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: student_dashboard.php');
    exit;
}
require 'db.php';

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'])) {
    $report_id = intval($_POST['report_id']);
    $response = trim($_POST['response']);
    $stmt = $pdo->prepare('UPDATE reports SET response = ?, status = "closed" WHERE id = ? AND teacher_id = ?');
    $stmt->execute([$response, $report_id, $_SESSION['user_id']]);
}

// Fetch all reports for this teacher
$stmt = $pdo->prepare('SELECT r.*, u.username AS student_name, t.title AS task_title, s.id AS submission_id FROM reports r
    JOIN users u ON r.student_id = u.id
    LEFT JOIN submissions s ON r.submission_id = s.id
    LEFT JOIN tasks t ON s.task_id = t.id
    WHERE r.teacher_id = ? ORDER BY r.created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <h2>Student Problem Reports</h2>
    <a href="teacher_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <?php if (empty($reports)): ?>
        <div class="alert alert-info">No reports found.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Student</th><th>Task</th><th>Message</th><th>Status</th><th>Response</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($reports as $rep): ?>
            <tr>
                <td><?= htmlspecialchars($rep['student_name']) ?></td>
                <td><?= htmlspecialchars($rep['task_title']) ?></td>
                <td><?= nl2br(htmlspecialchars($rep['message'])) ?></td>
                <td><?= htmlspecialchars($rep['status']) ?></td>
                <td><?= nl2br(htmlspecialchars($rep['response'])) ?></td>
                <td>
                    <?php if ($rep['status'] === 'open'): ?>
                    <form method="post" style="min-width:180px;">
                        <input type="hidden" name="report_id" value="<?= $rep['id'] ?>">
                        <textarea name="response" class="form-control mb-1" placeholder="Response" required></textarea>
                        <button type="submit" class="btn btn-success btn-sm">Send Response</button>
                    </form>
                    <?php else: ?>
                        <span class="text-success">Closed</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
</body>
</html> 
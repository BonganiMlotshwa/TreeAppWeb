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

// Fetch task info with teacher details
$stmt = $pdo->prepare('SELECT t.*, u.username AS teacher_name FROM tasks t JOIN users u ON t.teacher_id = u.id WHERE t.id = ?');
$stmt->execute([$task_id]);
$task = $stmt->fetch();
if (!$task) {
    echo '<div class="alert alert-danger">Task not found.</div>';
    exit;
}

// Fetch student's submission for this task
$stmt = $pdo->prepare('SELECT * FROM submissions WHERE task_id = ? AND student_id = ?');
$stmt->execute([$task_id, $_SESSION['user_id']]);
$submission = $stmt->fetch();

// Check if task has any attachments
$task_attachment = $task['attachment'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Task - <?= htmlspecialchars($task['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h2><?= htmlspecialchars($task['title']) ?></h2>
            <a href="student_dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Task Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Teacher:</strong></div>
                        <div class="col-md-8"><?= htmlspecialchars($task['teacher_name']) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Due Date:</strong></div>
                        <div class="col-md-8">
                            <?= htmlspecialchars($task['due_date']) ?>
                            <?php 
                            $due_date = new DateTime($task['due_date']);
                            $today = new DateTime();
                            if ($due_date < $today): ?>
                                <span class="badge bg-danger ms-2">Overdue</span>
                            <?php elseif ($due_date->diff($today)->days <= 3): ?>
                                <span class="badge bg-warning ms-2">Due Soon</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Description:</strong></div>
                        <div class="col-md-8"><?= nl2br(htmlspecialchars($task['description'])) ?></div>
                    </div>
                    <?php if ($task_attachment): ?>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Task Files:</strong></div>
                        <div class="col-md-8">
                            <a href="uploads/<?= htmlspecialchars($task_attachment) ?>" class="btn btn-primary btn-sm" target="_blank">
                                <i class="fas fa-download"></i> Download Task File
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submission Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Your Submission</h5>
                </div>
                <div class="card-body">
                    <?php if ($submission): ?>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Status:</strong></div>
                            <div class="col-md-8">
                                <?php if ($submission['status'] === 'graded'): ?>
                                    <span class="badge bg-success">Graded</span>
                                <?php else: ?>
                                    <span class="badge bg-info">Submitted</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Submitted:</strong></div>
                            <div class="col-md-8"><?= htmlspecialchars($submission['submitted_at']) ?></div>
                        </div>
                        <?php if (!is_null($submission['mark'])): ?>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Mark:</strong></div>
                            <div class="col-md-8">
                                <span class="badge bg-primary fs-6"><?= htmlspecialchars($submission['mark']) ?>/100</span>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if ($submission['feedback']): ?>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Feedback:</strong></div>
                            <div class="col-md-8"><?= nl2br(htmlspecialchars($submission['feedback'])) ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($submission['submission_file']): ?>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Your File:</strong></div>
                            <div class="col-md-8">
                                <a href="uploads/<?= htmlspecialchars($submission['submission_file']) ?>" class="btn btn-success btn-sm" target="_blank">
                                    <i class="fas fa-download"></i> Download Your Submission
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Your Answer:</strong></div>
                            <div class="col-md-8">
                                <div class="border rounded p-3 bg-light">
                                    <?= nl2br(htmlspecialchars($submission['submission_text'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <strong>No submission yet.</strong> You haven't submitted this task.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <?php if (!$submission): ?>
                        <a href="submit_task.php?task_id=<?= $task['id'] ?>" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-upload"></i> Submit Task
                        </a>
                    <?php else: ?>
                        <a href="submit_task.php?task_id=<?= $task['id'] ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-edit"></i> Edit Submission
                        </a>
                    <?php endif; ?>
                    
                    <a href="report_problem.php?task_id=<?= $task['id'] ?>" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-exclamation-triangle"></i> Report Problem
                    </a>
                    
                    <a href="student_dashboard.php" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</body>
</html> 
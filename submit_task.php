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
$stmt = $pdo->prepare('SELECT t.*, u.username AS teacher_name FROM tasks t JOIN users u ON t.teacher_id = u.id WHERE t.id = ?');
$stmt->execute([$task_id]);
$task = $stmt->fetch();
if (!$task) {
    echo '<div class="alert alert-danger">Task not found.</div>';
    exit;
}

// Fetch existing submission
$stmt = $pdo->prepare('SELECT * FROM submissions WHERE task_id = ? AND student_id = ?');
$stmt->execute([$task_id, $_SESSION['user_id']]);
$submission = $stmt->fetch();

    $submit_error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $submission_text = trim($_POST['submission_text']);
        $file_name = $submission ? $submission['submission_file'] : null;
        
        // Handle file upload with better validation
        if (isset($_FILES['submission_file']) && $_FILES['submission_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['submission_file'];
            $allowed_types = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed_types)) {
                $file_name = uniqid('sub_' . $_SESSION['user_id'] . '_') . '.' . $ext;
                $upload_path = 'uploads/' . $file_name;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // File uploaded successfully
                } else {
                    $submit_error = 'Failed to upload file. Please try again.';
                }
            } else {
                $submit_error = 'Invalid file type. Allowed: ' . implode(', ', $allowed_types);
            }
        }
        
        if (!$submit_error) {
            if ($submission) {
                // Update existing submission
                $stmt = $pdo->prepare('UPDATE submissions SET submission_text = ?, submission_file = ?, submitted_at = NOW(), status = "submitted" WHERE id = ?');
                $stmt->execute([$submission_text, $file_name, $submission['id']]);
            } else {
                // Insert new submission
                $stmt = $pdo->prepare('INSERT INTO submissions (task_id, student_id, submission_text, submission_file, submitted_at, status) VALUES (?, ?, ?, ?, NOW(), "submitted")');
                $stmt->execute([$task_id, $_SESSION['user_id'], $submission_text, $file_name]);
            }
            header('Location: view_task.php?task_id=' . $task_id);
            exit;
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Submit Task - <?= htmlspecialchars($task['title']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/darkmode.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkmode.js"></script>
</head>
<body class="bg-light">

<button class="dark-mode-toggle" title="Toggle dark mode"></button>
<div class="container mt-5">
    <h2>Submit Task: <?= htmlspecialchars($task['title']) ?></h2>
    <a href="view_task.php?task_id=<?= $task['id'] ?>" class="btn btn-secondary mb-3">&larr; Back to Task</a>
    
    <?php if ($submit_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($submit_error) ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Submit Your Work</h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="submission_text" class="form-label">
                                <strong>Your Answer (Text):</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="submission_text" id="submission_text" class="form-control" rows="8" 
                                placeholder="Enter your detailed answer here..." required><?= $submission ? htmlspecialchars($submission['submission_text']) : '' ?></textarea>
                            <div class="form-text">Provide a detailed written response to the task.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="submission_file" class="form-label">
                                <strong>Upload Supporting Files (Optional):</strong>
                            </label>
                            <input type="file" name="submission_file" id="submission_file" class="form-control">
                            <div class="form-text">
                                Allowed file types: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP, RAR<br>
                                Maximum file size: 10MB
                            </div>
                            <?php if ($submission && $submission['submission_file']): ?>
                                <div class="mt-2 alert alert-info">
                                    <strong>Current file:</strong> 
                                    <a href="uploads/<?= htmlspecialchars($submission['submission_file']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download Current File
                                    </a>
                                    <small class="d-block mt-1">Uploading a new file will replace this one.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-upload"></i> 
                                <?= $submission ? 'Update Submission' : 'Submit Task' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Task Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Teacher:</strong> <?= htmlspecialchars($task['teacher_name']) ?></p>
                    <p><strong>Due Date:</strong> <?= htmlspecialchars($task['due_date']) ?></p>
                    <p><strong>Status:</strong> 
                        <?php if ($submission): ?>
                            <span class="badge bg-info">Submitted</span>
                        <?php else: ?>
                            <span class="badge bg-warning">Not Submitted</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Help</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-info-circle text-info"></i> Provide detailed answers</li>
                        <li><i class="fas fa-file-upload text-success"></i> Upload supporting documents</li>
                        <li><i class="fas fa-edit text-warning"></i> You can edit your submission</li>
                        <li><i class="fas fa-question-circle text-primary"></i> Report problems if needed</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html> 